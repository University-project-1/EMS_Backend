# EMS Backend: Comprehensive AI Handover Document

**Last Updated:** 2026-07-10
**Status:** Functional Implementation (Auth/Profile, Admin Booths/BoothRequests/Services CRUD, Exhibitor Booth Booking, Team Invitations, and Visitor Booth/Hall lists are live. Event management pending).
**Laravel Version:** 13.7  
**PHP Version:** 8.3+  

---

## 1. Project Overview

**Exhibition Management System (EMS) Backend** is a Laravel REST API for managing events, exhibitions, booths, and visitor interactions.

**Three user types:**
- **Visitor (`User` model):** Mobile app users viewing exhibitions and events
- **Admin (`SystemUser` type=`admin`):** System administrators
- **Exhibitor (`SystemUser` type=`exhibitor`):** Companies managing booths and events

**Current Maturity:**
- ✅ Complete: Authentication (OTP, email verification, Google OAuth2), registration, profile management, FCM push tokens, rate limiting.
- ✅ Complete: Admin Booths, Booth Requests (booking approvals & rejections, conflict detection), and Services CRUD endpoints.
- ✅ Complete: Exhibitor Booth booking & request workflow, and Team Invitations system (inviting system users to booths or companies).
- ✅ Complete: Visitor/Visitor profile list & show endpoints for Booths & Halls with Spatie QueryBuilder filtering.
- ✅ Complete: Model authorization policies (`BoothPolicy`, `CompanyPolicy`) for invitation management and `type.admin` middleware.
- ❌ Missing: Event creation & attendance tracking, Company CRUD (outside of booth request flow), Report/complaint resolutions, Reviews, and Visitor engagement tracking (leads/saved items).

**API Documentation:** Available at `/docs/api` (Scramble-generated OpenAPI spec)

---

## 2. Architecture Overview

### 2.1 Core Design Patterns

**Triple-Guard Authentication (Passport OAuth2):**
- `mobile` guard: `User` model (mobile visitors)
- `system` guard: `SystemUser` model (admins/exhibitors)
- Both guards use **Passport** for token-based API authentication
- Token lifetimes configurable in [AppServiceProvider](app/Providers/AppServiceProvider.php)

**Service-Oriented Business Logic:**
- No repositories; services query models directly
- Services handle **transactions, caching, OTP generation, media uploads, email verification**
- DTOs carry validated request payloads; `HasUpdatePayload` trait tracks PATCH field changes

**Request→Service→Model Flow:**
1. **Form Request:** Validates payload, applies array notation or custom rules
2. **Controller:** Injects service, calls method with request data
3. **Service:** Manages transactions, state, notifications, error handling
4. **Model:** Persists data; handles relationships and casts

**API Routing & Consumers (BFF Pattern):**
- Strict separation of Controllers, Routes, and Resources based on the consumer (Admin, Exhibitor, Visitor).
- Example: Visitors have `VisitorBoothResource` which hides sensitive data like `price`.

**Filtering & Querying (Spatie QueryBuilder):**
- Strictly using `spatie/laravel-query-builder` for all GET list endpoints.
- Complex filtering logic must be extracted to Dedicated Custom Filters in `app/Filter/` (e.g., MinFilter, MaxFilter, BookedBoothFilter, DateFilter, CompanyNameFilter).
- Avoid inline closures or Local Scopes for Spatie filters.

**Exception Handling:**
- `ModelNotFoundException` is handled globally in `bootstrap/app.php` to return a standardized JSON response matching global helpers. No need for `if(!$model)` when Route Model Binding is used.

### 2.2 Passport Configuration

Located in [config/passport.php](config/passport.php):
- Default guard: `web` (for Laravel command execution only)
- API guards: `mobile`, `system` (defined in [config/auth.php](config/auth.php))
- Tokens expire in 15 days; refresh tokens in 30 days; personal access tokens in 6 months
- Scopes: Not yet implemented (all endpoints are scope-agnostic)

**OAuth Clients:**
- Two personal access clients created on first `php artisan migrate` (see [DatabaseSeeder](database/seeders/DatabaseSeeder.php))
- Device authorization flow available but not yet used

### 2.3 Rate Limiting (AppServiceProvider)

Custom rate limiters active on all auth routes:
- `login_register`: 10 req/min per IP
- `verify_otp`: 5 req/min per IP
- `forgot_password`: 3 req/hour per phone
- `profile_update`: 20 req/min per user
- `password_update`: 3 req/min per user
- `phone_update_request`: 2 req/hour per user

Rate limit violations return `429` with custom error message via global helper.

---

## 3. Folder Structure

