# EMS Backend - AI Project Context

Last audited: 2026-08-14
Document version: 2.1
Audit status: Source, routes, live schema, tests, and static analysis reviewed after the Priority 0 repair pass
Authoritative product source: Current routes and code behavior
Historical requirements reference: C:\Users\LAPTOP KING\Desktop\EMS\SRS.pdf (8 pages; known to be incomplete/inaccurate and not authoritative)

This document is the current handover for the EMS backend. It replaces older progress claims that no longer matched the repository.

## 1. How to Read Feature Status

- Implemented: The backend route and supporting code exist for the core requirement.
- Partial: Useful implementation exists, but its code-defined workflow is incomplete or has a known defect.
- Missing: No usable implementation was found.
- Frontend/external: The backend exposes supporting data, but the actual client, interactive UI, or third-party workflow is outside this repository.
- Code implementation is the source of truth for current features. The SRS traceability section is retained only as historical context until the SRS is rewritten from the code.
- Source-complete is not the same as production-ready. The verification and known-issues sections below determine current engineering priority.

## 2. Current Technical Snapshot

Laravel Boost application-info reported:

| Component | Current version/state |
|---|---|
| PHP | 8.3 |
| Laravel | 13.13.0 |
| Database | MySQL |
| Passport | 13.7.5 |
| Socialite | 5.27.0 |
| Boost | 2.4.8 |
| Pest | 4.7.2 |
| PHPUnit | 12.5.28 |
| Larastan | 3.10.0 |
| Pint | 1.29.1 |
| Telescope | 5.20.0 |
| Tailwind CSS | 4.3.0 |

Repository inventory:

| Area | Count |
|---|---:|
| Non-vendor routes | 158 |
| API routes | 156 |
| Admin API routes | 60 |
| Exhibitor API routes | 52 |
| Visitor API routes | 37 |
| Visitor auth routes | 7 |
| Controllers | 45 |
| Form Requests | 35 |
| API Resources | 30 |
| Services | 34 |
| Eloquent models | 23 |
| Policies | 6 |
| Migrations | 33 |
| Seeders | 17 |
| Test files ending in Test.php | 10 |

OpenAPI:

- Scramble is installed and secures the generated specification with bearer authentication.
- api.json currently contains 140 documented path templates.
- Interactive API documentation is expected at /docs/api.
- api.json is generated output and should be refreshed after route or contract changes.

## 3. Product and Actors

EMS is an Exhibition Management System with three backend actors:

1. Visitor
   - Model: App\Models\User
   - Guard: mobile
   - Intended consumer: mobile application

2. Exhibitor
   - Model: App\Models\SystemUser with type exhibitor
   - Guard: system
   - Intended consumer: exhibitor web dashboard

3. Administrator
   - Model: App\Models\SystemUser with type admin
   - Guard: system
   - Intended consumer: admin web panel

The repository is primarily an API backend. It contains only minimal web views:

- resources/views/welcome.blade.php
- resources/views/scan-landing.blade.php

The mobile app, admin panel, exhibitor dashboard, and interactive exhibition map are not implemented here.

## 4. Architecture to Preserve

The established project pattern is:

Request -> Form Request -> DTO -> Service -> Eloquent Model -> API Resource

Important conventions:

- Separate BFF-style controllers and resources for Admin, Exhibitor, Visitor, and Shared concerns.
- Use Passport bearer tokens with mobile and system guards.
- Use SystemUserType to distinguish admin and exhibitor accounts.
- Keep business workflows in services; controllers should stay thin.
- Use Form Requests and validated data. Avoid request all-data mass assignment.
- Use DTOs for structured service input, including PatchDTO and HasUpdatePayload for partial updates.
- Use Spatie QueryBuilder for list filters, includes, sorting, and pagination.
- Put non-trivial filters in app/Filter classes.
- Return API responses through successResponse and errorResponse.
- Use Eloquent API Resources to keep actor-specific response contracts.
- Use policies and Gate checks for object ownership and team operations.
- Use transactions, row locks, cache locks, and post-commit notifications for multi-record workflows.
- Use Spatie Media Library for avatars, company media, event logos, and generated QR codes.
- Use backed enums for domain statuses and types.
- Preserve URL versioning under /api/v1.
- Treat existing migrations as immutable after deployment; create new migrations for changes.
- Before Laravel code changes, use Laravel Boost search-docs for installed-version documentation.
- After PHP changes, run vendor/bin/pint --dirty --format agent.

