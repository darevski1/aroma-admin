<?php

namespace App\Http\Controllers\Admin\Chatbot;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotProviderSetting;
use App\Models\Chatbot\ChatbotSetting;
use App\Services\Chatbot\ChatbotManager;
use App\Services\Chatbot\Providers\OpenAIChatbotProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProviderController extends Controller
{
    public function __construct(private readonly ChatbotManager $manager)
    {
    }

    public function index()
    {
        $settings = ChatbotSetting::current();

        $providers = collect(array_keys($this->manager->available()))->mapWithKeys(fn ($key) => [$key => [
            'provider' => $this->manager->provider($key),
            'config' => ChatbotProviderSetting::for($key),
            'problems' => $this->manager->problems($key),
        ]]);

        /** @var OpenAIChatbotProvider $openai */
        $openai = $this->manager->provider('openai');

        return view('admin_pages.chatbot.providers', [
            'settings' => $settings,
            'providers' => $providers,
            'activeKey' => $this->manager->activeKey($settings),
            'openaiKey' => [
                'source' => $openai->keySource(),
                'env_present' => filled(config('chatbot.openai.api_key')),
                'stored_present' => filled(ChatbotProviderSetting::for('openai')->api_key_encrypted),
            ],
        ]);
    }

    public function activate(Request $request)
    {
        $data = $request->validate([
            'provider' => ['required', Rule::in(array_keys($this->manager->available()))],
        ]);
        $key = $data['provider'];
        $provider = $this->manager->provider($key);

        if ($problems = $this->manager->problems($provider)) {
            return back()->with('error', 'Cannot activate ' . $provider->label() . ': ' . implode(' ', $problems));
        }

        // A paid provider is only ever activated by an explicit, confirmed admin action.
        if ($provider->isPaid() && ! $request->boolean('confirm_paid')) {
            return back()->with('error', 'Please confirm that ' . $provider->label() . ' requests are billed to your account.');
        }

        ChatbotSetting::current()->update(['provider' => $key]);

        return back()->with('success', $provider->label() . ' is now the active provider.');
    }

    public function update(Request $request, string $provider)
    {
        $config = ChatbotProviderSetting::for($provider);
        $settings = ChatbotSetting::current();
        $enabled = $request->boolean('is_enabled');

        if (! $enabled && $settings->provider === $provider) {
            return back()->with('error', 'This provider is active. Switch to another provider before disabling it.');
        }

        match ($provider) {
            'faq' => $this->updateFaq($request, $config),
            'ollama' => $this->updateOllama($request, $config),
            'openai' => $this->updateOpenAi($request, $config),
        };

        $config->is_enabled = $enabled;
        $config->save();

        return back()->with('success', $this->manager->provider($provider)->label() . ' settings saved.');
    }

    public function test(string $provider): JsonResponse
    {
        $result = $this->manager->provider($provider)->testConnection();

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    private function updateFaq(Request $request, ChatbotProviderSetting $config): void
    {
        $data = $request->validate(['threshold' => 'required|numeric|min:0.1|max:1']);

        $config->additional_settings = [
            'threshold' => round((float) $data['threshold'], 2),
            'language_matching' => $request->boolean('language_matching'),
            'keyword_matching' => $request->boolean('keyword_matching'),
        ];
    }

    private function updateOllama(Request $request, ChatbotProviderSetting $config): void
    {
        $data = $request->validate([
            'base_url' => 'required|url:http,https|max:255',
            'model_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9._:\/\-]+$/'],
            'timeout' => 'required|integer|min:1|max:300',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'required|integer|min:16|max:8192',
        ]);

        $config->base_url = rtrim($data['base_url'], '/');
        $config->model_name = $data['model_name'];
        $config->additional_settings = array_filter([
            'timeout' => (int) $data['timeout'],
            // Empty = use the default temperature from System Instructions.
            'temperature' => $data['temperature'] !== null ? (float) $data['temperature'] : null,
            'max_tokens' => (int) $data['max_tokens'],
        ], fn ($v) => $v !== null);
    }

    private function updateOpenAi(Request $request, ChatbotProviderSetting $config): void
    {
        $data = $request->validate([
            'api_key_source' => 'required|in:env,database',
            'api_key' => ['nullable', 'string', 'max:300', 'regex:/^\S+$/'],
            'model_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9._:\-]+$/'],
            'base_url' => 'nullable|url:https,http|max:255',
            'timeout' => 'required|integer|min:1|max:300',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'required|integer|min:16|max:16384',
            'max_requests_per_hour' => 'required|integer|min:0|max:100000',
            'max_requests_per_day' => 'required|integer|min:0|max:1000000',
            'input_price_per_million' => 'nullable|numeric|min:0|max:10000',
            'output_price_per_million' => 'nullable|numeric|min:0|max:10000',
        ]);

        $config->api_key_reference = $data['api_key_source'];
        if (filled($data['api_key'] ?? null)) {
            $config->api_key_encrypted = $data['api_key'];
        } elseif ($request->boolean('clear_api_key')) {
            $config->api_key_encrypted = null;
        }

        $config->model_name = $data['model_name'];
        $config->base_url = filled($data['base_url'] ?? null) ? rtrim($data['base_url'], '/') : null;
        $config->additional_settings = [
            'timeout' => (int) $data['timeout'],
            'max_tokens' => (int) $data['max_tokens'],
            'max_requests_per_hour' => (int) $data['max_requests_per_hour'],
            'max_requests_per_day' => (int) $data['max_requests_per_day'],
            'input_price_per_million' => $data['input_price_per_million'] !== null ? (float) $data['input_price_per_million'] : null,
            'output_price_per_million' => $data['output_price_per_million'] !== null ? (float) $data['output_price_per_million'] : null,
        ];
        // Empty = use the default temperature from System Instructions (empty there too = not sent).
        if ($data['temperature'] !== null) {
            $config->additional_settings += ['temperature' => (float) $data['temperature']];
        }
    }
}