```
app/
├── Console/Commands/
│   ├── CleanUnverifiedAccounts.php  [Scheduled daily: deletes expired/used OTPs]
│   └── AutoDeploy.php               [Scheduled every minute: git pull + cache clear]
├── DTOs/
│   ├── PatchDTO.php                 [Base class for PATCH operations with field tracking]
│   ├── Mobile/UpdateProfileDTO.php
│   ├── Shared/UpdatePasswordDTO.php
│   └── SystemUser/LoginDTO.php, RegisterDTO.php, ProfileUpdateDTO.php, BoothRequestDTO.php, BoothUpdateDTO.php, CompanyDTO.php, ServiceDTO.php, UpdateServiceDTO.php
├── Enum/                            [8 backing enums for domain validation]
│   ├── Status.php                   [pending, approved, rejected]
│   ├── SystemUserType.php           [admin, exhibitor]
│   ├── EventType.php                [conference, workshop, lecture, other]
│   ├── ReportStatus.php             [pending, resolved, rejected]
│   ├── Gender.php, AnnouncementReceiverType.php, DeviceType.php, HallType.php
├── Exceptions/ApiException.php      [Custom app exception handler]
├── Filter/                          [Custom Spatie QueryBuilder filters]
│   ├── BookedBoothFilter.php        [Filters booths by booking status]
│   ├── CompanyNameFilter.php        [Filters booths by associated company name]
│   ├── DateFilter.php               [Filters records by created_at date]
│   ├── MaxFilter.php                [Filters values less than or equal to max]
│   └── MinFilter.php                [Filters values greater than or equal to min]
├── Helper/ApiResponse.php           [Global successResponse(), errorResponse() helpers]
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── Mobile/
│   │   │   ├── AuthController        [register, verifyRegister, login, logout, resendOtp]
│   │   │   ├── ProfileController     [show, updateProfile, updatePassword, requestPhoneUpdate, verifyPhoneUpdate]
│   │   │   ├── PasswordController    [forgotPassword, verifyForgotPasswordOtp, resetPassword]
│   │   │   └── BoothController       [index, show]
│   │   ├── SystemUser/
│   │   │   ├── Admin/
│   │   │   │   ├── AuthController    [login, logout]
│   │   │   │   ├── BoothController   [index, show, update]
│   │   │   │   ├── BoothRequestController [index, show, approve, reject]
│   │   │   │   └── ServiceController [resource CRUD]
│   │   │   ├── Exhibitor/
│   │   │   │   ├── AuthController    [register, verify email, login, logout, googleAuth]
│   │   │   │   ├── BoothController   [index, show, book, ownedBooths]
│   │   │   │   ├── ServiceController [index]
│   │   │   │   └── InvitationController [companyInvitations, boothInvitations, show, storeForCompany, storeForBooth, approve, reject]
│   │   │   └── Shared/
│   │   │       ├── ProfileController [show, update]
│   │   │       └── ResetPasswordController [changePassword, sendResetLink, resetPassword]
│   │   └── Shared/FCMController      [store: updateOrCreate FCM token]
│   ├── Middleware/
│   │   ├── ApiLocalization.php       [Sets locale based on request header]
│   │   └── EnsureUserIsAdmin.php     [Middleware alias type.admin; restricts to admins]
│   ├── Requests/                    [25 form request classes; validation active]
│   └── Resources/                   [10 API resources; comprehensive coverage]
│       ├── Mobile/
│       │   ├── UserResource.php
│       │   └── BoothResource.php
│       ├── SystemUser/
│       │   ├── Exhibitor/
│       │   │   └── InvitaionResource.php
│       │   └── Shared/
│       │       ├── BoothRequestResource.php, BoothRequestServiceResource.php, BoothResource.php, CompanyResource.php, ProfileResource.php, ServiceResource.php, SystemUserResource.php
├── Jobs/
│   └── SendOtpWhatsappJob.php       [Queued; calls UltraMsg API with OTP code]
├── Models/                          [23 eloquent models with soft deletes and polymorphic relations]
│   ├── User.php                     [Mobile visitor; HasApiTokens, media avatars]
│   ├── SystemUser.php               [Admin/Exhibitor; MustVerifyEmail, media avatars]
│   ├── Company.php                  [Company/exhibitor entity; coordinates stored as float; status]
│   ├── Booth.php                    [Exhibition booth; unique qr_token, composite (hall_id, number)]
│   ├── BoothRequest.php             [Request to book booth; final_price snapshot]
│   ├── Hall.php                     [Physical exhibition hall; type, area]
│   ├── Event.php                    [Polymorphic; avg_rating, unique qr_token]
│   ├── EventHall.php                [Event venue; hourly pricing for billing]
│   ├── Lead.php                     [User view/scan of Booth/Event; no timestamps]
│   ├── Saved.php                    [User bookmark of Company/Event; composite unique index]
│   ├── Review.php                   [1-5 rating; composite unique index]
│   ├── Report.php                   [Complaint; polymorphic reporter/reportable]
│   ├── Invitation.php               [Team invitation model; token unique]
│   ├── Announcement.php, Facility.php, BusCatalog.php, Service.php, EventSpeaker.php [skeletal]
│   └── DeviceToken.php, OtpCode.php [Support models]
├── Notifications/Auth/
│   ├── VerifyApiEmail.php           [Sends verification link with id/{hash} to frontend URL]
│   └── ResetApiPassword.php         [Sends password reset link to frontend]
├── Policies/                        [Authorization policies]
│   ├── BoothPolicy.php              [Enforces invitation rules for Booths]
│   ├── CompanyPolicy.php            [Enforces invitation rules for Companies]
│   └── InvitationPolicy.php         [Enforces user validation for accepting/rejecting invitations]
├── Providers/
│   ├── AppServiceProvider.php       [Rate limiters, Scramble config, HTTPS enforcement, Passport key configurations]
│   └── TelescopeServiceProvider.php [Dev debugging dashboard]
├── Services/
│   ├── Mobile/
│   │   ├── AuthService.php          [register, verifyRegister, login, logout, forgotPassword, resetPassword, resendOtp]
│   │   ├── OtpService.php           [generateOtp, verifyOtp; rate limits, locks, cache cleanup]
│   │   └── ProfileService.php       [updateProfile, verifyPhoneUpdate with media library]
│   ├── SystemUser/
│   │   ├── Admin/
│   │   │   ├── AuthService.php      [login]
│   │   │   ├── BoothRequestService.php [approve, reject, conflict checking]
│   │   │   ├── ServiceService.php   [services CRUD logic]
│   │   │   └── UpdateBoothService.php [booth properties updates]
│   │   ├── Exhibitor/
│   │   │   ├── AuthService.php      [register, login, verifyEmail]
│   │   │   ├── GoogleAuthService.php [Socialite OAuth2 token validation; auto-creates SystemUser]
│   │   │   ├── BoothRequestService.php [request booth booking, attach services]
│   │   │   ├── CompanyService.php   [creates and updates companies]
│   │   │   └── InvitationService.php [creates, accepts, rejects team/booth invitations]
│   │   └── Shared/ProfileService.php [update with media library]
│   └── Shared/
│       ├── PasswordService.php      [updatePassword; revokes other tokens + device tokens]
│       ├── FCMService.php           [updateOrCreate device token; maps oauth_access_token_id]
│       └── ResetSystemUserPasswordService.php [sendResetLink, resetPassword via Laravel Password broker]
├── Trait/HasUpdatePayload.php       [updatePayload() method for PATCH DTO field extraction]
└── README.md, AGENTS.md, database_architecture.dbml

config/
├── auth.php                         [Dual guard config; default guard = "system"; password brokers for both models]
├── passport.php                     [Private/public key env vars; default guard = "web" (unused in API)]
├── app.php, database.php, queue.php, cache.php [Standard Laravel; queue driver = database]
├── services.php                     [Google OAuth, UltraMsg WhatsApp config via env]
├── scramble.php                     [OpenAPI spec generation; auto security via MiddlewareAuthSecurityStrategy]
└── filesystems.php, mail.php, logging.php [Default configs]

database/
├── migrations/                      [32 migrations; ordered by timestamp]
│   ├── Core Auth: users, system_users, oauth_* (Passport), otp_codes, device_tokens, media
│   ├── Exhibition: companies, halls, booths, booth_requests, booth_request_services
│   ├── Events: event_halls, events, event_speakers
│   ├── User Interactions: leads, saved, reviews, reports
│   ├── Infrastructure: announcements, facilities, bus_catalog, notifications, jobs, cache
│   └── Tools: telescope_entries (Telescope dev debugging)
├── factories/
│   └── UserFactory.php              [Minimal factory for testing]
└── seeders/DatabaseSeeder.php       [Creates 2 Passport clients, demo User, demo SystemUser]

routes/
├── api.php                          [Mounts /v1 prefix with nested route groups]
└── api/v1/
    ├── admin.php                    [Admin login, password reset, profile, FCM]
    ├── exhibitor.php                [Exhibitor register, Google auth, email verify, profile, password, FCM]
    └── mobile.php                   [Visitor auth, password reset, profile, FCM with per-route rate limiting]

resources/
├── css/, js/                        [Vite-bundled; Tailwind v4]
└── views/welcome.blade.php          [Default Laravel landing page]

tests/
├── Pest.php                         [Test configuration; RefreshDatabase disabled by default]
├── Feature/ExampleTest.php          [Boilerplate test]
└── Unit/ExampleTest.php             [Boilerplate test]

bootstrap/app.php, providers.php     [Laravel 13 skeleton; no custom middleware wired yet]

vite.config.js                       [Vue/React/Tailwind ready; not configured]
phpunit.xml, phpstan.neon, .github/skills/, AGENTS.md
```

---

## 4. Implemented Modules

### 4.1 Completed Routes (18 endpoints)

#### Mobile Visitor Routes (`/v1/auth`, `/v1/visitor`)

**Authentication:**
- `POST /auth/register` → Generate OTP, cache user data for 10 min, return `registration_id`
- `POST /auth/register/verify` → Verify OTP, create User, issue Passport token, clear cache
- `POST /auth/login` → Phone + password auth, issue token
- `DELETE /auth/logout` → Revoke token, delete device tokens for this token
- `POST /auth/otp/resend` → Regenerate OTP for same session
- `POST /auth/password/forgot` → Send reset OTP via WhatsApp (return UUID if user not found for security)
- `POST /auth/password/otp/verify` → Verify reset OTP, cache temp token for 10 min
- `POST /auth/password/reset` → Reset password with temp token, revoke all other tokens