## 5. Authentication, Authorization, and Shared Infrastructure

### Implemented

- Visitor registration by phone with a six-digit OTP sent through the queued UltraMsg WhatsApp job.
- OTP hashes, expiration, attempt limits, cooldown, daily generation limit, database row locks, and cache locks.
- Visitor phone/password login, logout, password reset, profile update, avatar, password change, and phone-change OTP flow.
- Exhibitor registration, email verification, email/password login, logout, password reset, and profile/avatar update.
- Google token login through Socialite userFromToken.
- Admin email/password login, logout, password reset, profile, and password change.
- Passport access tokens for User and SystemUser.
- FCM device-token registration endpoints for all actors.
- Admin routes enforce type.admin; every authenticated exhibitor route now enforces type.exhibitor.
- Shared FCM registration resolves both the device-token relation and OAuth access-token id from the guard selected by the route.
- Database notifications with list, statistics, mark one read, mark all read, and delete operations.
- Queued email/database/FCM notifications for booking requests, booking decisions, reports, reviews, invitations, and announcements.
- Central JSON exception envelope in bootstrap/app.php when the request expects JSON.
- English and Arabic localization files.

### Partial or risky

- User and SystemUser use Passport HasApiTokens but do not implement the OAuthenticatable contract described by current Passport guidance. Validate this integration before treating authentication as fully hardened.
- config/passport.php names web as the Passport guard even though only mobile and system guards are defined.
- Admin login and exhibitor login/register/Google auth are not route-throttled.
- phone_update_request is defined but not attached to the visitor phone-request route.
- The email verification route is not protected by Laravel signed middleware; it relies on the user id plus the email SHA-1 hash.
- FcmChannel catches and logs all send failures without rethrowing, preventing queue retries for those failures.
- Queue after_commit is false globally, so each transactional dispatch must explicitly use afterCommit or DB::afterCommit.

## 6. Historical SRS Functional Traceability (Non-authoritative)

This section records how the old SRS compares with the current backend. It must not be used to classify an implemented behavior as defective merely because the documents differ. Product scope and the next SRS revision should be derived from verified code behavior first.

### 6.1 Admin Module

| Requirement | Status | Current evidence and remaining gap |
|---|---|---|
| FR-ADM-01 - Admin login to the web system | Implemented | POST /api/v1/admin/login, Passport token, admin-only protected group. The actual web panel is external. |
| FR-ADM-02 - Accept or reject booth booking requests | Implemented | Request index/show/stats, conflict preview, approve/reject, company/booth assignment, competing-request rejection, QR creation, and notifications. |
| FR-ADM-03 - Accept or reject event requests | Implemented | Request index/show/stats, overlap checks, row locking, approve/reject, competing-request rejection, QR creation, and notifications. |
| FR-ADM-04 - Review volunteers imported from Google Forms | Missing | No volunteer model, import, connector, route, or review workflow exists. |
| FR-ADM-05 - Email communication portal with exhibitors | Missing | Automated verification, invitation, password, and status emails exist, but there is no admin-to-exhibitor communication portal or message workflow. |
| FR-ADM-06 - Dashboard and general booking/exhibition statistics | Implemented | Cached admin dashboard includes summaries, daily trends, and breakdowns. Separate booth, event, report, visitor, and directory statistics also exist. |
| FR-ADM-07 - Manage booth information and status | Partial | Admin can list, show, filter, and update number, area, price, svg_id, and qr_token. Booth allocation is managed by approval. No admin create/delete endpoint or explicit booth status field exists. |
| FR-ADM-08 - Manage booth-service data, prices, and availability | Implemented | The code-defined API supports list/show/create/update and is_active. Routes are restricted to those four implemented API actions; no delete feature is claimed. |

### 6.2 Exhibitor Module

