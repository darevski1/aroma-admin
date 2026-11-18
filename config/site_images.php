<?php

/**
 * Background images that live in compiled CSS files (not in the HTML), so the scanner cannot see them.
 * Each entry becomes an editable image on every page whose markup uses the CSS class; the replacement is
 * applied as a small <style> override, the stylesheet itself is never touched.
 */
return [
    'css_backgrounds' => [
        ['class' => 'intro-header', 'src' => '/assets/images/logo/hero-bg-fi24CLXc.jpg', 'label' => 'Homepage hero background'],
    ],
];