**Profile & Security:**
- `GET /visitor/profile` → Return authenticated user with media avatar URL
- `POST /visitor/profile/update` → PATCH user fields + optional avatar upload (clears old)
- `PUT /visitor/profile/password/update` → Change password (current + new); revokes other tokens
- `POST /visitor/profile/phone/request` → OTP for phone change
- `POST /visitor/profile/phone/verify` → Verify new phone, update record

**Device & Notifications:**
- `POST /visitor/fcm/register-token` → UpdateOrCreate FCM token with device type

#### Admin Routes (`/v1/admin`)

- `POST /login` → Email + password auth, issue token
- `POST /change-password` → Change password with current password validation
- `POST /logout` → Revoke token
- `GET /profile` → Retrieve admin profile with media
- `POST /profile` → Update name + optional avatar
- `POST /forgot-password`, `POST /reset-password` → Via Laravel Password broker
- `GET /booths`, `GET /booths/{id}`, `PATCH /booths/{id}` → Paginated Booth management with Spatie QueryBuilder filtering.
- `GET /booths/requests` → Paginated listing of booking requests with filters.
- `GET /booths/requests/{id}` → Retrieve details of a booking request.
- `POST /booths/requests/approve/{id}` → Approve booth request (auto-assigns booth to company, sets price, and detects conflicts).
- `PATCH /booths/requests/reject/{id}` → Reject booth request.
- `GET /service`, `POST /service`, `GET /service/{id}`, `PATCH /service/{id}`, `DELETE /service/{id}` → Service CRUD resource.

#### Exhibitor Routes (`/v1/exhibitor`)

- `POST /register` → Create SystemUser, fire `Registered` event (queues email verification), issue token
- `POST /login` → Email + password, issue token
- `GET /email/verify/{id}/{hash}` → Verify email hash, mark `email_verified_at`, fire `Verified` event
- `POST /auth/system/google` → Validate Google token via Socialite, create/link SystemUser, download avatar
- `GET /booth` → List booths with filtering (min_price, max_price, area, hall_id, hall_type, number).
- `GET /booth/my` → List booths owned by current exhibitor.
- `POST /booth/request-booth` → Create booking request (optionally auto-creating a Company or using existing).
- `GET /booth/{id}` → Retrieve details of a booth.
- `GET /booth/{booth}/invitations`, `POST /booth/{booth}/invitations` → List and send invitations to join a booth.
- `GET /companies/{company}/invitations`, `POST /companies/{company}/invitations` → List and send invitations to join a company.
- `GET /invitation/{token}` → Retrieve invitation details.
- `POST /invitation/{token}/accept` → Accept team/booth invitation and join.
- `POST /invitation/{token}/reject` → Reject invitation.
- `GET /services` → Retrieve available add-on services list.
- Same password/profile routes as admin

---

### 4.2 Not Yet Implemented

**No endpoints for:**
- Event management (create, list, update, attend)
- Company CRUD (except auto-creation/lookup via booth requests)
- Report/complaint workflow
- Review/rating management
- Announcement management
- Saved items (wishlist) retrieval
- Lead tracking queries
- Facility location search
- Bus schedule retrieval

---

## 5. Database Schema Summary

### 5.1 Core Tables (33 migrations total)

| Table | Purpose | Key Traits | Notes |
|-------|---------|-----------|-------|
| `users` | Mobile visitors | soft deletes, unique (email, phone) | Avatar via media library |
| `system_users` | Admins/Exhibitors | soft deletes, unique email, `email_verified_at` | type: admin/exhibitor; Avatar via media; google_id |
| `companies` | Exhibitor organizations | soft deletes, unique phone | Geo: headquarters_lat/lng; social_links JSON; status |
| `company_system_users` | Many-to-many | Composite PK (company_id, system_user_id) | Cascade delete on both sides |
| `halls` | Physical exhibition spaces | soft deletes, unique number | type: exhibition/event; area float; full timestamps |
| `booths` | Exhibition booths | soft deletes, unique qr_token, composite (hall_id, number) | svg_id for interactive map; company_id nullable; price decimal(10,2); full timestamps |
| `booth_system_users` | Staff assignment to booths | Composite PK (booth_id, system_user_id) | assigned_by FK; created_at only |
| `booth_requests` | Booth booking applications | soft deletes | status: pending/approved/rejected; final_price snapshot; full timestamps |
| `booth_request_services` | Line items for requests | — | quantity int; unit_price decimal(10,2); no timestamps |
| `services` | Booth add-on services | — | price decimal(10,2); is_active boolean; unique name; full timestamps |
| `event_halls` | Dedicated event venues | — | unique number; price_per_hour decimal(10,2); no timestamps |
| `events` | Exhibition events | soft deletes, unique qr_token | Polymorphic eventable (Company/SystemUser); status, type, avg_rating; date datetime |
| `event_speakers` | Event presenters | — | name; no timestamps; cascade on event delete |
| `leads` | User/Exhibitor views | Composite unique (user_id, leadable_type, leadable_id) | created_at only; no updated_at |
| `saved` | User bookmarks | Composite unique (user_id, savedable_type, savedable_id) | created_at only; polymorphic savedable |
| `reviews` | 1-5 ratings + comments | Composite unique (user_id, reviewable_type, reviewable_id) | Polymorphic reviewable; soft deletes on related entities |
| `reports` | Complaints/Issues | — | Polymorphic reporter/reportable; resolved_by FK (nullable) |
| `announcements` | Push notifications | — | receiver: visitor/exhibitor/all; is_active boolean; full timestamps |
| `facilities` | Toilets, mosques, etc. | — | gender, type, svg_id; no timestamps |
| `bus_catalog` | Transportation schedules | — | duration int; start_time/end_time time; full timestamps |
| `device_tokens` | FCM push tokens | Polymorphic tokenable | fcm_token unique; oauth_access_token_id char(80); full timestamps |
| `otp_codes` | OTP verification | Unique session_id | type: registration/password_reset/phone_update; attempts counter; full timestamps |
| `media` | Spatie MediaLibrary | — | Polymorphic model; centralized file storage (uuid, conversions_disk, manipulations, custom_properties, responsive_images, order_column) |
| `invitations` | Team invitations to join companies/booths | sender_id FK, status default 'pending' | Polymorphic inviteable (Company/Booth); unique token; expires_at |

**Passport OAuth Tables:** `oauth_clients`, `oauth_access_tokens`, `oauth_refresh_tokens`, `oauth_auth_codes`, `oauth_device_codes`

**Infrastructure:** `jobs`, `job_batches`, `failed_jobs`, `cache`, `cache_locks`, `notifications`, `telescope_entries`, `telescope_entries_tags`, `telescope_monitoring`

### 5.2 Key Constraints

- **Composite Unique Indexes:**
  - `(hall_id, number)` on booths → Prevent duplicate booth numbers per hall
  - `(user_id, leadable_type, leadable_id)` on leads → Prevent duplicate lead records
  - `(user_id, savedable_type, savedable_id)` on saved → Prevent duplicate saves
  - `(user_id, reviewable_type, reviewable_id)` on reviews → Prevent duplicate reviews (1 per user per item)

- **Cascade Behavior:** Most FKs cascade on delete (exceptions: company_id on booths is nullable with null-on-delete)

- **Soft Deletes:** Active on most domain tables; allows recovery and relationship integrity

---

## 6. Model Relationships

**User (Mobile Visitor):**
```php
hasMany(Lead::class)              // Views/scans they've recorded
hasMany(Saved::class)             // Bookmarks
hasMany(Review::class)            // Ratings given
morphMany(Report::class, 'reporter')    // Complaints filed
morphMany(DeviceToken::class, 'tokenable') // FCM tokens
```

