@extends('layouts.volunteer')

@section('content')
    <header class="volunteer-hero">
        <div class="volunteer-shell volunteer-hero-content">
            <x-volunteer.brand
                :heading="__('volunteer.page.title')"
                :intro="__('volunteer.page.intro')"
            />
            <x-volunteer.language-switcher />
        </div>
    </header>

    <main class="volunteer-shell volunteer-main">
        @include('volunteer.partials.event-information')
        @include('volunteer.partials.application-form')
    </main>
@endsection
