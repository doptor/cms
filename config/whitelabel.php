<?php

// config/whitelabel.php
// ─────────────────────────────────────────────────────────────────
// RyaanCMS White-Label Configuration
// Agencies can customize branding per deployment or per domain.
// ─────────────────────────────────────────────────────────────────

return [

    // Enable white-label mode
    'enabled' => env('WHITELABEL_ENABLED', false),

    // Default branding (used when no domain-specific config found)
    'default' => [
        'name'          => env('WHITELABEL_NAME',  'RyaanCMS'),
        'tagline'       => env('WHITELABEL_TAGLINE','AI-Powered Laravel CMS'),
        'logo'          => env('WHITELABEL_LOGO',  null),           // URL to custom logo
        'favicon'       => env('WHITELABEL_FAVICON',null),
        'color'         => env('WHITELABEL_COLOR', '#6c63ff'),      // Primary color
        'color2'        => env('WHITELABEL_COLOR2', '#00d4aa'),     // Accent color
        'hide_branding' => env('WHITELABEL_HIDE',  false),         // Hide "Powered by RyaanCMS"
        'support_email' => env('WHITELABEL_EMAIL', null),
        'support_url'   => env('WHITELABEL_SUPPORT_URL', null),
        'docs_url'      => env('WHITELABEL_DOCS_URL', null),
        'custom_css'    => env('WHITELABEL_CSS', null),            // URL to custom CSS file
    ],

    /*
    |─────────────────────────────────────────────────────────────────
    | Multi-tenant domain example:
    |─────────────────────────────────────────────────────────────────
    | Store these in the `whitelabel_configs` database table:
    |
    | domain: 'cms.myagency.com'
    | config: {
    |   "name": "AgencyCMS",
    |   "color": "#e74c3c",
    |   "logo": "https://myagency.com/logo.png",
    |   "hide_branding": true
    | }
    |─────────────────────────────────────────────────────────────────
    */
];