**SystemUser (Admin/Exhibitor):**
```php
belongsToMany(Company::class, 'company_system_users')
belongsToMany(Booth::class, 'booth_system_users')->withPivot('assigned_by')
hasMany(BoothRequest::class)
morphMany(Event::class, 'eventable')
morphMany(DeviceToken::class, 'tokenable')
hasMany(Report::class, 'resolved_by')  // Reports they've resolved
```

**Company:**
```php
belongsToMany(SystemUser::class, 'company_system_users')
hasMany(Booth::class)
hasMany(BoothRequest::class)
morphMany(Event::class, 'eventable')  // Conferences, etc.
```

**Booth:**
```php
belongsTo(Hall::class)
belongsTo(Company::class)->nullable()
belongsToMany(SystemUser::class, 'booth_system_users')->withPivot('assigned_by')
hasMany(BoothRequest::class)
morphMany(Lead::class, 'leadable')
morphMany(Review::class, 'reviewable')
morphMany(Report::class, 'reportable')
morphMany(Saved::class, 'savedable')
```

**Event:**
```php
morphTo('eventable')  // Company or SystemUser
belongsTo(EventHall::class)
hasMany(EventSpeaker::class)
morphMany(Lead::class, 'leadable')
morphMany(Saved::class, 'savedable')
morphMany(Review::class, 'reviewable')
morphMany(Report::class, 'reportable')
```

**BoothRequest:**
```php
belongsTo(Booth::class)
belongsTo(Company::class)
belongsTo(SystemUser::class)
hasMany(BoothRequestService::class, 'request_id')
```

**Invitation:**
```php
belongsTo(SystemUser::class, 'sender_id')
morphTo('inviteable') // Company or Booth
```

All relationships include **soft deletes** on the model side, allowing safe data recovery.

---

## 7. Authentication & Authorization Flow

### 7.1 Mobile Visitor Registration

1. `POST /auth/register` (phone, email, password, profile fields)
   - Validate uniqueness of phone/email
   - Generate 6-digit OTP, hash it
   - Store OTP in `otp_codes` table with 5-min expiry and UUID session ID
   - Dispatch `SendOtpWhatsappJob` (asynchronous via database queue)
   - Cache user payload for 10 min under `reg_data_{registration_id}`
   - Return `registration_id` (UUID)

2. `POST /auth/register/verify` (phone, otp, registration_id)
   - Verify OTP: Hash check, expiry check, attempt counter (max 3)
   - Retrieve cached user data; confirm no race-condition re-registration
   - **Transaction:** Create User, issue Passport token, clear cache
   - Return User + token

### 7.2 Admin/Exhibitor Registration & Verification

**Exhibitor Manual Registration:**
1. `POST /exhibitor/register` (name, email, password)
   - Create SystemUser
   - Fire `Registered` event → Queue `VerifyApiEmail` notification
   - Issue Passport token
   - Return User + token (email not yet verified)

2. `GET /exhibitor/email/verify/{id}/{hash}` (signed URL from email)
   - Verify SHA1 hash against user's email
   - Set `email_verified_at`
   - Fire `Verified` event

**Exhibitor Google OAuth:**
1. `POST /exhibitor/auth/system/google` (token from frontend Google SDK)
   - Validate token with Google servers via Socialite
   - Find or create SystemUser with matching email
   - Download and attach Google avatar to media library
   - Mark `email_verified_at` (auto-verified via Google)
   - Issue Passport token

**Admin Login:**
- No registration endpoint; created manually or via database
- `POST /admin/login` (email, password)

### 7.3 Token Management

**Passport Configuration ([AppServiceProvider](app/Providers/AppServiceProvider.php)):**
- Access token expiry: 15 days
- Refresh token expiry: 30 days
- Personal access token expiry: 6 months
- Scopes: Not yet implemented (all endpoints are scope-agnostic)

**Token Revocation on Logout:**
- Revoke current token via `$token->revoke()`
- Delete device tokens associated with this token
- Other active tokens remain valid

**Multi-Device Sessions:**
- Each device stores a separate `DeviceToken` record with unique `fcm_token`
- Logout only revokes the current token; other devices remain authenticated
- Password change revokes all OTHER tokens (current session survives)

### 7.4 Authorization

**Authorization & Policies:**
- Model authorization policies exist in `app/Policies/` (specifically `BoothPolicy`, `CompanyPolicy`, and `InvitationPolicy`) to enforce team invitation permissions (via `manageInvitations`, `view`, `accept`, and `reject` gates).
- Admin routes (`/v1/admin/*`) are protected using the `type.admin` middleware which aliases `EnsureUserIsAdmin::class`. This verifies that the authenticated `system` user has the `SystemUserType::ADMIN` enum value, preventing exhibitors from executing admin functions.
- Other controller actions currently assume authorization based on authentication guards (`auth:mobile` and `auth:system`).

**Security Recommendation:** Extend policies to other entities (e.g. `Event`, `Service`, `BoothRequest`) and continue integrating `Gate::authorize()` checks in controllers.

---

## 8. API Design Conventions

### 8.1 Response Format (Global Helpers)

**Success:**
```json
{
  "status": true,
  "message": "success",
  "data": { ... }
}
```
HTTP: `200`, `201`, or `204`

**Error:**
```json
{
  "status": false,
  "message": "Invalid credentials",
  "data": null
}
```
HTTP: `400`, `401`, `403`, `404`, `429`, etc.

**Rate Limit Exceeded:**
```json
{
  "status": false,
  "message": "Too many login or register attempts. Please try again later.",
  "data": null
}
```
HTTP: `429`

### 8.2 Status Codes

- `200` OK → Successful request, data returned
- `201` Created → Resource created (not currently used; routes return 200)
- `204` No Content → Success, no response body (not currently used)
- `400` Bad Request → Validation failed
- `401` Unauthorized → Authentication required or failed
- `403` Forbidden → Permission denied
- `404` Not Found → Resource not found
- `429` Too Many Requests → Rate limit exceeded
- `500` Internal Server Error → Uncaught exception

### 8.3 Authentication Header

```
Authorization: Bearer {oauth_access_token}
```

Bearer token issued by Passport on login or registration.

### 8.4 Validation & Error Messages

**Form Requests** ([app/Http/Requests](app/Http/Requests)):
- Enum validation: `new Enum(Gender::class)`
- Existence checks: `exists:users,phone`
- Unique constraints: `unique:users,phone,{id}` (ignores soft-deleted rows by default)
- Custom messages via `messages()` method
- Array notation: `['required', 'string', 'min:8']`

**Validation Error Response:**
```json
{
  "status": false,
  "message": "The given data was invalid.",
  "data": {
    "phone": ["The phone field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 8.5 Documentation

**OpenAPI/Swagger UI:**
- URL: `/docs/api` (Stoplight Elements UI)
- Spec endpoint: `/api.json`
- Configured via [config/scramble.php](config/scramble.php)
- Auto-discovers routes with `#[Group('...')]` attributes on controllers
- Security scheme: HTTP Bearer (auto-applied to `auth:*` routes)

**Route Groups (Scramble):**
- `Visitor/Auth`, `Visitor/Profile`, `Visitor/ForgotPassword`
- `SystemUser/Admin/Auth`, `SystemUser/Exhibitor/Auth`, `SystemUser/Profile`
- `SystemUser/reset_password`

---

## 9. Business Rules Implemented

### 9.1 OTP System

- **Type:** Numeric, 6 digits
- **Delivery:** WhatsApp via UltraMsg API (async job)
- **Expiry:** 5 minutes (configurable in `OtpService::generateOtp()`)
- **Attempts:** Max 3 before session locks
- **Cooldown:** 60 seconds between requests (per phone/type)
- **Daily Limit:** 10 OTPs per phone per type
- **Types:** `registration`, `password_reset`, `phone_update`
- **Session Management:** UUID-based; idempotent within window; auto-mark as used on verification
- **Storage:** `otp_codes` table with indexes on `phone` and `session_id`

