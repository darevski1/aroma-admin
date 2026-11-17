<?php

namespace Database\Seeders;

use App\Models\Chatbot\ChatbotCategory;
use App\Models\Chatbot\ChatbotFaq;
use App\Models\Chatbot\ChatbotKnowledge;
use Illuminate\Database\Seeder;

/**
 * Starter FAQ / knowledge content for the website chatbot, taken only from
 * facts already published on the site (contact page, services pages).
 * Idempotent: existing entries (same question / title) are left untouched,
 * so admin edits are never overwritten.
 */
class ChatbotSeeder extends Seeder
{
    public function run(): void
    {
        $contact = ChatbotCategory::firstOrCreate(['slug' => 'kontakt'], ['name' => 'Контакт / Contact', 'sort_order' => 1]);
        $services = ChatbotCategory::firstOrCreate(['slug' => 'uslugi'], ['name' => 'Услуги / Services', 'sort_order' => 2]);

        $faqs = [
            [$contact, 'mk', 'Како можам да ве контактирам?',
                "Можете да не контактирате на телефон +389 71 354 822 или на email info@itplus.mk. Може да ни пишете и преку формата на страницата Контакт: " . url('/kontakt'),
                ['контакт', 'телефон', 'емаил', 'email', 'број', 'јавам', 'контактирам']],
            [$contact, 'en', 'How can I contact you?',
                'You can reach us by phone at +389 71 354 822 or by email at info@itplus.mk. You can also use the form on our Contact page: ' . url('/kontakt'),
                ['contact', 'phone', 'email', 'call', 'number', 'reach']],
            [$contact, 'mk', 'Каде се наоѓате?',
                'Нашата адреса е Бул. 11 Октомври 86 - 3/8, 1000 Скопје, Македонија.',
                ['адреса', 'локација', 'наоѓате', 'канцеларија', 'скопје']],
            [$contact, 'en', 'Where are you located?',
                'Our address is Bul. 11 Oktomvri 86 - 3/8, 1000 Skopje, North Macedonia.',
                ['address', 'location', 'located', 'office', 'skopje']],
            [$contact, 'mk', 'Кое е вашето работно време?',
                'Работиме од понеделник до петок, од 09:00 до 17:00 часот.',
                ['работно време', 'работите', 'отворено', 'часови', 'викенд']],
            [$contact, 'en', 'What are your working hours?',
                'We are open Monday to Friday, 09:00 – 17:00.',
                ['working hours', 'opening hours', 'open', 'hours', 'weekend']],
            [$services, 'mk', 'Кои услуги ги нудите?',
                "ITPlus нуди комплетни ИТ решенија:\n• Мрежни инсталации и одржување на мрежи\n• Одржување на компјутерски системи\n• Видео надзор и контрола на пристап\n• Сервис и продажба на принтери\n• CenoBots автономни роботи за чистење\nПовеќе на: " . url('/uslugi'),
                ['услуги', 'нудите', 'правите', 'сервис', 'решенија']],
            [$services, 'en', 'What services do you offer?',
                "ITPlus offers complete IT solutions:\n• Network installation and maintenance\n• Computer system maintenance\n• Video surveillance and access control\n• Printer service and sales\n• CenoBots autonomous cleaning robots\nMore at: " . url('/uslugi'),
                ['services', 'offer', 'solutions', 'do you do']],
            [$services, 'mk', 'Што се CenoBots роботите?',
                'CenoBots се професионални автономни роботи за комерцијално и индустриско чистење – продажба, изнајмување, обука и локален сервис преку ITPlus. Повеќе: ' . url('/services/cenobots'),
                ['cenobots', 'робот', 'роботи', 'чистење']],
            [$services, 'en', 'What are CenoBots robots?',
                'CenoBots are professional autonomous robots for commercial and industrial cleaning – sales, rental, training and local service through ITPlus. More: ' . url('/services/cenobots'),
                ['cenobots', 'robot', 'robots', 'cleaning']],
        ];

        foreach ($faqs as $i => [$category, $language, $question, $answer, $keywords]) {
            ChatbotFaq::firstOrCreate(['question' => $question], [
                'category_id' => $category->id,
                'language' => $language,
                'answer' => $answer,
                'keywords' => $keywords,
                'is_active' => true,
                'sort_order' => $i + 1,
            ]);
        }

        ChatbotKnowledge::firstOrCreate(['title' => 'Контакт информации / Contact information'], [
            'category_id' => $contact->id,
            'language' => 'mk',
            'type' => 'url',
            'source' => url('/kontakt'),
            'content' => "Адреса: Бул. 11 Октомври 86 - 3/8, 1000 Скопје, Македонија\nТелефон: +389 71 354 822\nEmail: info@itplus.mk\nРаботно време: Пон - Пет, 09:00 - 17:00\n\nAddress: Bul. 11 Oktomvri 86 - 3/8, 1000 Skopje, North Macedonia. Phone: +389 71 354 822. Email: info@itplus.mk. Working hours: Monday–Friday 09:00–17:00.",
            'is_active' => true,
        ]);

        ChatbotKnowledge::firstOrCreate(['title' => 'Услуги на ITPlus / ITPlus services'], [
            'category_id' => $services->id,
            'language' => 'mk',
            'type' => 'url',
            'source' => url('/uslugi'),
            'content' => "ITPlus нуди професионални ИТ решенија: мрежни инсталации и одржување на мрежи, одржување на компјутерски системи, видео надзор и контрола на пристап, сервис и продажба на принтери, техничка поддршка и CenoBots автономни роботи за чистење (продажба, изнајмување, обука и локален сервис).\n\nITPlus provides professional IT solutions: network installation and maintenance, computer system maintenance, video surveillance and access control, printer service and sales, technical support, and CenoBots autonomous cleaning robots (sales, rental, training and local service).",
            'is_active' => true,
        ]);
    }
}
