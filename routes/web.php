<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Models\HomepageSection;

// Auth routes WITHOUT registration
Auth::routes(['register' => false, 'reset' => true]);

Route::get("/", function(){
    $sections = HomepageSection::published();
    return view("welcome", compact('sections'));
})->middleware('page.active');

// ─── Admin-only routes ────────────────────────────────────────
Route::group(['middleware' => ['auth', 'verified', 'admin']], function () {

    // Dashboard
    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // User Management
    Route::get('/users', [DashboardController::class, 'index'])->name('users');
    Route::get('/users/create', [DashboardController::class, 'create'])->name('users.create');
    Route::post('/users', [DashboardController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [DashboardController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [DashboardController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [DashboardController::class, 'destroy'])->name('users.destroy');
    Route::get('/get-users', [DashboardController::class, 'getUsers'])->name('get.users');

    // Navigation Builder
    Route::get('/navigation', [NavigationController::class, 'viewNavigation'])->name('navigation');
    Route::get('/navigation/tree', [NavigationController::class, 'tree'])->name('navigation.tree');
    Route::post('/navigation', [NavigationController::class, 'store'])->name('navigation.store');
    Route::put('/navigation/save', [NavigationController::class, 'saveMenu'])->name('navigation.save');

    // Page Management (CMS) – admin index moved to /admin/pages to avoid conflict with public /pages
    Route::get('/admin/pages', [PageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [PageController::class, 'store'])->name('pages.store');

    // Add Existing Manual Blade Page (from resources/views/pages)
    Route::get('/pages/manual-blades', [PageController::class, 'manualBlades'])->name('pages.manual-blades');
    Route::post('/pages/add-existing', [PageController::class, 'addExisting'])->name('pages.add-existing');
    Route::post('/pages/add-existing-selected', [PageController::class, 'addExistingSelected'])->name('pages.add-existing-selected');
    Route::post('/pages/remove-existing', [PageController::class, 'removeExisting'])->name('pages.remove-existing');
    Route::post('/pages/remove-all-existing', [PageController::class, 'removeAllExisting'])->name('pages.remove-all-existing');

    // Page Editor (must be before {page} wildcard routes)
    Route::get('/pages/{page}/editor', [PageController::class, 'editor'])->name('pages.editor');
    Route::post('/pages/{page}/save-content', [PageController::class, 'saveContent'])->name('pages.save-content');

    // Template Content Editor
    Route::get('/pages/{page}/edit-content', [PageController::class, 'editContent'])->name('pages.edit-content');
    Route::patch('/pages/{page}/update-content', [PageController::class, 'updateContent'])->name('pages.update-content');
    Route::post('/pages/{page}/visual-save', [PageController::class, 'visualSave'])->name('pages.visual-save');

    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::patch('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::patch('/pages/{page}/toggle-publish', [PageController::class, 'togglePublish'])->name('pages.toggle');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');

    Route::get('/pages/all/json', [PageController::class, 'allJson'])->name('pages.all.json');

    // Health Check
    Route::get('/health', [\App\Http\Controllers\HealthController::class, 'index'])->name('health-check');

    // ─── Media Library ─────────────────────────────────────
    Route::get('/admin/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/admin/media', [MediaController::class, 'store'])->name('media.store');
    Route::get('/admin/media/{media}', [MediaController::class, 'show'])->name('media.show');
    Route::patch('/admin/media/{media}', [MediaController::class, 'update'])->name('media.update');
    Route::delete('/admin/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::post('/admin/media/bulk-delete', [MediaController::class, 'bulkDelete'])->name('media.bulk-delete');
    Route::post('/admin/media/{media}/replace', [MediaController::class, 'replace'])->name('media.replace');
    Route::get('/admin/media/{media}/json', [MediaController::class, 'showJson'])->name('media.json');
    Route::get('/admin/media/{media}/download', [MediaController::class, 'download'])->name('media.download');

    // Media Picker API
    Route::get('/admin/media-picker', [MediaController::class, 'picker'])->name('media.picker');
    Route::post('/admin/media-picker/upload', [MediaController::class, 'pickerUpload'])->name('media.picker.upload');

    // ─── Posts ─────────────────────────────────────────────
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::patch('/posts/{post:slug}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post:slug}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::patch('/posts/{post:slug}/toggle-publish', [PostController::class, 'togglePublish'])->name('posts.toggle');

    // ─── Homepage Sections ──────────────────────────────────
    Route::get('/homepage-sections', [HomepageSectionController::class, 'index'])->name('homepage-sections.index');
    Route::get('/homepage-sections/create', [HomepageSectionController::class, 'create'])->name('homepage-sections.create');
    Route::post('/homepage-sections', [HomepageSectionController::class, 'store'])->name('homepage-sections.store');
    Route::patch('/homepage-sections/{section}/toggle-publish', [HomepageSectionController::class, 'togglePublish'])->name('homepage-sections.toggle-publish');
    Route::patch('/homepage-sections/{section}/order', [HomepageSectionController::class, 'updateOrder'])->name('homepage-sections.order');
    Route::post('/homepage-sections/reorder', [HomepageSectionController::class, 'reorder'])->name('homepage-sections.reorder');
});

// ─── Public routes ────────────────────────────────────────────
Route::middleware('page.active')->group(function () {
Route::get('/services/cenobots', [PublicController::class, 'cennobots'])->name('cennobots');

// CenoBots models — overview + one page per model. The {model} segment is
// constrained to the ids declared in config/cenobots.php so unknown slugs 404.
Route::get('/modeli', [PublicController::class, 'cennobotsModels'])->name('cennobots.models');
// Every model detail page uses the same template. The {model} placeholder is
// constrained to known catalogue ids, so unknown slugs still 404.
Route::get('/modeli/{model}', [PublicController::class, 'cennobotsModel'])
    ->whereIn('model', array_column(config('cenobots.models'), 'id'))
    ->name('cennobots.model');

// Same pages also reachable under the CenoBots section path, so they sit next
// to /services/cenobots in the sitemap and share its header/footer links.
Route::get('/services/cenobots/modeli', [PublicController::class, 'cennobotsModels'])->name('cennobots.models.section');
Route::get('/services/cenobots/modeli/{model}', [PublicController::class, 'cennobotsModel'])
    ->whereIn('model', array_column(config('cenobots.models'), 'id'))
    ->name('cennobots.model.section');

Route::get('uslugi/mrezni-instalacii-i-odrzuvanje-na-mrezi', [PublicController::class, 'computerNetwork'])->name('mrezni-instalacii-i-odrzuvanje-na-mrezi');
Route::get('uslugi/odrzuvanje-na-kompjuterski-sistemi', [PublicController::class, 'computerWorld'])->name('odrzuvanje-na-kompjuterski-sistemi');
Route::get('uslugi/video-nadzor-i-kontrola-na-pristap', [PublicController::class, 'accessControl'])->name('video-nadzor-i-kontrola-na-pristap');
Route::get('uslugi/servis-i-prodazba-na-printeri', [PublicController::class, 'printerWorld'])->name('servis-i-prodazba-na-printeri');
Route::get('za-nas', [PublicController::class, 'about'])->name('za-nas');
Route::get('uslugi', [PublicController::class, 'uslugi'])->name('uslugi');
Route::get('portfolio', [PublicController::class, 'portfolio'])->name('portfolio');
Route::get('kontakt', [PublicController::class, 'kontakt'])->name('kontakt');
});

// ─── Public Pages listing & slug-based rendering ──────────────
Route::get('/pages', [PublicController::class, 'pagesListing'])->name('pages.listing');
Route::get('/pages/{slug}', [PublicController::class, 'showPage'])->name('pages.public');

// Dynamic page rendering (fallback for pages not matched by specific routes)
Route::get('/p/{slug}', [PageController::class, 'show'])->name('pages.show');

// XML sitemap for search engines (see SitemapController)
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');