| Requirement | Status | Current evidence and remaining gap |
|---|---|---|
| FR-EXH-01 - Secure account creation and login | Implemented | Email registration/verification, email/password login, Google login, invite registration, logout, reset password, Passport tokens, and authenticated exhibitor-role enforcement. |
| FR-EXH-02 - View and manage personal profile | Implemented | Shared profile show/update, avatar media, password change, and reset flow. |
| FR-EXH-03 - View exhibition and contact information | Partial | Authenticated halls, booths, facilities, event halls, announcements, services, and nearest-event data exist. No dedicated exhibition-information or contact-details endpoint exists. |
| FR-EXH-04 - Interactive map, available booth selection, and booking request | Backend implemented / frontend external | Booth/hall/svg data, availability filters, company creation/selection, service selection, price snapshot, and booking request exist. The interactive map UI is not in this repo. |
| FR-EXH-05 - Manage approved booth and company data | Partial | Exhibitors can list owned booths and view company profiles. They cannot update company or booth details. |
| FR-EXH-06 - Create events inside the exhibition | Implemented | Event creation with organizer/company, hall, time, duration, speakers, logo, pending approval, calendar, list, nearest, and statistics. No exhibitor edit/cancel flow exists. |
| FR-EXH-07 - View interested visitors/leads | Implemented | Booth/event lead counts, visitor lists, and weekly series are implemented; event route binding uses App\Models\Event. |
| FR-EXH-08 - General booth and event statistics | Partial | Event request statistics and per-booth/per-event lead analytics exist. There is no unified exhibitor dashboard covering all booths and events. |
| FR-EXH-09 - Email communication with admin | Missing | No interactive contact-admin or message workflow exists. Automated system email is not a replacement. |
| FR-EXH-10 - Manage companies/booths and invite team members | Partial | Company and booth invitations can be sent, viewed, accepted, rejected, canceled, and used for registration. Direct company/booth editing is missing. |

### 6.3 Visitor Module

| Requirement | Status | Current evidence and remaining gap |
|---|---|---|
| FR-VIS-01 - Static buses catalog | Implemented | Authenticated visitor bus catalog list; admin buses CRUD is implemented. |
| FR-VIS-02 - Secure visitor registration and login | Implemented | OTP registration, phone/password login, logout, reset password, token issuance, and throttling. |
| FR-VIS-03 - View and edit profile | Implemented | Profile show/update, avatar, password update, and phone update verification. |
| FR-VIS-04 - Save companies or events | Partial | Events and booths can be toggled as saved. Companies cannot be saved. Only a saved-booths list exists; there is no saved-events list. |
| FR-VIS-05 - View map and booth locations | Backend implemented / frontend external | Hall, booth, facility, event-hall, and svg_id data are exposed. The interactive mobile map is outside this backend. |
| FR-VIS-06 - Receive announcements and exhibition notifications | Implemented | Audience-targeted announcements, database notifications, FCM delivery, token registration, read state, statistics, and delete. |
| FR-VIS-07 - Review companies and events | Partial | Visitors can create/update one review per booth or event, list reviews, and delete their own review. Company reviews are not implemented; the backend models the reviewed exhibition presence as a booth. |
| FR-VIS-08 - View scan history | Partial | QR resolution, lead registration, and scan-history resource exist. The history controller passes an unexecuted relationship builder and does not eager-load polymorphic targets; endpoint behavior and N+1 performance need correction. |
| FR-VIS-09 - Submit a report about a booth or event | Partial | Visitor report creation and admin list/show/stats/resolve/reject are implemented with notifications. Validation does not require exactly one of event_id or booth_id, so an empty target can reach persistence and fail. |

## 7. SRS Non-Functional Requirements

| Requirement | Status | Assessment |
|---|---|---|
| NFR 4.1 - Performance | Partial | Resources, pagination, cursor pagination, eager loading, aggregates, query filters, and selected caching are used. Several unpaginated lists, N+1 paths, and no measured performance target remain. |
| NFR 4.2 - Security | Partial | Bearer tokens, hashed passwords/OTPs, validation, admin/exhibitor role middleware, policies, and throttling exist. Incomplete throttling, unsigned verification, and remaining object/visibility review prevent production-hardening completion. |
| NFR 4.3 - Android/iOS compatibility | Frontend/external | No mobile client is in this repository, so compatibility cannot be verified here. |
| NFR 4.4 - Transaction integrity | Partial | Core OTP, auth, booth request, event request, invitation, review, report, announcement, and Google-login workflows use transactions and some row locks. Not every multi-step path is equally protected. |
| NFR 4.5 - Resource optimization | Partial | with, withCount, withAvg, withExists, cursor pagination, chunkById, dashboard caching, lookup caching, and locks are used. Lazy-loading prevention is not enabled; known N+1 paths and cache-invalidation gaps remain. |
| NFR 4.6 - API documentation | Partial | Scramble and api.json exist. The generated file has 140 path templates while the repo currently has 156 API routes; it should be regenerated after the route repair. No Postman collection exists. |
| NFR 4.7 - Rate limiting | Partial | Named limiters cover visitor auth, OTP, password/profile updates, and reports. They do not cover every API user or all system-user auth operations. |
| NFR 4.8 - Scalability and maintainability | Partial | Layered services, DTOs, requests, resources, filters, enums, and actor-separated controllers are established. Two failing/erroring tests, 281 static-analysis errors, inconsistent controller patterns, and deployment risks remain. |