### 9.2 Booth Model

- **QR Token:** Unique, secure random string for QR generation
- **Number:** Unique per hall (composite unique index `(hall_id, number)`)
- **Pricing:** Decimal(10,2) in primary currency
- **Company Association:** Nullable until booth request is approved
- **Staff Assignment:** Managed via `booth_system_users` pivot with `assigned_by` tracking
- **Availability:** `is_booked` flag tracks whether booth is available (column exists; logic not yet implemented)

### 9.3 Booth Request Workflow

- **Status Enum:** `pending`, `approved`, `rejected`
- **Final Price:** Decimal snapshot at request time (allows pricing to change over time)
- **Services:** Line items with quantity + unit_price snapshots (prevents retroactive price changes)
- **Approval Logic:** Not yet implemented in endpoints; should auto-assign booth to company

### 9.4 Event Management

- **Polymorphic Ownership:** Company or SystemUser (via `eventable_type` + `eventable_id`)
- **Status:** `pending`, `approved`, `rejected`
- **Type:** `conference`, `workshop`, `lecture`, `other` (Enum)
- **Duration:** Stored in minutes (not hours)
- **QR Token:** Unique; used for attendance tracking (endpoint not yet implemented)
- **Average Rating:** Decimal(3,2); updated on review changes (logic not yet implemented)
- **Pricing:** Associated hall has `price_per_hour`; billing calculation not yet implemented

### 9.5 Lead Tracking

- **Trigger:** When user scans Booth QR or views Event
- **Deduplication:** Composite unique index `(user_id, leadable_type, leadable_id)` prevents duplicates
- **No Update Tracking:** `created_at` only; visits to same item reuse record without updating timestamp

### 9.6 User Bookmarks (Saved Items)

- **Polymorphic Target:** Company or Event
- **Deduplication:** Composite unique index `(user_id, savedable_type, savedable_id)`
- **No Timestamps:** `created_at` only

### 9.7 Reviews & Ratings

- **Scale:** 1–5 integer
- **Deduplication:** Composite unique index `(user_id, reviewable_type, reviewable_id)` (one review per user per item)
- **Polymorphic Target:** Booth or Event
- **Comment:** Optional text field
- **Relationship:** Can be reported via Report model (allows meta-complaints about reviews)

### 9.8 Reporting & Complaints

- **Polymorphic Reporter:** User or SystemUser (who filed complaint)
- **Polymorphic Reportable:** Booth, Event, Review, or null (flexible for future extensions)
- **Status:** `pending`, `resolved`, `rejected`
- **Resolution:** `resolved_by` FK points to SystemUser admin who resolved it
- **Admin Notes:** Text field for internal tracking
- **Workflow:** Not yet implemented; should have approval/rejection logic

### 9.9 Announcements

- **Receiver Type:** `visitor`, `exhibitor`, `all` (Enum)
- **Active Flag:** Controls visibility
- **Media:** Can attach images via Media Library
- **No Timestamps:** `created_at` only; static announcements

### 9.10 Facilities

- **Gender:** Distinguishes facilities (separate toilets, etc.)
- **Type:** Mosque, toilet, etc.
- **SVG ID:** Maps to floor plan graphic
- **No Timestamps:** Static reference data

---

## 10. Services & Repositories

### 10.1 Service Architecture (No Repositories)

**All business logic is in [app/Services](app/Services). No repository pattern is used.**

Services query models directly and handle:
- State management (caching, temp tokens)
- Transactions
- External API calls (WhatsApp OTP, Google OAuth, file uploads)
- Notifications
- Error handling

### 10.2 Service Breakdown

| Service | Location | Responsibility |
|---------|----------|---|
| `AuthService` (Mobile) | [app/Services/Mobile/AuthService.php](app/Services/Mobile/AuthService.php) | register, verifyRegister, login, logout, forgotPassword, verifyForgotPasswordOtp, resetPassword, resendOtp |
| `OtpService` | [app/Services/Mobile/OtpService.php](app/Services/Mobile/OtpService.php) | generateOtp, verifyOtp; rate limiting, locking, cleanup |
| `ProfileService` (Mobile) | [app/Services/Mobile/ProfileService.php](app/Services/Mobile/ProfileService.php) | updateProfile (PATCH), verifyPhoneUpdate; media library integration |
| `AuthService` (Admin) | [app/Services/SystemUser/Admin/AuthService.php](app/Services/SystemUser/Admin/AuthService.php) | login |
| `AuthService` (Exhibitor) | [app/Services/SystemUser/Exhibitor/AuthService.php](app/Services/SystemUser/Exhibitor/AuthService.php) | register, login, verifyEmail |
| `GoogleAuthService` | [app/Services/SystemUser/Exhibitor/GoogleAuthService.php](app/Services/SystemUser/Exhibitor/GoogleAuthService.php) | handleGoogleProviderToken; Socialite token validation, auto-create/link user |
| `ProfileService` (SystemUser) | [app/Services/SystemUser/Shared/ProfileService.php](app/Services/SystemUser/Shared/ProfileService.php) | update (PATCH) with media library |
| `PasswordService` | [app/Services/Shared/PasswordService.php](app/Services/Shared/PasswordService.php) | updatePassword; current password validation, token revocation |
| `FCMService` | [app/Services/Shared/FCMService.php](app/Services/Shared/FCMService.php) | store; updateOrCreate device token |
| `ResetSystemUserPasswordService` | [app/Services/SystemUser/Shared/ResetSystemUserPasswordService.php](app/Services/SystemUser/Shared/ResetSystemUserPasswordService.php) | sendResetLink, resetPassword via Laravel Password broker |

### 10.3 Dependency Injection Pattern

All services use constructor injection; no `app()` calls.

```php
public function __construct(
    protected OtpService $otp,
    protected PasswordService $password
) {}
```

---

## 11. Validation Strategy

### 11.1 Form Request Pattern

All routes type-hint Form Request classes in controller methods. Laravel auto-validates and returns 422 on failure.

**Example:**
```php
public function register(RegisterRequest $request) {
    $validated = $request->validated();  // Always use validated(), never all()
}
```

### 11.2 Validation Rules by Route

| Route | Key Rules |
|-------|-----------|
| `/auth/register` | first_name/last_name max:255; email unique; phone unique max:20; password min:8; birthday before:today; gender Enum |
| `/auth/login` | phone required; password required |
| `/auth/register/verify` | phone required; otp digits:6; registration_id uuid |
| `/visitor/profile/update` | first_name/last_name/email/job/location sometimes max:255; avatar image mimes:jpeg,png,jpg,webp max:4096 |
| `/visitor/profile/password/update` | current_password required; new_password min:8 confirmed different:current_password |
| `/visitor/profile/phone/request` | phone unique:users,phone,{id} |
| `/exhibitor/register` | name max:255; email unique (where email_verified_at is not null); password min:8 |
| `/admin/login` | email max:255; password required |
| `POST /fcm/register-token` | token required; device_type Enum(DeviceType::class) |

### 11.3 Common Patterns

- **Existence Checks:** `exists:table_name,column` (e.g., `exists:users,phone` in forgot-password)
- **Uniqueness:** `unique:table_name,column,{id}` (ignores soft deletes by default in Laravel 13)
- **Enums:** `new Enum(GenderClass::class)` (strict backing value validation)
- **PATCH Semantics:** Use `sometimes` for optional fields; DTOs track which fields were provided
- **Confirmed:** Password confirmation (`password_confirmation` field required)

---

## 12. Response Format Standards

### 12.1 API Resources

**Comprehensive resource coverage is implemented to transform API outputs:**

