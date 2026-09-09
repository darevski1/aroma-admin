<?php

namespace App\Support;

/**
 * Defines the editable content fields for each manually-created page template.
 *
 * Each layout maps to an array of "sections", and each section contains
 * fields the admin can edit. Field types:
 *   - text      → single-line input
 *   - textarea  → multi-line input
 *   - image     → media picker (stores URL/path)
 *   - repeatable → a group of fields that can appear multiple times (stats, cards, etc.)
 */
class PageTemplateConfig
{
    /**
     * Check whether a given layout value corresponds to a template-based page.
     */
    public static function isTemplatePage(string $layout): bool
    {
        return array_key_exists($layout, static::getLayouts());
    }

    /**
     * Get all registered template layouts.
     */
    public static function getLayouts(): array
    {
        return [
            'pages.about.index'             => 'За Нас (ITPlus)',
            'pages.uslugi.index'            => 'Услуги',
            'pages.computer_network.index'  => 'Мрежни инсталации',
            'pages.computerWorld.index'     => 'Одржување компјутери',
            'pages.accessControl.index'     => 'Видео надзор',
            'pages.printerWorld.index'      => 'Принтери',
            'pages.cennobots.index'         => 'ЦеноБотс',
            'pages.portfolio.index'         => 'Портфолио',
            'pages.kontakt.index'           => 'Контакт',
        ];
    }

    /**
     * Get the editable field definitions for a given layout.
     *
     * Returns an array of sections, each containing fields.
     * Each field: ['key' => string, 'label' => string, 'type' => text|textarea|image, 'default' => mixed]
     */
    public static function getFields(string $layout): array
    {
        $method = 'layout_' . str_replace('.', '_', $layout);

        if (method_exists(static::class, $method)) {
            return static::$method();
        }

        return [];
    }

    // ─── pages.about.index ──────────────────────────────────────────

