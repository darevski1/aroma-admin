<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Model;

/** Per-provider configuration (FAQ Engine, Ollama, OpenAI). */
class ChatbotProviderSetting extends Model
{
    protected $table = 'chatbot_provider_settings';

    protected $fillable = [
        'provider', 'model_name', 'base_url', 'api_key_reference', 'api_key_encrypted',
        'is_enabled', 'additional_settings',
    ];

    // The stored key is encrypted at rest and never serialized.
    protected $hidden = ['api_key_encrypted'];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'additional_settings' => 'array',
            'api_key_encrypted' => 'encrypted',
        ];
    }

    public static function for(string $provider): self
    {
        return static::firstOrCreate(['provider' => $provider], ['is_enabled' => $provider === 'faq']);
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        $value = data_get($this->additional_settings ?? [], $key);

        return $value === null || $value === '' ? $default : $value;
    }
}