## 8. Implemented Feature Inventory

### 8.1 Admin

- Authentication, password reset/change, logout, profile, and FCM registration.
- Cached dashboard:
  - Visitors and companies totals/period counts
  - Booth allocation
  - Pending booth requests
  - Upcoming events
  - Open reports
  - Lead totals
  - Daily visitor/company/request/lead/event trends
  - Gender, booth allocation, and request-status breakdowns
- Booth inventory list/show/update with filters, includes, sorting, and pagination.
- Booth request list/show/statistics/approve/reject/conflict response.
- Company directory list/show.
- Manager directory list/show/statistics.
- Visitor directory list/statistics.
- Event hall list/show/update.
- Event request list/show/statistics/approve/reject/conflict handling.
- Announcement list/show/create/update/delete with audience notifications.
- Bus catalog list/show/create/update/delete.
- Report list/show/statistics/resolve/reject.
- Service list/show/create/update. These are the four intentional API actions currently implemented.
- Hall and facility read endpoints.
- Shared notification inbox operations.

### 8.2 Exhibitor

- Registration, email verification/resend, invite registration, Google login, password reset/change, profile, logout, and FCM registration.
- Booth browse/show, booking request, selected services, new/existing company, and owned-booth list.
- Company profile read.
- Company/booth invitation lists and full invite lifecycle.
- Accessible company/booth/event lookup endpoints.
- Announcement and active-service lists.
- Booth/event lead analytics.
- Event hall and facility reads.
- Event create, list, calendar, nearest, and request statistics.
- Booth/event review analytics.
- Shared notification inbox operations.

### 8.3 Visitor

- OTP registration and login/password/profile flows.
- Company list/show.
- Booth list/show.
- Event list/show/nearest with saved state and filters.
- Hall, event hall, facility, and bus-catalog reads.
- Announcement list.
- Save toggle for booths/events and saved-booth list.
- Review create/update, list, and own-review delete.
- Report creation.
- QR lead registration and scan-history endpoint.
- Notification inbox operations.
- FCM token registration.

### 8.4 Notifications

The application has queued notification classes for:

- Email verification
- Password reset
- Team invitations
- New booth/event booking requests to admins
- Booth request approved/rejected
- Event request approved/rejected
- New visitor reports to admins
- New visitor reviews to exhibitors
- Announcement created/updated

Channels include database, mail where appropriate, and a custom FCM channel.

## 9. Data Model and Persistence

The live Boost schema confirms the domain tables are migrated in MySQL.

Core auth:

- users
- system_users
- oauth_access_tokens, oauth_refresh_tokens, oauth_clients, oauth_auth_codes, oauth_device_codes
- otp_codes
- device_tokens
- password_reset_tokens
- sessions

Exhibition and booking:

- companies
- company_system_users
- halls
- booths
- booth_system_users
- booth_requests
- services
- booth_request_services

Events and venue:

- event_halls
- events
- event_speakers

Visitor engagement and support:

- announcements
- bus_catalogs
- facilities
- leads
- saved
- reviews
- reports
- invitations
- notifications
- media

Infrastructure:

- jobs
- failed_jobs
- job_batches
- cache and cache_locks
- Telescope tables

Important domain behavior:

- Booth numbers are unique per hall.
- Booth and event QR tokens are unique and are generated on approval.
- Event organizers are polymorphic: SystemUser or Company.
- Leads are polymorphic: Booth or Event.
- Saved items are polymorphic: currently Booth or Event.
- Reviews are polymorphic: currently Booth or Event.
- Reports use polymorphic reporter and reportable relations.
- Invitations target Company or Booth.
- Company and booth team membership use pivot tables with assigned_by.
- Most core domain models use soft deletes.

## 10. External Services and Runtime Dependencies

