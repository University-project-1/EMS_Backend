<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __('volunteer.page.intro') }}">
    <title>@yield('title', __('volunteer.page.title'))</title>
    @vite(['resources/css/app.css'])
</head>
<body class="volunteer-page">
    @yield('content')
</body>
</html>
