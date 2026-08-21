@props([
    'heading' => null,
    'intro' => null,
    'variant' => 'hero',
])

<div {{ $attributes->class(['volunteer-brand', "volunteer-brand--{$variant}"]) }}>
    <img class="volunteer-brand-logo" src="{{ asset('images/ems-logo.jpg') }}" alt="{{ __('volunteer.page.eyebrow') }}">

    @if ($heading || $intro)
        <div class="volunteer-brand-copy">
            <p class="volunteer-eyebrow">{{ __('volunteer.page.eyebrow') }}</p>

            @if ($heading)
                <h1>{{ $heading }}</h1>
            @endif

            @if ($intro)
                <p class="volunteer-intro">{{ $intro }}</p>
            @endif
        </div>
    @endif
</div>