| Integration | State |
|---|---|
| Passport OAuth2 | Integrated with mobile and system guards; configuration concerns remain |
| Google Socialite | Token-to-user login implemented |
| UltraMsg WhatsApp | OTP job implemented and queued |
| Firebase Cloud Messaging | Custom channel and guard-correct device-token storage implemented; delivery retry/failure handling still needs hardening |
| Laravel Mail | Verification, reset, invitations, and request-status email notifications |
| Spatie Media Library | Avatars, company logo/gallery, event logo, and QR SVG storage |
| Spatie QueryBuilder | Filtering, includes, sorting, and pagination |
| Endroid QR Code | QR SVG generation |
| Scramble | OpenAPI generation and interactive docs |
| Telescope | Development diagnostics |

Production configuration remains environment-dependent:

- The default mailer fallback is log.
- The default queue connection fallback is database.
- The default cache fallback is database.
- Firebase credentials must be supplied.
- UltraMsg credentials must be supplied.
- Passport keys/clients must be provisioned.
- A long-running queue worker is required for queued notifications and OTP delivery.

## 11. Verification Baseline

These results were collected on 2026-08-14 after the Priority 0 repair pass.

### Routes

Command:

    php artisan route:list --json --except-vendor

Result:

- 158 non-vendor routes
- 156 API routes
- 60 admin routes
- 52 exhibitor routes
- 37 visitor routes
- 7 visitor auth routes
- Admin services now expose exactly index, store, show, and update.

### Tests

Command:

    php artisan test --compact

Result:

- 22 tests discovered
- 20 passed
- 1 failed
- 1 errored
- 283 assertions

Focused Priority 0 regression run:

- 8 tests passed
- 215 assertions
- Covers authenticated exhibitor middleware, admin rejection, legitimate exhibitor access, system-guard FCM registration, successful system-user password change, event model binding, the admin service action set, and every registered controller action.

Current failures:

1. MapDataSeederTest expected 3 booth requests after repeated seeders but found 9.
2. MapDataSeederTest errored because EventSeeder could not find a SystemUser in one seed order.

Coverage is narrow:

- Seeders and map fixture data
- Notification payloads
- Announcement notification recipient behavior
- Default application smoke test

Missing test coverage includes:

- Visitor, exhibitor, and admin authentication
- Broader guard and object-level role boundaries beyond the new exhibitor regression cases
- OTP behavior and job failure paths
- Booth booking and approval races
- Event scheduling and approval races
- Invitations and ownership
- Saved/review/report validation
- Visitor/exhibitor FCM token registration beyond the new system-guard regression case
- Most API filters and pagination
- Policies and forbidden access
- Scheduled commands and deployment

### Static Analysis

Command:

    vendor/bin/phpstan analyse --no-progress --error-format=table

Result:

- Failed with 281 reported errors.
- Some are missing type metadata or generic QueryBuilder/Resource inference issues.
- No PHPStan error is currently reported in the files changed for the Priority 0 fixes.

### Formatting

Command:

    vendor/bin/pint --dirty --format agent

Result:

- Passed and formatted the dirty PHP files.

## 12. Regenerated Problem Priorities

Priorities below are based on verified code behavior and operational impact, not on differences from the old SRS.

### Priority 0 - No known open blocker after this pass

The previous Priority 0 findings are resolved:

1. Every authenticated exhibitor route now includes type.exhibitor; an admin receives 403 and a verified exhibitor succeeds.
2. Shared FCM registration now reads both the user and OAuth access-token id from the selected guard.
3. Exhibitor event-lead binding uses App\Models\Event.
4. Admin service routing exposes only the four controller actions actually implemented: index, store, show, and update.
5. System-user password change no longer calls successResponse with the nonexistent code named argument.
6. A route-wide regression assertion confirms that every registered controller route targets a method that exists.

### Priority 1 - Fix next: broken requests or data/operational integrity

1. Report target validation permits a targetless report
   - StoreReportRequest allows both event_id and booth_id to be absent.
   - ReportService then selects Booth with a null reportable id, so an otherwise validated request can fail during persistence.

2. Visitor scan history does not execute/load the intended query correctly
   - Mobile LeadController passes the leads relationship builder directly to a resource collection.
   - ScanHistoryResource dereferences polymorphic leadable data, booth company media, and event media without eager loading.

3. Two documented filters target nonexistent columns
   - ManagerDirectoryController exposes filter[phone], but system_users has no phone column.
   - FaciltyController maps filter[type] to gender, but facilities stores type and has no gender column.
   - Supplying either affected filter can produce a database error.

