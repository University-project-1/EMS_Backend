<section class="volunteer-form-card" aria-labelledby="application-form-title">
    <h2 id="application-form-title">{{ __('volunteer.form.title') }}</h2>
    <p>{{ __('volunteer.form.subtitle') }}</p>

    @if ($errors->any())
        <div class="volunteer-error-summary" role="alert" tabindex="-1">
            {{ __('volunteer.form.error_summary') }}
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('volunteer.application.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="volunteer-honeypot" aria-hidden="true">
            <label for="website">{{ __('volunteer.form.website') }}</label>
            <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="volunteer-form-grid">
            <x-volunteer.form-field name="full_name" :label="__('volunteer.form.full_name')" autocomplete="name" required />
            <x-volunteer.form-field name="email" :label="__('volunteer.form.email')" type="email" autocomplete="email" required />
            <x-volunteer.form-field name="phone" :label="__('volunteer.form.phone')" type="tel" autocomplete="tel" required />
            <x-volunteer.form-field name="city" :label="__('volunteer.form.city')" autocomplete="address-level2" />
            <x-volunteer.form-field
                name="cv"
                :label="__('volunteer.form.cv')"
                type="file"
                :accept="'.pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'"
                :help="__('volunteer.form.cv_hint', ['size' => config('volunteer.cv_max_kilobytes') / 1024])"
                full
                required
            />
            <x-volunteer.form-field name="motivation" :label="__('volunteer.form.motivation')" type="textarea" :help="__('volunteer.form.motivation_hint')" full required />
            <x-volunteer.form-field name="education_or_occupation" :label="__('volunteer.form.education_or_occupation')" type="textarea" full required />
            <x-volunteer.form-field name="skills" :label="__('volunteer.form.skills')" type="textarea" :help="__('volunteer.form.skills_hint')" full />

            <div class="volunteer-field volunteer-field--full">
                <div class="volunteer-consent">
                    <input id="privacy-consent" name="privacy_consent" type="checkbox" value="1" @checked(old('privacy_consent')) required>
                    <div>
                        <label for="privacy-consent">{{ __('volunteer.form.privacy_consent') }} <span>{{ __('volunteer.form.required') }}</span></label>
                        <span class="volunteer-hint">{{ __('volunteer.form.privacy_note') }}</span>
                        @error('privacy_consent')
                            <span class="volunteer-field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="volunteer-submit-row">
            <button type="submit">{{ __('volunteer.form.submit') }}</button>
        </div>
    </form>
</section>
