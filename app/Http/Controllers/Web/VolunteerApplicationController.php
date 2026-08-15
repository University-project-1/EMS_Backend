<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVolunteerApplicationRequest;
use App\Services\Shared\VolunteerApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VolunteerApplicationController extends Controller
{
    private const SUPPORTED_LOCALES = ['ar', 'en'];

    public function __construct(private readonly VolunteerApplicationService $volunteerApplications) {}

    public function create(): View
    {
        return view('volunteer.apply');
    }

    public function store(StoreVolunteerApplicationRequest $request): RedirectResponse
    {
        $this->volunteerApplications->submit(
            $request->safe()->except(['cv', 'privacy_consent', 'website']),
            $request->file('cv')
        );

        return to_route('volunteer.application.received');
    }

    public function received(): View
    {
        return view('volunteer.received');
    }

    public function changeLocale(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            throw new NotFoundHttpException;
        }

        return redirect()
            ->to($this->safePreviousUrl())
            ->withCookie(cookie()->forever('volunteer_locale', $locale));
    }

    private function safePreviousUrl(): string
    {
        $previous = url()->previous();

        return Str::startsWith($previous, url('/'))
            ? $previous
            : route('volunteer.application.create');
    }
}