    protected static function layout_pages_about_index(): array
    {
        return [
            [
                'name'   => 'Hero Section',
                'fields' => [
                    ['key' => 'hero_eyebrow',          'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'За Нас'],
                    ['key' => 'hero_heading_line_1',    'label' => 'Heading Line 1', 'type' => 'text',     'default' => 'Креираме Модерни'],
                    ['key' => 'hero_heading_line_2',    'label' => 'Heading Line 2', 'type' => 'text',     'default' => 'ИКТ Решенија'],
                    ['key' => 'hero_description',       'label' => 'Description',   'type' => 'textarea', 'default' => 'Нашата приказна започнува во 2018 година со тим од искусни и амбициозни инженери, посветени на развој на современи софтверски и ИКТ решенија за компании ширум светот.'],
                ],
            ],
            [
                'name'   => 'Stats (Right Cards)',
                'type'   => 'repeatable',
                'repeat' => 4,
                'fields' => [
                    ['key' => 'stat_{n}_value', 'label' => 'Stat Value', 'type' => 'text'],
                    ['key' => 'stat_{n}_label', 'label' => 'Stat Label', 'type' => 'text'],
                ],
                'defaults' => [
                    ['stat_1_value' => '15+',       'stat_1_label' => 'Години Искуство'],
                    ['stat_2_value' => '24/7',      'stat_2_label' => 'Техничка Поддршка'],
                    ['stat_3_value' => '100+',      'stat_3_label' => 'Реализирани Проекти'],
                    ['stat_4_value' => 'Worldwide',  'stat_4_label' => 'Клиенти Низ Светот'],
                ],
            ],
            [
                'name'   => 'Story Section',
                'fields' => [
                    ['key' => 'story_eyebrow',       'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'Нашата Приказна'],
                    ['key' => 'story_heading_line_1', 'label' => 'Heading Line 1', 'type' => 'text',     'default' => 'Технологијата е Нашата'],
                    ['key' => 'story_heading_line_2', 'label' => 'Heading Line 2', 'type' => 'text',     'default' => 'Инспирација'],
                    ['key' => 'story_paragraph_1',    'label' => 'Paragraph 1',   'type' => 'textarea', 'default' => 'Денес сме брзорастечка софтверска и ИКТ компанија која нуди иновативни решенија, професионални услуги и долгорочна поддршка за своите клиенти.'],
                    ['key' => 'story_paragraph_2',    'label' => 'Paragraph 2',   'type' => 'textarea', 'default' => 'Нашиот тим постојано инвестира во нови технологии и знаење, со цел да обезбедиме сигурни, модерни и ефикасни решенија кои го подобруваат работењето и дигиталната трансформација на компаниите.'],
                ],
            ],
        ];
    }

    // ─── pages.uslugi.index ─────────────────────────────────────────

    protected static function layout_pages_uslugi_index(): array
    {
        return [
            [
                'name'   => 'Hero Section',
                'fields' => [
                    ['key' => 'hero_eyebrow',    'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'Професионални ИТ услуги'],
                    ['key' => 'hero_heading',    'label' => 'Heading',       'type' => 'text',     'default' => 'Стабилна технологија за сигурен бизнис.'],
                    ['key' => 'hero_description','label' => 'Description',   'type' => 'textarea', 'default' => 'Нудиме професионални ИТ решенија за компании и физички лица, со брза поддршка, сигурност и современа технологија прилагодена на вашата работа.'],
                ],
            ],
            [
                'name'   => 'Overview Section',
                'fields' => [
                    ['key' => 'overview_eyebrow', 'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'Што покриваме'],
                    ['key' => 'overview_heading',  'label' => 'Heading',       'type' => 'text',     'default' => 'Услуги организирани околу реалните потреби на вашиот бизнис.'],
                    ['key' => 'overview_paragraph','label' => 'Description',   'type' => 'textarea', 'default' => 'Од мрежа и опрема до безбедност и печатење, секоја услуга е поставена за стабилност, брза реакција и долгорочна вредност.'],
                ],
            ],
            [
                'name'   => 'Services (4 items)',
                'type'   => 'repeatable',
                'repeat' => 4,
                'fields' => [
                    ['key' => 'service_{n}_title',       'label' => 'Title',       'type' => 'text'],
                    ['key' => 'service_{n}_description', 'label' => 'Description', 'type' => 'textarea'],
                ],
                'defaults' => [
                    ['service_1_title' => 'Мрежни инсталации и одржување на мрежи', 'service_1_description' => 'Комплетни решенија за LAN/Wi-Fi мрежи, конфигурација на рутери, switch уреди и стабилна мрежна инфраструктура.'],
                    ['service_2_title' => 'Одржување на компјутерски системи',      'service_2_description' => 'Редовно сервисирање, оптимизација и техничка поддршка за компјутери, сервери и деловни ИТ системи.'],
                    ['service_3_title' => 'Видео надзор и контрола на пристап',     'service_3_description' => 'Инсталација на CCTV системи, IP камери и безбедносни решенија за професионална заштита на домови и компании.'],
                    ['service_4_title' => 'Сервис и продажба на принтери',           'service_4_description' => 'Продажба, сервисирање и одржување на ласерски и inkjet принтери, тонери и резервни делови.'],
                ],
            ],
            [
                'name'   => 'Process Section',
                'fields' => [
                    ['key' => 'process_eyebrow', 'label' => 'Eyebrow Text', 'type' => 'text',     'default' => 'Како работиме'],
                    ['key' => 'process_heading',  'label' => 'Heading',      'type' => 'text',     'default' => 'Јасен процес, брза реализација и поддршка после имплементација.'],
                    ['key' => 'process_step_1',   'label' => 'Step 1',       'type' => 'text',     'default' => 'Анализа на потреби и постоечка инфраструктура.'],
                    ['key' => 'process_step_2',   'label' => 'Step 2',       'type' => 'text',     'default' => 'Предлог решение со јасен обем, рокови и приоритети.'],
                    ['key' => 'process_step_3',   'label' => 'Step 3',       'type' => 'text',     'default' => 'Имплементација, тестирање и континуирана техничка поддршка.'],
                ],
            ],
        ];
    }

    // ─── pages.computer_network.index ───────────────────────────────

    protected static function layout_pages_computer_network_index(): array
    {
        return [
            [
                'name'   => 'Header Section',
                'fields' => [
                    ['key' => 'eyebrow',     'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'CCTV Решенија'],
                    ['key' => 'heading',      'label' => 'Heading',       'type' => 'textarea', 'default' => "Паметен видео надзор за\nвашиот дом и бизнис"],
                    ['key' => 'description',  'label' => 'Description',   'type' => 'textarea', 'default' => "Професионална инсталација на CCTV системи,\nIP камери и видео надзор со 24/7 мониторинг,\nдалечински пристап и максимална безбедност."],
                ],
            ],
            [
                'name'   => 'Cards (3 items)',
                'type'   => 'repeatable',
                'repeat' => 3,
                'fields' => [
                    ['key' => 'card_{n}_title',       'label' => 'Title',       'type' => 'text'],
                    ['key' => 'card_{n}_description', 'label' => 'Description', 'type' => 'textarea'],
                    ['key' => 'card_{n}_image',       'label' => 'Image',       'type' => 'image', 'default' => './../assets/images/printer.jpg'],
                ],
                'defaults' => [
                    ['card_1_title' => 'IP Камери',            'card_1_description' => 'Висококвалитетни IP камери со Full HD и 4K резолуција,\nноќен режим и паметна детекција на движење.', 'card_1_image' => './../assets/images/printer.jpg'],
                    ['card_2_title' => '24/7 Мониторинг',      'card_2_description' => 'Следете го вашиот објект во секое време преку мобилен,\nтаблет или компјутер од било каде.',                      'card_2_image' => './../assets/images/printer.jpg'],
                    ['card_3_title' => 'Инсталација и Поддршка','card_3_description' => 'Комплетна инсталација, конфигурација и техничка поддршка\nза сигурен и стабилен CCTV систем.',                      'card_3_image' => './../assets/images/printer.jpg'],
                ],
            ],
            [
                'name'   => 'Bottom CTA',
                'fields' => [
                    ['key' => 'cta_heading',     'label' => 'Heading',    'type' => 'text',     'default' => 'Обезбедете го вашиот простор денес'],
                    ['key' => 'cta_description',  'label' => 'Description','type' => 'textarea', 'default' => "Нудиме современи CCTV решенија за компании,\nмагацини, канцеларии, продавници и домови."],
                    ['key' => 'cta_button_text',  'label' => 'Button Text','type' => 'text',     'default' => 'Побарај Понуда'],
                ],
            ],
            [
                'name'   => 'AI Section',
                'fields' => [
                    ['key' => 'ai_badge',        'label' => 'Badge Text',  'type' => 'text',     'default' => 'AI Security Solutions'],
                    ['key' => 'ai_heading',      'label' => 'Heading',     'type' => 'textarea', 'default' => "Паметен AI Видео Надзор\nза максимална безбедност"],
                    ['key' => 'ai_description',  'label' => 'Description', 'type' => 'textarea', 'default' => "Интелигентни CCTV системи со AI детекција,\nпрепознавање лица, следење на движење и\nпаметни известувања во реално време."],
                    ['key' => 'ai_button_text',  'label' => 'Button Text', 'type' => 'text',     'default' => 'Побарај Понуда'],
                    ['key' => 'ai_background_image', 'label' => 'Background Image', 'type' => 'image', 'default' => './../assets/images/cctv-hero.jpg'],
                ],
            ],
        ];
    }

    // ─── pages.accessControl.index ──────────────────────────────────

    protected static function layout_pages_accessControl_index(): array
    {
        // Same structure as computer_network
        return static::layout_pages_computer_network_index();
    }

    // ─── pages.computerWorld.index ──────────────────────────────────

    protected static function layout_pages_computerWorld_index(): array
    {
        return [
            [
                'name'   => 'Hero Section',
                'fields' => [
                    ['key' => 'hero_eyebrow',     'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'Server Infrastructure'],
                    ['key' => 'hero_heading_1',   'label' => 'Heading Line 1','type' => 'text',     'default' => 'Сервери,'],
                    ['key' => 'hero_heading_2',   'label' => 'Heading Line 2','type' => 'text',     'default' => 'Виртуелизација'],
                    ['key' => 'hero_heading_3',   'label' => 'Heading Line 3 (red accent)', 'type' => 'text', 'default' => 'ИТ Инфраструктура'],
                    ['key' => 'hero_description', 'label' => 'Description',   'type' => 'textarea', 'default' => 'Обезбедуваме комплетни серверски решенија за компании – од инсталација и конфигурација до одржување, мониторинг и 24/7 поддршка за критични системи.'],
                    ['key' => 'hero_image',       'label' => 'Hero Image',    'type' => 'image',    'default' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=1400&auto=format&fit=crop'],
                ],
            ],
            [
                'name'   => 'Stats',
                'type'   => 'repeatable',
                'repeat' => 3,
                'fields' => [
                    ['key' => 'stat_{n}_value', 'label' => 'Value', 'type' => 'text'],
                    ['key' => 'stat_{n}_label', 'label' => 'Label', 'type' => 'text'],
                ],
                'defaults' => [
                    ['stat_1_value' => '24/7',   'stat_1_label' => 'Мониторинг'],
                    ['stat_2_value' => '99.9%',  'stat_2_label' => 'Uptime'],
                    ['stat_3_value' => '15+',    'stat_3_label' => 'Години Искуство'],
                ],
            ],
            [
                'name'   => 'Services Section',
                'fields' => [
                    ['key' => 'services_eyebrow', 'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'Server Services'],
                    ['key' => 'services_heading',  'label' => 'Heading',       'type' => 'text',     'default' => 'Комплетни Серверски Решенија'],
                    ['key' => 'services_paragraph','label' => 'Description',   'type' => 'textarea', 'default' => 'Дизајн, имплементација и одржување на сигурна ИТ инфраструктура за мали, средни и големи компании.'],
                ],
            ],
            [
                'name'   => 'Service Cards (3 items)',
                'type'   => 'repeatable',
                'repeat' => 3,
                'fields' => [
                    ['key' => 'service_card_{n}_title',       'label' => 'Title',       'type' => 'text'],
                    ['key' => 'service_card_{n}_description', 'label' => 'Description', 'type' => 'textarea'],
                ],
                'defaults' => [
                    ['service_card_1_title' => 'Windows / Linux Server', 'service_card_1_description' => 'Инсталација, конфигурација и одржување на Windows Server и Linux системи за стабилна работа на вашата компанија.'],
                    ['service_card_2_title' => 'Virtualization',         'service_card_2_description' => 'VMware и Hyper-V решенија за оптимизација на ресурси, backup и висока достапност.'],
                    ['service_card_3_title' => 'Backup & Security',      'service_card_3_description' => 'Напредни backup системи, disaster recovery и заштита на вашите критични податоци.'],
                ],
            ],
        ];
    }

    // ─── pages.printerWorld.index ───────────────────────────────────

    protected static function layout_pages_printerWorld_index(): array
    {
        return [
            [
                'name'   => 'Hero Section',
                'fields' => [
                    ['key' => 'hero_eyebrow',      'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'Уреди за печатење'],
                    ['key' => 'hero_heading_1',    'label' => 'Heading Line 1','type' => 'text',     'default' => 'Продажба,'],
                    ['key' => 'hero_heading_2',    'label' => 'Heading Line 2','type' => 'text',     'default' => 'Изнајмување'],
                    ['key' => 'hero_heading_3',    'label' => 'Heading Line 3 (red accent)', 'type' => 'text', 'default' => 'Принтери'],
                    ['key' => 'hero_description',  'label' => 'Description',   'type' => 'textarea', 'default' => "Нудиме професионални решенија за продажба, изнајмување\nи сервисирање на printer уреди за компании, институции\nи деловни корисници. Брза поддршка, квалитетна опрема\nи сигурна техничка услуга на едно место."],
                    ['key' => 'hero_image',        'label' => 'Hero Image',    'type' => 'image',    'default' => './../assets/images/printer.jpg'],
                ],
            ],
            [
                'name'   => 'Stats',
                'type'   => 'repeatable',
                'repeat' => 3,
                'fields' => [
                    ['key' => 'stat_{n}_value', 'label' => 'Value', 'type' => 'text'],
                    ['key' => 'stat_{n}_label', 'label' => 'Label', 'type' => 'text'],
                ],
                'defaults' => [
                    ['stat_1_value' => '15+',  'stat_1_label' => 'Години Искуство'],
                    ['stat_2_value' => '24/7', 'stat_2_label' => 'Техничка Поддршка'],
                    ['stat_3_value' => '100+', 'stat_3_label' => 'Задоволни Клиенти'],
                ],
            ],
            [
                'name'   => 'Brand Logos',
                'type'   => 'repeatable',
                'repeat' => 3,
                'fields' => [
                    ['key' => 'logo_{n}_image', 'label' => 'Logo Image', 'type' => 'image'],
                    ['key' => 'logo_{n}_alt',   'label' => 'Alt Text',   'type' => 'text'],
                ],
                'defaults' => [
                    ['logo_1_image' => './../assets/images/logo/Canon_ide_p-tcLS_0.svg', 'logo_1_alt' => 'Canon Logo'],
                    ['logo_2_image' => './../assets/images/logo/Ricoh_Australia_Logo_0.svg', 'logo_2_alt' => 'Ricoh Logo'],
                    ['logo_3_image' => './../assets/images/logo/HP_Logo_0.svg', 'logo_3_alt' => 'HP Logo'],
                ],
            ],
            [
                'name'   => 'Services Section',
                'fields' => [
                    ['key' => 'services_eyebrow', 'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'Наши Услуги'],
                    ['key' => 'services_heading',  'label' => 'Heading',       'type' => 'text',     'default' => 'Комплетни Решенија за Вашиот Бизнис'],
                    ['key' => 'services_paragraph','label' => 'Description',   'type' => 'textarea', 'default' => "Обезбедуваме професионални услуги за продажба,\nизнајмување и одржување на системи\nспоред потребите на вашиот бизнис."],
                ],
            ],
            [
                'name'   => 'Service Cards (3 items)',
                'type'   => 'repeatable',
                'repeat' => 3,
                'fields' => [
                    ['key' => 'svc_card_{n}_title',       'label' => 'Title',       'type' => 'text'],
                    ['key' => 'svc_card_{n}_description', 'label' => 'Description', 'type' => 'textarea'],
                ],
                'defaults' => [
                    ['svc_card_1_title' => 'Продажба',     'svc_card_1_description' => 'Продажба на нови и професионални уреди за печатење од реномирани брендови за сите деловни потреби.'],
                    ['svc_card_2_title' => 'Изнајмување',  'svc_card_2_description' => 'Флексибилни решенија за изнајмување уреди за печатење со месечна поддршка и одржување.'],
                    ['svc_card_3_title' => 'Сервис',       'svc_card_3_description' => 'Брз и професионален сервис за сите уреди, дијагностика и техничка поддршка.'],
                ],
            ],
        ];
    }

    // ─── pages.cennobots.index ──────────────────────────────────────

    protected static function layout_pages_cennobots_index(): array
    {
        return [
            [
                'name'   => 'Hero Section',
                'fields' => [
                    ['key' => 'hero_heading',      'label' => 'Heading',       'type' => 'text',     'default' => 'CENOBOTS'],
                    ['key' => 'hero_description',  'label' => 'Description',   'type' => 'textarea', 'default' => 'AI-автономни роботи за чистење. Паметно, ефикасно и еколошко решение за секој комерцијален простор.'],
                    ['key' => 'hero_button_text',  'label' => 'Button Text',   'type' => 'text',     'default' => 'Дознајте повеќе'],
                    ['key' => 'hero_image',        'label' => 'Robot Image',   'type' => 'image',    'default' => './../assets/images/cenobot.png'],
                ],
            ],
            [
                'name'   => 'Solutions Section',
                'fields' => [
                    ['key' => 'solutions_eyebrow',    'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'Решенија'],
                    ['key' => 'solutions_heading',     'label' => 'Heading',       'type' => 'text',     'default' => 'Индустриски машини за професионално чистење'],
                    ['key' => 'solutions_description', 'label' => 'Description',   'type' => 'textarea', 'default' => 'Cenobots се индустриски машини за чистење наменети за ефикасно, безбедно и автоматизирано одржување на големи комерцијални и индустриски простори. Тие помагаат во намалување на трошоците, зголемување на продуктивноста и обезбедување постојано висок квалитет на чистење.'],
                ],
            ],
            [
                'name'   => 'Areas of Use (4 items)',
                'type'   => 'repeatable',
                'repeat' => 4,
                'fields' => [
                    ['key' => 'area_{n}_title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'area_{n}_image', 'label' => 'Image', 'type' => 'image'],
                ],
                'defaults' => [
                    ['area_1_title' => 'Трговски Центри',  'area_1_image' => './../assets/images/retail.jpg'],
                    ['area_2_title' => 'Хотели',            'area_2_image' => './../assets/images/hotel.jpg'],
                    ['area_3_title' => 'Болници',           'area_3_image' => './../assets/images/hospital.jpg'],
                    ['area_4_title' => 'Образование',       'area_4_image' => './../assets/images/education.jpg'],
                ],
            ],
            [
                'name'   => 'Technology/App Section',
                'fields' => [
                    ['key' => 'tech_heading',      'label' => 'Heading',       'type' => 'text',     'default' => 'Паметна контрола на чистењето во ваши раце'],
                    ['key' => 'tech_paragraph_1',   'label' => 'Paragraph 1',   'type' => 'textarea', 'default' => 'Апликацијата за мобилни уреди на CenoBots L50 носи ново ниво на контрола и прегледност во процесот на чистење. Со модерен интерфејс, корисниците во секој момент имаат увид во работата на роботот — од тековната позиција до детални информации за перформансите.'],
                    ['key' => 'tech_paragraph_2',   'label' => 'Paragraph 2',   'type' => 'textarea', 'default' => 'Преку апликацијата лесно се креираат рути за чистење, добиваат известувања за завршени задачи и се следи статусот на уредот. Управувањето станува едноставно, паметно и целосно под ваша контрола — каде и да сте.'],
                    ['key' => 'tech_image',         'label' => 'App Screenshot', 'type' => 'image',    'default' => './../assets/images/cenobots-app-l50.webp'],
                ],
            ],
            [
                'name'   => 'CTA Section',
                'fields' => [
                    ['key' => 'cta_heading',     'label' => 'Heading',       'type' => 'text',     'default' => 'Потребна ви е професионална ИТ поддршка?'],
                    ['key' => 'cta_description',  'label' => 'Description',   'type' => 'textarea', 'default' => "Контактирајте не за понуда, консултација или техничка поддршка.\nНудиме решенија за мрежи, видео надзор, сервисирање и комплетна ИТ инфраструктура."],
                ],
            ],
        ];
    }

    // ─── pages.portfolio.index ──────────────────────────────────────

    protected static function layout_pages_portfolio_index(): array
    {
        return [
            [
                'name'   => 'Hero Section',
                'fields' => [
                    ['key' => 'hero_eyebrow',    'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'Портфолио'],
                    ['key' => 'hero_heading',    'label' => 'Heading',       'type' => 'text',     'default' => 'Проекти што ја прават технологијата сигурна, брза и видлива.'],
                    ['key' => 'hero_description','label' => 'Description',   'type' => 'textarea', 'default' => "Дел од проектите и решенијата кои успешно ги реализиравме за компании,\nинституции и приватни клиенти."],
                ],
            ],
            [
                'name'   => 'Featured Section',
                'fields' => [
                    ['key' => 'featured_eyebrow', 'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'Featured work'],
                    ['key' => 'featured_heading',  'label' => 'Heading',       'type' => 'text',     'default' => 'Избрани реализации'],
                    ['key' => 'featured_paragraph','label' => 'Description',   'type' => 'textarea', 'default' => 'Поголеми инфраструктурни и безбедносни проекти со јасен фокус на стабилност, контрола и долгорочна вредност.'],
                ],
            ],
            [
                'name'   => 'Projects (6 items)',
                'type'   => 'repeatable',
                'repeat' => 6,
                'fields' => [
                    ['key' => 'project_{n}_title',       'label' => 'Title',       'type' => 'text'],
                    ['key' => 'project_{n}_category',    'label' => 'Category',    'type' => 'text'],
                    ['key' => 'project_{n}_description', 'label' => 'Description', 'type' => 'textarea'],
                    ['key' => 'project_{n}_image',       'label' => 'Image',       'type' => 'image'],
                ],
                'defaults' => [
                    ['project_1_title' => 'Инсталација на корпоративна мрежа', 'project_1_category' => 'Мрежни Решенија', 'project_1_description' => 'Поставување и конфигурација на стабилна LAN/WiFi инфраструктура за деловен објект со повеќе корисници.', 'project_1_image' => '/images/portfolio/network.jpg'],
                    ['project_2_title' => 'CCTV систем за деловен простор',     'project_2_category' => 'Видео Надзор',    'project_2_description' => 'Монтажа на IP камери, NVR систем и далечински пристап за целосна безбедност и контрола.', 'project_2_image' => '/images/portfolio/cctv.jpg'],
                    ['project_3_title' => 'Одржување на ИТ инфраструктура',     'project_3_category' => 'ИТ Поддршка',     'project_3_description' => 'Редовно одржување и оптимизација на компјутерски системи и сервери за компанија со повеќе канцеларии.', 'project_3_image' => '/images/portfolio/computers.jpg'],
                    ['project_4_title' => 'Сервисирање на печатарски систем',   'project_4_category' => 'Принтери',        'project_4_description' => 'Комплетен сервис и конфигурација на мрежни принтери за канцелариска и индустриска употреба.', 'project_4_image' => '/images/portfolio/printer.jpg'],
                    ['project_5_title' => 'Инсталација на сервер систем',       'project_5_category' => 'Серверски Решенија','project_5_description' => 'Конфигурација и обезбедување на серверска инфраструктура со backup и remote management.', 'project_5_image' => '/images/portfolio/server.jpg'],
                    ['project_6_title' => 'Smart Access систем',               'project_6_category' => 'Контрола на Пристап','project_6_description' => 'Инсталација на систем за контрола на пристап со картички, biometric уреди и логирање на корисници.', 'project_6_image' => '/images/portfolio/access-control.jpg'],
                ],
            ],
            [
                'name'   => 'Stats Section',
                'type'   => 'repeatable',
                'repeat' => 4,
                'fields' => [
                    ['key' => 'portfolio_stat_{n}_value', 'label' => 'Value', 'type' => 'text'],
                    ['key' => 'portfolio_stat_{n}_label', 'label' => 'Label', 'type' => 'text'],
                ],
                'defaults' => [
                    ['portfolio_stat_1_value' => '200', 'portfolio_stat_1_label' => 'Реализирани проекти'],
                    ['portfolio_stat_2_value' => '100', 'portfolio_stat_2_label' => 'Активни клиенти'],
                    ['portfolio_stat_3_value' => '10',  'portfolio_stat_3_label' => 'Години искуство'],
                    ['portfolio_stat_4_value' => '24',  'portfolio_stat_4_label' => 'Поддршка и сервис'],
                ],
            ],
            [
                'name'   => 'Testimonials (3 items)',
                'type'   => 'repeatable',
                'repeat' => 3,
                'fields' => [
                    ['key' => 'testimonial_{n}_quote',  'label' => 'Quote',  'type' => 'textarea'],
                    ['key' => 'testimonial_{n}_author', 'label' => 'Author', 'type' => 'text'],
                    ['key' => 'testimonial_{n}_tag',    'label' => 'Tag',    'type' => 'text'],
                ],
                'defaults' => [
                    ['testimonial_1_quote' => '„Стабилна мрежа, брза имплементација и јасна комуникација во секоја фаза од проектот."', 'testimonial_1_author' => 'Корпоративен клиент', 'testimonial_1_tag' => 'Мрежни решенија'],
                    ['testimonial_2_quote' => '„Видео надзорот и контролата на пристап ни дадоа многу подобра прегледност и сигурност."', 'testimonial_2_author' => 'Деловен простор', 'testimonial_2_tag' => 'Безбедносни системи'],
                    ['testimonial_3_quote' => '„Одржувањето е организирано, навремено и без непотребни прекини во секојдневната работа."', 'testimonial_3_author' => 'Приватна компанија', 'testimonial_3_tag' => 'ИТ поддршка'],
                ],
            ],
            [
                'name'   => 'CTA Section',
                'fields' => [
                    ['key' => 'portfolio_cta_eyebrow', 'label' => 'Eyebrow Text',  'type' => 'text',     'default' => 'Next project'],
                    ['key' => 'portfolio_cta_heading',  'label' => 'Heading',       'type' => 'text',     'default' => 'Подготвени сте за подобра ИТ инфраструктура?'],
                    ['key' => 'portfolio_cta_paragraph','label' => 'Description',   'type' => 'textarea', 'default' => 'Кажете ни што ви треба, а ние ќе предложиме јасно, стабилно и скалабилно решение.'],
                ],
            ],
        ];
    }

    // ─── pages.kontakt.index ────────────────────────────────────────

    protected static function layout_pages_kontakt_index(): array
    {
        return [
            [
                'name'   => 'Page Header',
                'fields' => [
                    ['key' => 'page_heading',      'label' => 'Heading',       'type' => 'text',     'default' => 'Контакт'],
                    ['key' => 'page_description',   'label' => 'Description',   'type' => 'textarea', 'default' => 'Слободно контактирајте не за прашања, понуди или техничка поддршка.'],
                ],
            ],
            [
                'name'   => 'Form Section',
                'fields' => [
                    ['key' => 'form_heading',        'label' => 'Form Heading',  'type' => 'text',     'default' => 'Испрати порака'],
                    ['key' => 'form_name_label',     'label' => 'Name Label',    'type' => 'text',     'default' => 'Име и Презиме'],
                    ['key' => 'form_email_label',    'label' => 'Email Label',   'type' => 'text',     'default' => 'Email'],
                    ['key' => 'form_message_label',  'label' => 'Message Label', 'type' => 'text',     'default' => 'Порака'],
                    ['key' => 'form_submit_text',    'label' => 'Submit Button', 'type' => 'text',     'default' => 'Испрати'],
                ],
            ],
            [
                'name'   => 'Contact Info',
                'fields' => [
                    ['key' => 'info_heading',   'label' => 'Heading',   'type' => 'text',     'default' => 'Информации'],
                    ['key' => 'info_address',   'label' => 'Address',   'type' => 'text',     'default' => 'Адреса: Бул: 11 Октомври 86 - 3/8, 1000 Скопје, Македонија'],
                    ['key' => 'info_phone',     'label' => 'Phone',     'type' => 'text',     'default' => 'Телефон: +389 71 354 822'],
                    ['key' => 'info_email',     'label' => 'Email',     'type' => 'text',     'default' => 'Email: info@itplus.mk'],
                    ['key' => 'info_hours',     'label' => 'Hours',     'type' => 'text',     'default' => 'Работно време: Пон - Пет, 09:00 - 17:00'],
                ],
            ],
            [
                'name'   => 'Map Section',
                'fields' => [
                    ['key' => 'map_heading', 'label' => 'Map Heading', 'type' => 'text', 'default' => 'Локација'],
                ],
            ],
        ];
    }

    // ─── Helper: resolve field value with defaults ──────────────────

    /**
     * Resolve a setting value: use stored value, then repeatable defaults, then field default.
     */
    public static function resolveValue(array $settings, array $field, ?array $repeatableDefaults = null, int $index = 0): mixed
    {
        $key = str_replace('{n}', $index, $field['key']);

        // 1. Stored value
        if (isset($settings[$key]) && $settings[$key] !== '') {
            return $settings[$key];
        }

        // 2. Repeatable defaults
        if ($repeatableDefaults && isset($repeatableDefaults[$index][$key])) {
            return $repeatableDefaults[$index][$key];
        }

        // 3. Field default
        return $field['default'] ?? '';
    }
}