4. Announcement and review API contracts contain executable typos/inconsistent values
   - StoreAnnouncementRequest accepts Exhibitors/visitors/all, update accepts exhibitors/visitors/all, and the enum uses singular values.
   - StoreAnnouncementRequest spells webp as webg, rejecting valid WebP uploads on create.
   - The exhibitor booth-review path is published as reviews/booht/{booth}.

5. Booth approval is not protected against concurrent approvals
   - Event approval locks the event hall before conflict checking.
   - Booth approval checks status before the transaction and does not lock the booth/request row before assigning the company.
   - Concurrent admin requests can race and overwrite allocation state.

6. Scheduled auto-deploy is unsafe as a production mechanism
   - app:auto-deploy runs git reset --hard origin/master, migrations, queue restart, and blocking queue:work.
   - It is scheduled every two hours without environment restriction, withoutOverlapping, onOneServer, or a deployment lock.

### Priority 2 - Security/reliability hardening and release quality

7. Rate limiting is incomplete
   - Admin login and exhibitor login/register/Google auth have no named throttle.
   - The visitor phone-request route does not use the already-defined phone_update_request limiter.
   - Most authenticated traffic has no general per-user limiter.

8. Invitation lists perform per-row queries and lazy loads
   - InvitaionResource runs a SystemUser existence query for every invitation.
   - List queries eager-load sender but not inviteable, which the resource dereferences.
   - Model::preventLazyLoading is not enabled to expose regressions during development.

9. Lookup caches can return stale authorization/ownership choices
   - Per-user company, booth, and event lookups are cached for 15 minutes.
   - Invitation acceptance, booking approval, and company/event creation do not invalidate those keys.

10. Queued external delivery needs bounded failure behavior
    - SendOtpWhatsappJob has fixed retries/backoff but no HTTP connect/read timeout, failed handler, uniqueness, or external rate-limit middleware.
    - FCM channel exceptions are logged and swallowed, so failed sends are not retried.
    - Global queue after_commit remains false, requiring every transactional dispatch to opt in correctly.

11. API exception JSON depends on the Accept header
    - The exception renderer uses expectsJson only.
    - An /api request without Accept: application/json may receive an HTML error response.

12. Passport integration and configuration need validation
    - User and SystemUser use HasApiTokens but do not implement the current OAuthenticatable contract.
    - config/passport.php names web as the guard although the application defines system and mobile Passport guards.

13. The release gate is not clean
    - The full suite has one failure and one error in MapDataSeederTest due to non-idempotent/order-dependent seeding.
    - PHPStan reports 281 issues; many are metadata/inference problems, but real runtime defects must be separated and fixed rather than suppressed.
    - api.json has not yet been regenerated after the route repair.

14. Saved-event support is asymmetric in the implemented API
    - Visitors can toggle saved events and booths.
    - Only saved booths have a retrieval endpoint, leaving saved events write-only from the API consumer's perspective.

### Product decisions - not classified as defects

- Visitor visibility of pending companies or unallocated booths needs an explicit product decision; the current map/browse code exposes them, so the old SRS alone is not evidence of a bug.
- Reviews currently target booths and events. Company reviews are not assumed missing unless product scope changes.
- Exhibitor company/booth editing, volunteer import, messaging portals, and other old-SRS items are candidates for future scope, not implementation defects without a new product decision.

## 13. Unimplemented or Incomplete Capabilities

### Confirmed from partial code paths

- Saved events can be toggled but cannot be retrieved through a saved-events endpoint.
- Visitor scan history has a route/resource but its query execution and relation loading are defective.
- Reports have a complete visitor-to-admin workflow, but target validation does not enforce a target.
- Announcement creation/update is implemented, but its receiver and media-validation contracts are inconsistent.
- API documentation exists but has not been regenerated against the repaired 156-route API surface.
- Authorization coverage exists for core models, but comprehensive forbidden-access tests do not.
- Production queue monitoring, bounded external-service retries, safe deployment, and a passing release gate remain incomplete.

### Not present in current code; product scope must be confirmed

