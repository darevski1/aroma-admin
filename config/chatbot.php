<?php

/*
|--------------------------------------------------------------------------
| Chatbot
|--------------------------------------------------------------------------
|
| Server-side defaults for the website chatbot. Everything an administrator
| changes day to day (active provider, models, instructions, FAQ, knowledge)
| lives in the database and is edited under Admin → Chatbot. Secrets stay here
| / in .env and are never sent to the browser.
|
*/

return [

    // Providers the manager knows about: key => provider class.
    'providers' => [
        'faq' => \App\Services\Chatbot\Providers\FaqChatbotProvider::class,
        'ollama' => \App\Services\Chatbot\Providers\OllamaChatbotProvider::class,
        'openai' => \App\Services\Chatbot\Providers\OpenAIChatbotProvider::class,
    ],

    // Providers that cost money per request. The manager never falls back to these.
    'paid_providers' => ['openai'],

    // Public endpoint throttle (requests per minute per visitor session / IP).
    'rate_limit_per_minute' => (int) env('CHATBOT_RATE_LIMIT', 20),

    // Maximum visitor message length (characters).
    'max_message_length' => 1000,

    'ollama' => [
        'base_url' => env('OLLAMA_BASE_URL', 'http://127.0.0.1:11434'),
        'model' => env('OLLAMA_MODEL', 'llama3.2'),
    ],

    'openai' => [
        // The API key is read from the environment unless an administrator stores
        // one (encrypted) through the admin panel.
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    // Retrieval: how much knowledge base context is sent to AI providers.
    'retrieval' => [
        'max_entries' => 4,
        'max_chars_per_entry' => 1500,
        'max_total_chars' => 6000,
        'history_messages' => 6,
    ],
];
