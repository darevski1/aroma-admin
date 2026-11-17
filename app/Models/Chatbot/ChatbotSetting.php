<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Model;

/** Single-row table holding the chatbot's general settings and system instructions. */
class ChatbotSetting extends Model
{
    protected $table = 'chatbot_settings';

    protected $fillable = [
        'enabled', 'show_on_frontend', 'bot_name', 'welcome_message', 'system_instructions',
        'instruction_sections', 'default_language', 'fallback_message', 'unavailable_message',
        'provider', 'fallback_to_faq', 'response_limit', 'temperature',
    ];

    // Internal instructions never leave the server through serialization.
    protected $hidden = ['system_instructions', 'instruction_sections'];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'show_on_frontend' => 'boolean',
            'fallback_to_faq' => 'boolean',
            'instruction_sections' => 'array',
            'response_limit' => 'integer',
            'temperature' => 'float',
        ];
    }

    public static function current(): self
    {
        return static::query()->orderBy('id')->first() ?? static::create([
            'bot_name' => 'Assistant',
            'provider' => 'faq',
            'default_language' => 'mk',
        ])->refresh();
    }

    /** Whether the widget should be rendered on public pages. */
    public function isVisible(): bool
    {
        return $this->enabled && $this->show_on_frontend;
    }
}
