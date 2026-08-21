@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'autocomplete' => null,
    'accept' => null,
    'help' => null,
    'full' => false,
])

@php
    $id = str($name)->replace('_', '-')->toString();
    $isInvalid = $errors->has($name);
@endphp

<div {{ $attributes->class(['volunteer-field', 'volunteer-field--full' => $full]) }}>
    <label for="{{ $id }}">
        {{ $label }}

        @if ($required)
            <span>{{ __('volunteer.form.required') }}</span>
        @endif
    </label>

    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" @required($required) @class(['is-invalid' => $isInvalid])>{{ old($name) }}</textarea>
    @else
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $type === 'file' ? '' : old($name) }}"
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($accept) accept="{{ $accept }}" @endif
            @required($required)
            @class(['is-invalid' => $isInvalid])
        >
    @endif

    @if ($help)
        <span class="volunteer-hint">{{ $help }}</span>
    @endif

    @error($name)
        <span class="volunteer-field-error">{{ $message }}</span>
    @enderror
</div>
