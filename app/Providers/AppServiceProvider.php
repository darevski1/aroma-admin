<?php

namespace App\Providers;

use App\Services\Chatbot\ChatbotManager;
use App\Services\Chatbot\Contracts\KnowledgeRetrieverInterface;
use App\Services\Chatbot\Retrieval\KeywordKnowledgeRetriever;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Chatbot: swap the retriever here for semantic / vector search later.
        $this->app->bind(KnowledgeRetrieverInterface::class, KeywordKnowledgeRetriever::class);
        // One manager per request so provider settings are always fresh.
        $this->app->scoped(ChatbotManager::class);
        // Hero sliders: one renderer per request so slider assets load once per page.
        $this->app->scoped(\App\Services\HeroSlider\HeroSliderRenderer::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // @heroSlider('2026-1') in templates; <x-hero-slider code="2026-1" /> works too.
        \Illuminate\Support\Facades\Blade::directive('heroSlider', fn (string $code) =>
            "<?php echo app(\App\Services\HeroSlider\HeroSliderRenderer::class)->renderCode({$code}); ?>");

        // Public chatbot endpoint: per visitor session (falls back to IP).
        RateLimiter::for('chatbot', function (Request $request) {
            $key = $request->hasSession() ? $request->session()->getId() : $request->ip();

            return [
                Limit::perMinute((int) config('chatbot.rate_limit_per_minute', 20))->by('chatbot:' . $key),
                Limit::perMinute((int) config('chatbot.rate_limit_per_minute', 20) * 3)->by('chatbot-ip:' . $request->ip()),
            ];
        });

        Health::checks([
            DatabaseCheck::new(),
            OptimizedAppCheck::new(),
            QueueCheck::new(),
        ]);
    }
}
