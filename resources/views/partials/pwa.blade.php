{{-- PWA: manifest, theme color, icons --}}
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="{{ (isset($school) ? $school->primary_color : null) ?: '#16a34a' }}">
<link rel="apple-touch-icon" href="/icons/icon-192.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="SAS">
