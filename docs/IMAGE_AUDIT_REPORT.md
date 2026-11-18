# Aroma – Image audit & Site Images management

Admin screen: **Admin → Site Images** (`/admin/site-images`). Media Library: `/admin/media`.

## How it works
* Every public page is post-processed (`ApplySiteImages` middleware). Each `<img src>`, inline `background-image:url()` and `<style>` `url()` is keyed by its **original** URL, so templates are never edited and "Restore original" is exact.
* The inventory (`site_image_slots`) fills itself whenever a page is rendered; **Rescan website** (or `php artisan images:scan`) visits every published page.
* A replacement (`site_image_overrides`) has a **scope**: *this page only* (default) or *everywhere this image is used*. Page-specific wins over global. Nothing is replaced on other pages unless "everywhere" is chosen.
* Per image: replacement (Media Library or upload), mobile image (`<picture>` / media query below 768px), alt, title, object-position, object-fit, hide, restore.
* Uploads: JPG/PNG/WebP/AVIF/GIF/SVG, 10 MB max, de-duplicated by content hash, downsized to 2400px, WebP srcset variants (480/960/1600), SVG scripts stripped. Deleting a file that is in use returns 409 and needs explicit confirmation; forcing it restores the originals.
* Visual page editor (`/pages/{id}/editor`): `<img>` clicking already existed; CSS background images now get a "Change background image" button (homepage printer hero is the test case).

## Numbers (dev database, 2026-11-18)
| | |
|---|---|
| Public pages scanned | 16 (home, 5 service pages, about, contact, portfolio, uslugi, CenoBots landing, /modeli + 5 model pages) |
| Image slots found | 142 (140 `<img>`, 1 inline CSS background, 1 stylesheet background) on the 16 pages |
| Already editable before (visual editor, `<img>` inside `<main>`) | 93 |
| Needed integration | 49 = 47 `<img>` outside `<main>` (navbar/footer/CenoBots header+footer) + 2 backgrounds (homepage printer hero, homepage hero `.intro-header` from the compiled CSS) |
| Connected to Admin now | 142 / 142 |

## Why they were not editable
* Header/footer/navbar images: the editor only edits `<main>`.
* Homepage printer hero: it is a CSS `background-image` on a `<div>`; the editor only handled `<img>`.
* `/modeli/{model}`: five URLs share one Page record, so an editor image edit hit all of them (use Site Images page scope to change one model).

## Not editable / caveats
* 14 referenced files do not exist on disk (already broken before; now replaceable): `assets/pdf/cenotbots/Authorized Distributor CenoBots IT Plus.jpg`, `assets/images/cenobots/cenno/hero-robot.jpg`, `.../opt/*` (9 webp), `assets/images/warehouse-sweeper.jpg`, `assets/images/hotel.jpg`, a `.png` GUID file.
* Stylesheet backgrounds are only supported for classes listed in `config/site_images.php` (currently `.intro-header`, the homepage hero).
* Not covered: images created by JavaScript at runtime, `og:image`/favicons in `<head>`, inline `<svg>`, `srcset`/`<source>` entries of existing `<picture>`s, images whose URL has no image extension.
* The same file used twice on one page shares one replacement (per page, not per occurrence).
* Unpublished pages are not scanned until viewed.

## Files
New: `app/Models/SiteImage{Slot,Override}.php`, `app/Services/SiteImages/{SiteImages,MediaOptimizer,MediaUsage,SiteScanner}.php`, `app/Http/Middleware/ApplySiteImages.php`, `app/Http/Controllers/Admin/SiteImageController.php`, `app/Console/Commands/ScanSiteImages.php`, `resources/views/admin_pages/site-images/index.blade.php`, migration `2026_11_19_000001_create_site_images_tables.php` (tables `site_image_slots`, `site_image_overrides`), `tests/Feature/SiteImagesTest.php`.
New config: `config/site_images.php`.
Changed: `bootstrap/app.php`, `routes/web.php`, `admin_partials/sidebar.blade.php`, `Admin/MediaController.php` (optimizer, dedupe, usage check, dimensions), `media/index|show.blade.php` (confirm in-use delete), `app/Services/InlineEdits.php` + `resources/js/page-editor.js` (background images).

## Tests
`SiteImagesTest` – 16 tests (inventory, printer replace/restore, page vs global scope, alt/title/position/mobile, dedupe/optimise/dimensions, SVG sanitising, in-use delete, permissions, editor background edits): all pass. Run with sqlite (`DB_CONNECTION=sqlite DB_DATABASE=:memory:`); the suite otherwise wipes the dev DB. 28 other tests fail identically with and without these changes (sqlite user/role FK).