| Resource | Location | Transforms |
|----------|----------|-----------|
| `UserResource` | [app/Http/Resources/Mobile/UserResource.php](app/Http/Resources/Mobile/UserResource.php) | id, first_name, last_name, email, job, location, birthday (Y-m-d), gender, phone, avatar (media URL) |
| `BoothResource` (Mobile) | [app/Http/Resources/Mobile/BoothResource.php](app/Http/Resources/Mobile/BoothResource.php) | id, number, area, type, is_booked, company, hall |
| `ProfileResource` | [app/Http/Resources/SystemUser/Shared/ProfileResource.php](app/Http/Resources/SystemUser/Shared/ProfileResource.php) | id, name, email, type (Enum value), avatar (media URL or null) |
| `BoothResource` (System) | [app/Http/Resources/SystemUser/Shared/BoothResource.php](app/Http/Resources/SystemUser/Shared/BoothResource.php) | id, hall_id, company_id, qr_token, svg_id, number, area, price, created_at, updated_at, is_booked, hall, company |
| `BoothRequestResource` | [app/Http/Resources/SystemUser/Shared/BoothRequestResource.php](app/Http/Resources/SystemUser/Shared/BoothRequestResource.php) | id, booth_id, company_id, system_user_id, final_price, status, reason_for_booking, created_at, updated_at, services, company, booth, system_user |
| `BoothRequestServiceResource` | [app/Http/Resources/SystemUser/Shared/BoothRequestServiceResource.php](app/Http/Resources/SystemUser/Shared/BoothRequestServiceResource.php) | id, request_id, service_id, quantity, unit_price, name |
| `CompanyResource` | [app/Http/Resources/SystemUser/Shared/CompanyResource.php](app/Http/Resources/SystemUser/Shared/CompanyResource.php) | id, name, business_sector, social_links, phone, year_founded, description, headquarters_lat, headquarters_lng, status, logo, gallery |
| `ServiceResource` | [app/Http/Resources/SystemUser/Shared/ServiceResource.php](app/Http/Resources/SystemUser/Shared/ServiceResource.php) | id, name, price, is_active |
| `SystemUserResource` | [app/Http/Resources/SystemUser/Shared/SystemUserResource.php](app/Http/Resources/SystemUser/Shared/SystemUserResource.php) | id, name, email, type |
| `InvitaionResource` | [app/Http/Resources/SystemUser/Exhibitor/InvitaionResource.php](app/Http/Resources/SystemUser/Exhibitor/InvitaionResource.php) | id, sender_id, email, token, status, expires_at, created_at, updated_at, sender, inviteable |

### 12.2 Missing Resources

No resources for: Event, Review, Report, Announcement, Lead, Saved, Facility, BusCatalog.

**Impact:** Standardized resources ensure API contracts remain clean. Future endpoints should map models to their respective resources.

### 12.3 Media Library Integration

**Avatar Collections:**
- `User` → `user-avatars` collection
- `SystemUser` → `avatar` collection (single file)
- `Company` → (not yet used)

**Retrieval in Resources:**
```php
'avatar' => $this->getFirstMediaUrl('user-avatars') ?? null
```

**URL Format:** `/storage/{disk}/{media-uuid}/{file-name}` (when media disk is `public`)

---

## 13. External Packages Used

| Package | Version | Purpose | Status |
|---------|---------|---------|--------|
| `laravel/framework` | 13.7 | Core framework | Active |
| `laravel/passport` | 13.0 | OAuth2 token auth | Configured; scopes not used |
| `laravel/socialite` | 5.27 | Google OAuth provider | Integrated; Exhibitor Google login working |
| `spatie/laravel-medialibrary` | 11.22 | File uploads, avatars | Integrated; collections on User/SystemUser |
| `spatie/laravel-query-builder` | * | Query filtering (generic) | Installed; not used in code yet |
| `dedoc/scramble` | 0.13.24 | OpenAPI spec generation | Configured; `/docs/api` live |
| `laravel/tinker` | 3.0 | Interactive shell | Dev only |
| `barryvdh/laravel-ide-helper` | * | IDE auto-completion | Dev only |
| `larastan/larastan` | 3.10 | Static analysis | Dev only |
| `laravel/pint` | 1.27 | Code formatting | Dev only |
| `laravel/telescope` | 5.20 | Dev debugging dashboard | Dev only; configured |
| `pestphp/pest` | 4.7 | Testing framework | Dev only; Pest.php configured; tests minimal |
| `mockery/mockery` | 1.6 | Mocking library | Dev only |

### 13.1 External Services

| Service | Library | Configuration | Status |
|---------|---------|---------------|--------|
| WhatsApp OTP | UltraMsg API | `config/services.php` (env vars) | Integrated; queued job active |
| Google OAuth | Socialite | `config/services.php` + .env | Integrated; token validation working |
| Email | Laravel Mail | `config/mail.php` (default: log) | Not production-ready (log driver) |
| Queue | Database | `config/queue.php` | Active; WhatsApp job uses this |
| Cache | Database | `config/cache.php` | Active; OTP caching uses this |
| File Storage | Local disk | `config/filesystems.php` | Local only; no S3/cloud integration |

---

## 14. Environment Requirements

### 14.1 Minimum Software Versions

- **PHP:** 8.3+
- **MySQL:** 8.0+ OR SQLite (default in .env.example)
- **Node.js:** 18+ (for npm)
- **Composer:** 2.0+

### 14.2 Key Environment Variables

```env
APP_NAME=EMS
APP_ENV=local|production
APP_DEBUG=true|false
APP_URL=http://localhost:8000
APP_FRONTEND_URL=http://localhost:3000  # Custom: for email verification links

DB_CONNECTION=sqlite|mysql|pgsql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database  # OTP/email jobs queued here
CACHE_STORE=database

MAIL_MAILER=log  # ⚠️ Not production-ready; configure SMTP
MAIL_FROM_ADDRESS=hello@example.com

# OAuth providers
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback  # (Not used in API flow)

# WhatsApp OTP
ULTRAMSG_INSTANCE_ID=...
ULTRAMSG_TOKEN=...

# Passport encryption keys (auto-generated on first migrate)
PASSPORT_PRIVATE_KEY=...
PASSPORT_PUBLIC_KEY=...

# Socialite session (for storing intermediate OAuth state)
SESSION_DRIVER=database
```

### 14.3 Recommended Tooling

- **Postman/Insomnia:** API testing
- **TablePlus/Sequel Pro:** Database management
- **VS Code + Laravel extensions:** PHP IntelliSense, Blade syntax
- **Ray:** Debug helper package (not installed; optional)

---

## 15. Current Progress Status

### 15.1 Completed

✅ **Authentication & Authorization (Surface Level)**
- OTP-based mobile registration (WhatsApp delivery)
- Email verification for System Users
- Google OAuth login (Exhibitor)
- Password reset flows (both User types)
- Passport OAuth2 token lifecycle
- Rate limiting on auth endpoints
- FCM device token management

✅ **Profile Management**
- View own profile (all user types)
- Update profile fields + avatar (all user types)
- Password change with token revocation
- Phone number change with OTP verification

✅ **Data Model Foundation**
- 22 Eloquent models with relationships defined
- 32 migrations with proper schema design
- Soft deletes on domain entities
- Polymorphic relations (Lead, Saved, Review, Report)
- Media library integration (avatars)

✅ **API Infrastructure**
- Dual-guard Passport setup (mobile, system)
- OpenAPI documentation generation (Scramble)
- Global response helpers (successResponse, errorResponse)
- Rate limiting middleware
- Form request validation

