<?php

namespace App\Services\Chatbot\Providers;

use App\Models\Chatbot\ChatbotFaq;
use App\Models\Chatbot\ChatbotProviderSetting;
use App\Services\Chatbot\Support\ChatbotResponse;
use App\Services\Chatbot\Support\ChatContext;
use App\Services\Chatbot\Support\FaqMatcher;

/** Free, database-driven engine: answers from the FAQ, otherwise the fallback message. */
class FaqChatbotProvider extends AbstractChatbotProvider
{
    public function __construct(ChatbotProviderSetting $config, private readonly FaqMatcher $matcher)
    {
        parent::__construct($config);
    }

    public function key(): string
    {
        return 'faq';
    }

    public function label(): string
    {
        return 'FAQ Engine';
    }

    public function usesKnowledgeContext(): bool
    {
        return false;
    }

    public function configurationErrors(): array
    {
        return [];
    }

    public function options(?string $language = null): array
    {
        return [
            'threshold' => (float) $this->config->setting('threshold', FaqMatcher::DEFAULT_THRESHOLD),
            'keyword_matching' => (bool) $this->config->setting('keyword_matching', true),
            'language_matching' => (bool) $this->config->setting('language_matching', true),
            'language' => $language,
        ];
    }

    public function reply(ChatContext $context): ChatbotResponse
    {
        $match = $this->matcher->bestMatch($context->question, $this->options($context->language));

        if ($match) {
            /** @var ChatbotFaq $faq */
            $faq = $match['faq'];

            return new ChatbotResponse(
                message: $faq->answer,
                answered: true,
                type: ChatbotResponse::TYPE_FAQ,
                provider: $this->key(),
                matchedFaqId: $faq->id,
                metadata: ['score' => $match['score']],
            );
        }

        return new ChatbotResponse(
            message: (string) ($context->settings->fallback_message ?: 'Sorry, I do not have information about that.'),
            answered: false,
            type: ChatbotResponse::TYPE_FALLBACK,
            provider: $this->key(),
        );
    }

    public function testConnection(): array
    {
        $count = ChatbotFaq::query()->active()->count();

        return [
            'ok' => true,
            'message' => "FAQ Engine is ready ({$count} active FAQ " . ($count === 1 ? 'entry' : 'entries') . ').',
        ];
    }
}
