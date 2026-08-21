<nav class="volunteer-language-switcher" aria-label="{{ __('volunteer.language.switcher_label') }}">
    @foreach (['ar', 'en'] as $locale)
        <a
            href="{{ route('volunteer.application.locale', ['locale' => $locale]) }}"
            hreflang="{{ $locale }}"
            lang="{{ $locale }}"
            @class(['is-active' => app()->isLocale($locale)])
            @if (app()->isLocale($locale)) aria-current="true" @endif
        >
            {{ __('volunteer.language.'.$locale) }}
        </a>
    @endforeach
</nav>