✅ **External Integrations**
- WhatsApp OTP delivery (UltraMsg API)
- Google OAuth token validation (Socialite)
- FCM push token storage
- Email notifications (VerifyApiEmail, ResetApiPassword)

### 15.2 Partial/In-Progress

⚠️ **Authorization:** Basic policies exist (`BoothPolicy`, `CompanyPolicy`) for invitations, and admin routes are protected by `type.admin` middleware. Policies for other models (e.g. `Event`, `Service`, `BoothRequest`) are not yet written.

⚠️ **API Resources:** Resources are created for Booths, BoothRequests, Companies, Services, and Invitations. Others (Events, Reports, etc.) still need them.

⚠️ **Queue System:** Active with database driver; WhatsApp jobs are queued. Email notifications not yet queued.

### 15.3 Not Started

❌ **Event Management:** No endpoints for creation, attendance, speaker management.

❌ **Company Management:** CRUD endpoints for Company management (other than auto-creation/lookup via booth requests) do not exist.

❌ **Reporting & Complaints:** Models exist; no workflow endpoints.

❌ **Lead/Analytics:** No endpoints to query user viewing history or engagement.

❌ **Announcement Management:** Models exist; no delivery/targeting logic.

❌ **Testing:** Pest setup is configured, but only basic tests exist (need to expand feature tests).

❌ **Frontend Client:** React/Vue setup exists (Vite); no UI code.

---

## 16. Pending Features

### 16.1 High Priority (Blocking MVP)

1. **Event Management**
   - Create/update/list events (Company or SystemUser can host)
   - Event hall booking and conflict detection
   - Attendee tracking via Lead model
   - Speaker management

2. **Extend Authorization Policies**
   - Create remaining policies for Event, BoothRequest, etc.
   - Enforce policy checks across all controllers

3. **Remaining Read/List Endpoints**
   - `GET /events` with date range, type, organizer filters
   - `GET /companies` with search
   - `GET /announcements` for visitor/exhibitor

### 16.2 Medium Priority

4. **Review & Rating System**
   - `POST /reviews` (rate booth/event)
   - `GET /reviews/{id}` (retrieve single review)
   - Auto-calculate `avg_rating` on Event/Booth
   - Report review (already modeled; endpoints missing)

5. **Reporting & Resolution Workflow**
   - `POST /reports` (file complaint)
   - `GET /reports` (admin list)
   - `PATCH /reports/{id}/resolve` (admin resolves with notes)

