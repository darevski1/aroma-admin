<?php

namespace Tests\Feature\Chatbot;

use App\Models\Chatbot\ChatbotCategory;
use App\Models\Chatbot\ChatbotProviderSetting;
use App\Services\Chatbot\ChatbotManager;
use App\Services\Chatbot\Support\ChatbotResponse;
use App\Services\Chatbot\Support\TextNormalizer;

class FaqEngineTest extends ChatbotTestCase
{
    private function ask(string $question): ChatbotResponse
    {
        return app(ChatbotManager::class)->respond($question);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->faq('Кое е вашето работно време?', 'Пон - Пет, 09:00 - 17:00.', ['работно време', 'отворено']);
        $this->faq('What are your working hours?', 'Monday to Friday, 09:00 – 17:00.', ['opening hours', 'open'], 'en');
        $this->faq('Дали сервисирате принтери?', 'Да, сервисираме и продаваме принтери.', ['принтер', 'сервис'], 'mk');
        $this->faq('How can I contact you?', 'Call +389 71 354 822.', ['phone', 'email'], 'en');
    }

    public function test_initial_configuration_is_free_faq_engine(): void
    {
        $settings = $this->settings();
        $this->assertTrue($settings->enabled);
        $this->assertSame('faq', $settings->provider);
        $this->assertTrue(ChatbotProviderSetting::for('faq')->is_enabled);
        $this->assertFalse(ChatbotProviderSetting::for('ollama')->is_enabled);
        $this->assertFalse(ChatbotProviderSetting::for('openai')->is_enabled);
        $this->assertNotEmpty($settings->welcome_message);
        $this->assertNotEmpty($settings->fallback_message);
    }

    public function test_exact_question_matches(): void
    {
        $r = $this->ask('What are your working hours?');

        $this->assertSame(ChatbotResponse::TYPE_FAQ, $r->type);
        $this->assertTrue($r->answered);
        $this->assertSame('Monday to Friday, 09:00 – 17:00.', $r->message);
        $this->assertEquals(1.0, $r->metadata['score']);
    }

    public function test_case_whitespace_and_punctuation_are_ignored(): void
    {
        $r = $this->ask("   WHAT are   your working-hours ?!! ");

        $this->assertSame('Monday to Friday, 09:00 – 17:00.', $r->message);
    }

    public function test_partial_question_matches(): void
    {
        $this->assertSame('Да, сервисираме и продаваме принтери.', $this->ask('сервисирате принтери')->message);
        $this->assertSame('Monday to Friday, 09:00 – 17:00.', $this->ask('your working hours')->message);
    }

    public function test_keyword_matches(): void
    {
        $r = $this->ask('Do you have a phone number?');

        $this->assertSame(ChatbotResponse::TYPE_FAQ, $r->type);
        $this->assertSame('Call +389 71 354 822.', $r->message);
    }

    public function test_macedonian_cyrillic_question(): void
    {
        $this->assertSame('Пон - Пет, 09:00 - 17:00.', $this->ask('Кое е работното време?')->message);
    }

    public function test_macedonian_latin_transliteration(): void
    {
        $this->assertSame('Да, сервисираме и продаваме принтери.', $this->ask('Dali servisirate printeri?')->message);
        $this->assertSame('kontakt', TextNormalizer::normalize('КОНТАКТ!'));
        $this->assertSame(TextNormalizer::normalize('Шишe чаша жешко'), TextNormalizer::normalize('šiše čaša žeško'));
    }

    public function test_english_question(): void
    {
        $this->assertSame('Call +389 71 354 822.', $this->ask('how can i contact you')->message);
    }

    public function test_unknown_question_returns_fallback_not_a_random_answer(): void
    {
        $r = $this->ask('Колку чини пица со печурки?');

        $this->assertSame(ChatbotResponse::TYPE_FALLBACK, $r->type);
        $this->assertFalse($r->answered);
        $this->assertSame($this->settings()->fallback_message, $r->message);
        $this->assertNull($r->matchedFaqId);
    }

    public function test_inactive_faq_entries_are_ignored(): void
    {
        $this->faq('Do you sell drones?', 'Yes, we sell drones.', ['drones'], 'en', false);

        $this->assertSame(ChatbotResponse::TYPE_FALLBACK, $this->ask('Do you sell drones?')->type);
    }

    public function test_faqs_in_inactive_category_are_ignored(): void
    {
        $category = ChatbotCategory::create(['name' => 'Hidden', 'is_active' => false]);
        $faq = $this->faq('Do you sell drones?', 'Yes, we sell drones.', [], 'en');
        $faq->update(['category_id' => $category->id]);

        $this->assertSame(ChatbotResponse::TYPE_FALLBACK, $this->ask('Do you sell drones?')->type);
    }

    public function test_threshold_is_configurable(): void
    {
        $config = ChatbotProviderSetting::for('faq');
        $config->update(['additional_settings' => ['threshold' => 1.0, 'keyword_matching' => true, 'language_matching' => true]]);
        $this->assertSame(ChatbotResponse::TYPE_FALLBACK, $this->ask('working hours open now')->type);

        $config->update(['additional_settings' => ['threshold' => 0.3, 'keyword_matching' => true, 'language_matching' => true]]);
        $this->assertSame(ChatbotResponse::TYPE_FAQ, $this->ask('working hours open now')->type);
    }

    public function test_keyword_matching_can_be_disabled(): void
    {
        ChatbotProviderSetting::for('faq')->update(['additional_settings' => ['threshold' => 0.5, 'keyword_matching' => false]]);

        $this->assertSame(ChatbotResponse::TYPE_FALLBACK, $this->ask('Do you have a phone number?')->type);
    }

    public function test_response_limit_truncates_answers(): void
    {
        $this->faq('Long answer please', str_repeat('Lorem ipsum dolor. ', 50), [], 'en');
        $this->settings(['response_limit' => 100]);

        $this->assertSame(100, mb_strlen($this->ask('Long answer please')->message));
    }
}
