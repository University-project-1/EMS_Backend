@extends('layouts.volunteer')

@section('title', __('volunteer.received.title'))

@section('content')
    <main class="volunteer-received">
        <section class="volunteer-received-card" aria-labelledby="received-title">
            <div class="volunteer-received-header">
                <x-volunteer.brand variant="received" />
            </div>
            <h1 id="received-title">{{ __('volunteer.received.title') }}</h1>
            <p>{{ __('volunteer.received.message') }}</p>
            <a href="{{ route('volunteer.application.create') }}">{{ __('volunteer.received.back') }}</a>
        </section>
    </main>
@endsection
