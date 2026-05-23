@php
    $seoTitle = $seoTitle ?? 'Olives BI - Environmental Protection Intelligence';
    $seoDescription = $seoDescription ?? 'Olives BI helps governments, communities, and businesses protect land, forests, wetlands, rivers, air quality, and revenue systems with environmental intelligence.';
    $seoKeywords = $seoKeywords ?? 'Olives BI, environmental intelligence, land management, air quality monitoring, waste management, GIS, local government revenue, Ghana, wetlands, forest reserves';
    $seoUrl = $seoUrl ?? url()->current();
    $seoImage = $seoImage ?? asset('assets/images/locality/ramsar-wetland.jpg');
    $seoType = $seoType ?? 'website';
    $seoRobots = $seoRobots ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    $seoPublishedAt = $seoPublishedAt ?? null;
    $seoModifiedAt = $seoModifiedAt ?? now()->toIso8601String();
    $seoSchema = $seoSchema ?? [];
    $seoMainEntity = $seoMainEntity ?? null;
    $organizationSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Olives BI',
        'url' => route('auth.LandingPage'),
        'logo' => asset('assets/assetsfront_old/images/logo/ipts-logo-main.png'),
        'description' => 'Environmental protection and revenue intelligence platform for public institutions, communities, and businesses.',
        'sameAs' => [
            'https://github.com/stephendonkor003/olivesbi',
        ],
    ];
    $webSiteSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Olives BI',
        'url' => route('auth.LandingPage'),
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => route('locality.check').'?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];
    $pageSchema = array_merge([
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $seoTitle,
        'description' => $seoDescription,
        'url' => $seoUrl,
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => 'Olives BI',
            'url' => route('auth.LandingPage'),
        ],
        'about' => [
            'environmental intelligence',
            'geospatial data',
            'land revenue',
            'waste management',
            'air quality',
        ],
    ], $seoSchema);

    if ($seoMainEntity) {
        $pageSchema['mainEntity'] = $seoMainEntity;
    }
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ $seoKeywords }}">
<meta name="robots" content="{{ $seoRobots }}">
<meta name="author" content="Olives BI">
<meta name="publisher" content="Olives BI">
<meta name="application-name" content="Olives BI">
<meta name="theme-color" content="#1a5c2a">
<meta name="format-detection" content="telephone=yes">
<link rel="canonical" href="{{ $seoUrl }}">

<meta property="og:locale" content="en_GH">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:site_name" content="Olives BI">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:alt" content="Olives BI environmental intelligence platform">
@if ($seoPublishedAt)
<meta property="article:published_time" content="{{ $seoPublishedAt }}">
@endif
<meta property="article:modified_time" content="{{ $seoModifiedAt }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">
<meta name="twitter:image:alt" content="Olives BI environmental intelligence platform">

<script type="application/ld+json">
{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($webSiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($pageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
