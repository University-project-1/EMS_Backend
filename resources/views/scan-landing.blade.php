@php
    $isBooth = $leadable instanceof \App\Models\Booth;
    $isEvent = $leadable instanceof \App\Models\Event;
    $company = $isBooth ? $leadable->company : null;
    $companyName = $company?->name ?? 'Exhibitor';
    $hasCompanyLogo = filled($companyLogoUrl ?? null);
    $companyInitial = mb_strtoupper(mb_substr($companyName, 0, 1));
    $appUrl = config('app.frontend_url') ?: config('app.url');
    $displayTitle = $isBooth
        ? 'Discover this exhibitor in the app'
        : ($leadable->title ?? 'Discover Damascus International Fair');

    $socialLinks = collect(is_array($company?->social_links) ? $company->social_links : [])
        ->map(function ($url, $label): ?array {
            if (! is_string($url)) {
                return null;
            }

            $url = trim($url);
            $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

            if (! filter_var($url, FILTER_VALIDATE_URL) || ! in_array($scheme, ['http', 'https'], true)) {
                return null;
            }

            return [
                'label' => ucfirst(str_replace(['_', '-'], ' ', (string) $label)),
                'key' => strtolower((string) $label),
                'url' => $url,
            ];
        })
        ->filter()
        ->values();

    $websiteLink = $socialLinks->first(
        fn (array $link): bool => in_array($link['key'], ['website', 'web', 'site', 'url', 'website_url'], true)
    );

    $visibleSocialLinks = $socialLinks->reject(
        fn (array $link): bool => $websiteLink && $link['url'] === $websiteLink['url']
    );
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6f8fb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>{{ $displayTitle }}</title>
    <style>
        :root {
            --navy: #101827;
            --muted: #718096;
            --teal: #20a7a1;
            --teal-dark: #087f7a;
            --pink: #df0b82;
            --purple: #56328f;
            --surface: #ffffff;
            --background: #f6f8fb;
            --line: #e7ebf1;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(circle at 10% 0%, #e6f7f4 0, transparent 33%), linear-gradient(145deg, #f8fafc 0%, #eef8f7 100%);
            color: var(--navy);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .page { min-height: 100vh; padding: 42px 28px; }
        .layout { display: grid; width: min(100%, 1360px); min-height: 720px; margin: 0 auto; grid-template-columns: minmax(420px, .9fr) minmax(560px, 1.1fr); align-items: center; gap: clamp(58px, 7vw, 118px); }
        .intro { padding: 20px 0; }
        .logo { display: block; width: min(100%, 380px); height: auto; margin-bottom: 34px; mix-blend-mode: multiply; }
        .intro-kicker { margin: 0 0 16px; color: var(--teal-dark); font-size: 12px; font-weight: 900; letter-spacing: .14em; text-transform: uppercase; }
        .intro h1 { max-width: 600px; margin: 0; font-size: clamp(42px, 4.8vw, 70px); line-height: 1.02; letter-spacing: -.055em; }
        .intro-copy { max-width: 520px; margin: 25px 0 0; color: var(--muted); font-size: 18px; line-height: 1.75; }
        .brand-line { display: flex; align-items: center; gap: 9px; margin-top: 38px; color: #9aa6b5; font-size: 12px; font-weight: 700; }
        .brand-line::before { width: 34px; height: 2px; background: linear-gradient(90deg, var(--teal), var(--pink)); content: ""; }
        .card { overflow: hidden; border: 1px solid rgba(16, 24, 39, .07); border-radius: 28px; background: var(--surface); box-shadow: 0 25px 65px rgba(16, 24, 39, .12); }
        .accent { height: 7px; background: linear-gradient(90deg, var(--teal) 0 34%, var(--pink) 34% 65%, var(--purple) 65% 100%); }
        .content { padding: clamp(40px, 4.5vw, 64px); }
        .company-hero { display: flex; align-items: center; gap: 15px; margin-bottom: 26px; border-bottom: 1px solid var(--line); padding-bottom: 24px; }
        .company-logo { display: grid; width: 76px; height: 76px; flex: 0 0 76px; place-items: center; overflow: hidden; border: 1px solid #e6eef0; border-radius: 20px; background: linear-gradient(145deg, #f0fbf9, #fff5fb); box-shadow: 0 8px 18px rgba(16, 24, 39, .08); color: var(--teal-dark); font-size: 27px; font-weight: 900; }
        .company-logo img { width: 100%; height: 100%; object-fit: contain; padding: 8px; }
        .company-kicker { margin: 0 0 5px; color: var(--muted); font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .company-name { margin: 0; overflow: hidden; color: var(--navy); font-size: clamp(20px, 2.7vw, 28px); font-weight: 900; letter-spacing: -.035em; text-overflow: ellipsis; }
        .eyebrow { margin: 0 0 10px; color: var(--teal-dark); font-size: 12px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
        .content h2 { margin: 0; font-size: clamp(26px, 3vw, 39px); line-height: 1.12; letter-spacing: -.045em; }
        .lead { margin: 14px 0 27px; color: var(--muted); font-size: 15px; line-height: 1.7; }
        .summary { margin-bottom: 27px; border: 1px solid #dceceb; border-radius: 20px; background: linear-gradient(135deg, #f3fbfa, #fff9fc); padding: 7px; }
        .summary-heading { display: flex; align-items: center; justify-content: space-between; padding: 12px 15px 10px; color: var(--teal-dark); font-size: 11px; font-weight: 900; letter-spacing: .09em; text-transform: uppercase; }
        .summary-heading::after { width: 38px; height: 3px; border-radius: 99px; background: linear-gradient(90deg, var(--teal), var(--pink)); content: ""; }
        .summary-grid { display: grid; grid-template-columns: 1fr 1.7fr; gap: 7px; }
        .summary-row { display: flex; min-height: 82px; flex-direction: column; align-items: flex-start; justify-content: center; gap: 4px; border: 1px solid rgba(220, 236, 235, .8); border-radius: 15px; background: rgba(255,255,255,.85); padding: 13px 15px; }
        .summary-icon { display: none; }
        .label { margin: 0 0 3px; color: var(--muted); font-size: 11px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
        .value { margin: 0; overflow: hidden; font-size: 15px; font-weight: 800; text-overflow: ellipsis; white-space: nowrap; }
        .actions { display: grid; gap: 10px; }
        .cta, .secondary-cta { display: flex; width: 100%; align-items: center; justify-content: center; gap: 10px; border-radius: 15px; padding: 16px 18px; font-size: 15px; font-weight: 850; text-decoration: none; transition: transform .2s ease, box-shadow .2s ease, background .2s ease; }
        .cta { background: linear-gradient(135deg, var(--teal-dark), var(--teal)); box-shadow: 0 10px 20px rgba(8, 127, 122, .2); color: #fff; }
        .secondary-cta { border: 1px solid #cde6e3; background: #f4fbfa; color: var(--teal-dark); }
        .cta:hover, .secondary-cta:hover { transform: translateY(-1px); }
        .cta:hover { box-shadow: 0 13px 24px rgba(8, 127, 122, .27); }
        .secondary-cta:hover { background: #e7f7f4; }
        .arrow { font-size: 20px; line-height: 0; }
        .social-section { margin-top: 27px; border-top: 1px solid var(--line); padding-top: 22px; }
        .social-title { margin: 0 0 12px; color: var(--muted); font-size: 12px; font-weight: 800; }
        .socials { display: flex; flex-wrap: wrap; gap: 8px; }
        .social { display: inline-flex; align-items: center; border: 1px solid var(--line); border-radius: 999px; background: #fff; color: var(--navy); padding: 8px 12px; font-size: 12px; font-weight: 750; text-decoration: none; }
        .social:hover { border-color: #b7deda; color: var(--teal-dark); }
        .note { margin: 17px 0 0; color: var(--muted); font-size: 12px; line-height: 1.6; text-align: center; }
        @media (max-width: 880px) {
            .page { padding: 28px 20px; }
            .layout { width: min(100%, 1120px); min-height: 620px; grid-template-columns: minmax(310px, .85fr) minmax(430px, 1.15fr); gap: 42px; }
            .logo { width: min(100%, 300px); margin-bottom: 24px; }
            .intro-copy { font-size: 15px; }
            .intro h1 { font-size: clamp(38px, 5vw, 54px); }
            .content { padding: 36px; }
        }
        @media (max-width: 700px) {
            .page { padding: 16px 12px 26px; }
            .layout { display: block; min-height: 0; }
            .intro { padding: 0 8px 18px; text-align: center; }
            .logo { width: min(100%, 310px); margin: 0 auto 15px; }
            .intro-kicker { margin-bottom: 9px; font-size: 10px; }
            .intro h1 { margin: 0 auto; font-size: 31px; }
            .intro-copy { max-width: 360px; margin: 11px auto 0; font-size: 14px; line-height: 1.55; }
            .brand-line { justify-content: center; margin-top: 17px; font-size: 10px; }
            .card { border-radius: 23px; }
            .content { padding: 23px 20px 23px; }
            .summary-grid { grid-template-columns: 1fr 1.5fr; }
            .company-hero { margin-bottom: 22px; padding-bottom: 20px; }
            .company-logo { width: 64px; height: 64px; flex-basis: 64px; border-radius: 17px; }
            .company-name { font-size: 21px; }
            .content h2 { font-size: 27px; }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="layout">
            <section class="intro" aria-label="Damascus International Fair">
                <img class="logo" src="{{ asset('images/damascus-fair-logo.png') }}" alt="Damascus Fair Exhibitor Portal">
                <p class="intro-kicker">Damascus International Fair</p>
                <h1>Meet the exhibitors.<br>Explore the fair.</h1>
                <p class="intro-copy">Install the Damascus Fair app to discover exhibitors, find your way around, and get the complete visitor experience.</p>
                <div class="brand-line">Official Exhibitor Portal</div>
            </section>

            <section class="card" aria-labelledby="page-title">
                <div class="accent"></div>
                <div class="content">
                    @if ($isBooth)
                        <div class="company-hero">
                            <div class="company-logo">
                                @if ($hasCompanyLogo)
                                    <img src="{{ $companyLogoUrl }}" alt="{{ $companyName }} logo">
                                @else
                                    {{ $companyInitial }}
                                @endif
                            </div>
                            <div>
                                <p class="company-kicker">Exhibitor</p>
                                <p class="company-name">{{ $companyName }}</p>
                            </div>
                        </div>
                    @endif

                    <p class="eyebrow">Exhibitor information</p>
                    <h2 id="page-title">{{ $displayTitle }}</h2>
                    <p class="lead">The app gives you the full exhibitor profile, directions, and visitor services in one place.</p>

                    <div class="summary">
                        @if ($isBooth)
                            <div class="summary-heading">Visitor information</div>
                            <div class="summary-grid">
                                <div class="summary-row">
                                    <p class="label">Booth number</p>
                                    <p class="value">{{ $leadable->number ?? '—' }}</p>
                                </div>
                                <div class="summary-row">
                                    <p class="label">Exhibitor</p>
                                    <p class="value">{{ $companyName }}</p>
                                </div>
                            </div>
                        @elseif ($isEvent)
                            <div class="summary-heading">Event information</div>
                            <div class="summary-grid">
                                <div class="summary-row">
                                    <p class="label">Event</p>
                                    <p class="value">{{ $leadable->title ?? 'Damascus International Fair' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="actions">
                        <a class="cta" href="{{ $appUrl }}">Install app <span class="arrow">→</span></a>
                        @if ($websiteLink)
                            <a class="secondary-cta" href="{{ $websiteLink['url'] }}" target="_blank" rel="noopener noreferrer">Visit exhibitor website <span class="arrow">↗</span></a>
                        @endif
                    </div>

                    @if ($visibleSocialLinks->isNotEmpty())
                        <div class="social-section">
                            <p class="social-title">Follow {{ $companyName }}</p>
                            <div class="socials">
                                @foreach ($visibleSocialLinks as $socialLink)
                                    <a class="social" href="{{ $socialLink['url'] }}" target="_blank" rel="noopener noreferrer">{{ $socialLink['label'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <p class="note">The app is required to view full exhibitor details and directions.</p>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