6. **Lead & Analytics**
   - `POST /leads` (implicit on booth/event view; or explicit endpoint)
   - `GET /my-activity` (return user's lead history)
   - Admin analytics dashboard endpoints

7. **Spatie QueryBuilder Pagination & Filtering**
   - Implement query pagination and sorting on future models.

### 16.3 Lower Priority

9. **Advanced Features**
   - Booth availability calendar
   - Dynamic pricing (surge pricing during peak hours)
   - Discount codes
   - Refund/cancellation policies
   - Multi-language support
   - Push notifications via FCM (endpoints exist; UI integration missing)
   - Search by location/distance (requires geo-indexing)

10. **Production Hardening**
    - Comprehensive test suite (Pest feature/unit tests)
    - API rate limiting tweaks per endpoint
    - Logging & monitoring (Telescope for dev; ELK or similar for prod)
    - Error tracking (Sentry/Rollbar)
    - Database indexing audit
    - Cache strategy for expensive queries

---

## 17. Technical Debt / Known Issues

### 17.1 Security Issues

🔴 **CRITICAL:**
- **Rate limiting on password reset is weak.** 3 per hour per phone allows brute force by changing IP.
  - **Fix:** Implement account lockout or exponential backoff; log attempts
  - **File:** [AppServiceProvider](app/Providers/AppServiceProvider.php)

🟡 **HIGH:**
- **Notification URLs hardcoded.** Password reset & verification links use `config('app.frontend_url')` which is not standard.
  - **Fix:** Use signed URLs or frontend-generated links; verify domain before sending
  - **Files:** [VerifyApiEmail](app/Notifications/Auth/VerifyApiEmail.php), [ResetApiPassword](app/Notifications/Auth/ResetApiPassword.php)

- **WhatsApp OTP sent asynchronously without delivery confirmation.** No webhook to track failed deliveries.
  - **Fix:** Add UltraMsg webhook listener; retry logic
  - **File:** [SendOtpWhatsappJob](app/Jobs/SendOtpWhatsappJob.php)

- **No CSRF protection on API endpoints.** Not necessary for stateless API, but ensure frontend CORS is locked down.
  - **Fix:** Verify `APP_URL` in production matches frontend domain
  - **File:** [config/app.php](config/app.php)

### 17.2 Architectural Issues

🟡 **MEDIUM:**
- **No repositories.** Services query models directly; tight coupling makes testing harder.
  - **Fix:** Create repository layer for data access; inject into services
  - **Impact:** Affects all service classes in [app/Services](app/Services)

- **Soft deletes not scoped by default.** Queries will return deleted records unless explicitly excluded.
  - **Fix:** Use `withoutTrashed()` or `onlyTrashed()` where needed; add global scopes if appropriate
  - **Files:** All model queries in services

- **No database transaction error handling.** Caught exceptions might leave partial updates.
  - **Fix:** Add explicit rollback and error logging in try/catch blocks
  - **Files:** [AuthService](app/Services/Mobile/AuthService.php), [GoogleAuthService](app/Services/SystemUser/Exhibitor/GoogleAuthService.php)

### 17.3 Code Quality Issues

🟡 **MEDIUM:**
- **Duplicate code across Admin/Exhibitor AuthService.** Both have nearly identical login logic.
  - **Fix:** Extract shared logic to trait or base class
  - **Files:** [Admin/AuthService](app/Services/SystemUser/Admin/AuthService.php), [Exhibitor/AuthService](app/Services/SystemUser/Exhibitor/AuthService.php)

- **No logging in services.** Errors are silently thrown; hard to debug production issues.
  - **Fix:** Add `Log::info()`, `Log::error()` throughout
  - **Files:** All service classes

- **Inconsistent null handling.** Some services use `?? throw Exception()` others use if/throw.
  - **Fix:** Standardize on one pattern
  - **Files:** Services, controllers

### 17.4 Database Issues

🟡 **MEDIUM:**
- **No database indexes on frequently-queried columns.** Scans on phone, email, session_id could be slow.
  - **Fix:** Add indexes: `phone` (otp_codes), `email` (users, system_users), `session_id` (otp_codes, already exists), `eventable_type` (events)
  - **Files:** Create new migration

- **Soft delete queries inefficient.** No index on `deleted_at` for soft-delete scoping.
  - **Fix:** Add indexes on `deleted_at` columns
  - **Files:** Create new migration

- **No foreign key indexes by default.** Laravel migrations create FKs but MySQL doesn't auto-index them.
  - **Fix:** Verify `constrained()` includes implicit indexing or add explicit indexes
  - **Files:** Verify migrations

### 17.5 Feature Gaps

🟡 **MEDIUM:**
- **No test coverage.** Only boilerplate tests exist.
  - **Fix:** Write Pest feature tests for auth flows, CRUD endpoints, validation
  - **Files:** [tests/Feature](tests/Feature), [tests/Unit](tests/Unit)

- **No API versioning beyond URL prefix.** Future v2 changes will break v1 clients.
  - **Fix:** Use header-based versioning or parallel route groups; document deprecation policy
  - **Files:** Routes, controllers

- **Mail driver set to log.** Won't send real emails in production.
  - **Fix:** Configure SMTP in `.env.production`
  - **Files:** [config/mail.php](config/mail.php)

- **No request logging.** Can't audit who called what.
  - **Fix:** Add middleware to log HTTP requests/responses (Telescope does this in dev)
  - **Files:** Create new middleware

### 17.6 Configuration Issues

🟡 **MEDIUM:**
- **Timezone hardcoded to UTC.** Events/announcements may be confusing for local users.
  - **Fix:** Add timezone to User/SystemUser model; use in notification times
  - **Files:** Migrations, models

- **No multi-database support.** All queries hit single DB; no read replicas.
  - **Fix:** Configure read/write connections in `config/database.php`
  - **Files:** [config/database.php](config/database.php)

---

## 18. Recommended Next Steps

### Immediate (Next Sprint)

1. **Add Authorization Policies** (blocking security issue)
   - Create [app/Policies/](app/Policies) directory
   - Policies: `BoothPolicy`, `CompanyPolicy`, `EventPolicy`, `BoothRequestPolicy`
   - Add checks in all admin/exhibitor endpoints

2. **Implement Booth CRUD + Request Workflow**
   - Routes: `POST /booths`, `GET /booths/{id}`, `PATCH /booths/{id}`, etc.
   - Request workflow: `POST /booth-requests/{id}/approve`, `/reject`
   - Validation: Ensure booth number unique per hall, price > 0
   - Tests: Feature tests for happy path + error cases

3. **Add Event Management Endpoints**
   - Create/update/list events
   - Integrate EventHall booking validation (prevent double-booking)
   - Speaker management

4. **Create API Resources for All Entities**
   - Booth, Company, Event, BoothRequest, Review, Report, Announcement, etc.
   - Add pagination support; use `spatie/laravel-query-builder`

### Short-term (Next 2-3 Sprints)

5. **Add Read/List Endpoints**
   - `/booths` with filters (hall, company, availability, price range)
   - `/events` with date range, type, organizer filters
   - `/companies` with search by name, sector
   - `/announcements` filtered by receiver type

6. **Reporting & Analytics**
   - File complaints: `POST /reports`
   - Admin resolve: `PATCH /reports/{id}/resolve`
   - Analytics: Lead tracking, popular booths, engagement metrics

7. **Database Optimization**
   - Add missing indexes (phone, email, deleted_at, foreign keys)
   - Review query performance with database profiler

8. **Test Suite**
   - Write Pest feature tests for all auth flows
   - Unit tests for services (OtpService, AuthService, etc.)
   - Achieve 70%+ coverage

### Medium-term (1-2 Months)

9. **Production Hardening**
   - Error tracking (Sentry)
   - Structured logging (ELK or Papertrail)
   - Performance monitoring (New Relic, Datadog)
   - Database read replicas if traffic warrants

10. **Advanced Features**
    - Push notifications via FCM
    - Booth availability calendar with pricing
    - Search by location (geo-indexing)
    - Discount codes and promotions
    - Multi-language support

11. **Frontend Integration**
    - React/Vue SPA consuming API
    - Admin dashboard
    - Mobile app (React Native or Flutter)

---

## Architectural Decisions to Preserve

**For any future AI session working on this project:**

1. **Service-oriented design** (no repositories): Services handle all business logic; direct model queries. Keep this pattern for consistency.

2. **Passport OAuth2 with dual guards:** Two separate user models (`User`, `SystemUser`) with two guards (`mobile`, `system`). Don't merge into single User model without major refactoring.

3. **OTP caching + transactions:** Registration uses 10-min cache for atomicity. Don't move to database sessions without understanding race condition implications.

4. **Soft deletes everywhere:** Most domain models include `deleted_at`. Queries must use `withoutTrashed()` or risk returning soft-deleted data. Add global scopes if intended as default behavior.

5. **Polymorphic relations:** Lead, Saved, Review, Report use polymorphic patterns. Maintain for flexibility to track user interactions across different entity types.

6. **Rate limiting per endpoint:** Custom rate limiters in AppServiceProvider, not global middleware. Keep granular control for different auth flows.

7. **No authorization policies yet:** Security gap exists. Don't proceed with CRUD endpoints without adding policies first.

8. **Media library for avatars:** Spatie MediaLibrary on User/SystemUser. Don't add custom file upload logic; extend Media Library instead.

9. **Form requests for validation:** Always validate via Form Requests; never call `$request->all()`. Enforce `$request->validated()`.

10. **DTOs for service inputs:** Use readonly DTOs for type safety; `fromRequest()` factory for conversion from Form Requests.

11. **Backend-For-Frontend (BFF) Pattern:** Separate controllers and resources by user type (`Admin`, `Exhibitor`, `Visitor`) to maintain clean API contracts and Swagger docs. Do not use shared controllers with complex `if/else` guard checks.
12. **Spatie QueryBuilder Custom Filters:** Always use dedicated classes implementing `Filter` interface in `app/Filters` for complex queries. Re-use generic filters like `MinFilter` dynamically passing the column name.
13. **Scramble Documentation:** Document query parameters using PHP Attributes like `#[QueryParameter]` directly on controller methods instead of raw PHPDoc.
14. **Global Exception Handling:** Rely on `bootstrap/app.php` to catch `NotFoundHttpException` and format it as a standard JSON API response.

---

## File Locations Quick Reference

**Critical Files:**
- Config: [config/auth.php](config/auth.php), [config/passport.php](config/passport.php), [config/services.php](config/services.php)
- Auth: [app/Services/Mobile/AuthService.php](app/Services/Mobile/AuthService.php), [app/Services/Mobile/OtpService.php](app/Services/Mobile/OtpService.php)
- Models: [app/Models/User.php](app/Models/User.php), [app/Models/SystemUser.php](app/Models/SystemUser.php)
- Routes: [routes/api.php](routes/api.php), [routes/api/v1/mobile.php](routes/api/v1/mobile.php), [routes/api/v1/admin.php](routes/api/v1/admin.php), [routes/api/v1/exhibitor.php](routes/api/v1/exhibitor.php)
- Controllers: [app/Http/Controllers/Api/V1/Mobile/AuthController.php](app/Http/Controllers/Api/V1/Mobile/AuthController.php)
- Jobs: [app/Jobs/SendOtpWhatsappJob.php](app/Jobs/SendOtpWhatsappJob.php)
- Notifications: [app/Notifications/Auth/VerifyApiEmail.php](app/Notifications/Auth/VerifyApiEmail.php), [app/Notifications/Auth/ResetApiPassword.php](app/Notifications/Auth/ResetApiPassword.php)
- Migrations: [database/migrations/](database/migrations/) (32 total)
- Tests: [tests/](tests/) (minimal coverage)

**Scaffolding & Support:**
- Helpers: [app/Helper/ApiResponse.php](app/Helper/ApiResponse.php)
- DTOs: [app/DTOs/](app/DTOs/)
- Enums: [app/Enum/](app/Enum/)
- Requests: [app/Http/Requests/](app/Http/Requests/)
- Resources: [app/Http/Resources/](app/Http/Resources/)
- Database: [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php)
- Console: [routes/console.php](routes/console.php) (scheduled commands)

---

## Final Notes

This document is a comprehensive snapshot of the EMS backend as of June 2026. It reflects the actual, verified state of the codebase through direct inspection of migrations, models, services, controllers, and configuration.

**Use this as:**
1. **Onboarding guide** for new developers
2. **Handoff document** between AI sessions
3. **Architecture reference** when making design decisions
4. **Security audit checklist** (many items marked as debt)
5. **Feature prioritization** based on implementation status

**Update this document when:**
- Adding/removing major features
- Changing authentication or authorization schemes
- Refactoring service layer
- Adding new external integrations
- Resolving security issues
- Implementing policies or repositories

---

**Document Version:** 1.0  
**Generated:** 2026-06-16  
**Status:** Accurate as of last codebase scan