- Exhibitor event update/cancel/delete.
- Company write APIs outside booking/event creation.
- Booth create/delete administration.
- Hall/facility administration and hall pricing workflows.
- Visitor report history/status for reporter follow-up.
- Volunteer/Google Forms ingestion and admin review.
- Admin/exhibitor interactive messaging portals.
- Exhibitor editing of approved company and booth profiles.
- Company saving and company reviews.
- Unified exhibitor dashboard across all booths/events.
- Postman collection.
- The mobile app, interactive map client, admin panel, and exhibitor panel; those clients are outside this backend repository.

The second list is descriptive, not an approved backlog. Confirm product intent from implemented behavior and stakeholders before building it, then rewrite the SRS accordingly.

## 14. Recommended Implementation Order

1. Fix directly broken request paths
   - Fix scan-history execution/eager loading.
   - Require exactly one report target.
   - Remove the nonexistent manager phone filter or add the intended schema.
   - Correct facility filtering, announcement values/WebP validation, and the booht route typo.

2. Protect data and operations
   - Add booth-row locking during approval.
   - Replace scheduled auto-deploy with controlled CI/CD or restrict it to an explicitly safe environment.
   - Expand object-level authorization tests beyond the new role-boundary cases.

3. Strengthen API reliability
   - Add cache invalidation.
   - Add system-auth and general API rate limits.
   - Force JSON rendering for /api routes.
   - Add timeouts, retry/failure handling, and observability for external services.

4. Build a real release gate
   - Fix the two failing tests.
   - Add feature tests for each implemented workflow and important role boundary.
   - Fix PHPStan real errors and improve generics/resource annotations.
   - Run Pint, tests, PHPStan, and Scramble export in CI.

5. Production hardening
   - Configure SMTP, Firebase, UltraMsg, Passport keys/clients, queue workers, and monitoring.
   - Validate Passport contracts/configuration and production cache/queue choices.

6. Confirm future product scope
   - Use current code behavior as the baseline.
   - Decide which unimplemented capabilities are actually required.
   - Rewrite the SRS after those decisions instead of treating the old document as authoritative.

## 15. Quick File Map

Requirements and handover:

- AI_CONTEXT.md
- C:\Users\LAPTOP KING\Desktop\EMS\SRS.pdf
- database_architecture.dbml
- api.json

Routing:

- routes/api.php
- routes/api/v1/admin.php
- routes/api/v1/exhibitor.php
- routes/api/v1/mobile.php
- routes/web.php
- routes/console.php

Authentication:

- config/auth.php
- config/passport.php
- app/Models/User.php
- app/Models/SystemUser.php
- app/Services/Mobile/AuthService.php
- app/Services/Mobile/OtpService.php
- app/Services/SystemUser/Exhibitor/AuthService.php
- app/Services/SystemUser/Exhibitor/GoogleAuthService.php

Core workflows:

- app/Services/SystemUser/Exhibitor/BoothRequestService.php
- app/Services/SystemUser/Admin/BoothRequestService.php
- app/Services/SystemUser/Exhibitor/EventService.php
- app/Services/SystemUser/Admin/EventRequestService.php
- app/Services/SystemUser/Exhibitor/InvitationService.php
- app/Services/Mobile/ReviewService.php
- app/Services/Mobile/ReportService.php
- app/Services/Mobile/LeadService.php
- app/Services/Mobile/SavedService.php

Cross-cutting:

- bootstrap/app.php
- app/Providers/AppServiceProvider.php
- app/Helper/ApiResponse.php
- app/Services/Shared/NotificationService.php
- app/Services/Shared/NotificationRecipientResolver.php
- app/Services/Shared/FCMService.php
- app/Channels/FcmChannel.php
- app/Jobs/SendOtpWhatsappJob.php

Verification:

- tests/
- phpunit.xml
- phpstan.neon
- .github/skills/

## 16. Standard Audit Commands

Use the Laragon PHP executable if php is not on PATH.

    php artisan route:list --except-vendor
    php artisan test --compact
    vendor/bin/phpstan analyse --no-progress
    vendor/bin/pint --dirty --format agent

For database inspection and version-specific documentation, prefer Laravel Boost:

- application-info
- database-schema
- database-query for read-only queries
- search-docs
- get-absolute-url before sharing project URLs

## 17. Final Handover Rule

Do not mark a feature complete only because a model, migration, route, or controller exists.

For future AI updates, require:

1. Route and implementation evidence.
2. Correct actor/role authorization.
3. Validation and transaction behavior.
4. A passing feature test for the main happy path and important failures.
5. Updated API documentation.
6. A code-first product decision before changing behavior merely to match the historical SRS.
