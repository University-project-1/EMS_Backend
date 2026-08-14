<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $receiver
 * @property string $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereReceiver($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Announcement whereUpdatedAt($value)
 */
	class Announcement extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $hall_id
 * @property int|null $company_id
 * @property string|null $qr_token
 * @property string $number
 * @property float $area
 * @property numeric $price
 * @property string|null $svg_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BoothRequest> $boothRequests
 * @property-read int|null $booth_requests_count
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\Hall|null $hall
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invitation> $invitations
 * @property-read int|null $invitations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lead> $leads
 * @property-read int|null $leads_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $reports
 * @property-read int|null $reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Saved> $savedItems
 * @property-read int|null $saved_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SystemUser> $systemUsers
 * @property-read int|null $system_users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth whereHallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth whereQrToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth whereSvgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booth withoutTrashed()
 */
	class Booth extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $booth_id
 * @property int $company_id
 * @property int $system_user_id
 * @property numeric $final_price
 * @property \App\Enum\Status $status
 * @property string|null $reason_for_booking
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service> $attachedServices
 * @property-read int|null $attached_services_count
 * @property-read \App\Models\Booth|null $booth
 * @property-read \App\Models\Company|null $company
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BoothRequestService> $services
 * @property-read int|null $services_count
 * @property-read \App\Models\SystemUser|null $systemUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest whereBoothId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest whereFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest whereReasonForBooking($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest whereSystemUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequest withoutTrashed()
 */
	class BoothRequest extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $request_id
 * @property int $service_id
 * @property int $quantity
 * @property numeric $unit_price
 * @property-read \App\Models\BoothRequest|null $request
 * @property-read \App\Models\Service|null $service
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequestService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequestService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequestService query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequestService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequestService whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequestService whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequestService whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothRequestService whereUnitPrice($value)
 */
	class BoothRequestService extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $booth_id
 * @property int $system_user_id
 * @property int|null $assigned_by
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothSystemUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothSystemUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothSystemUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothSystemUser whereAssignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothSystemUser whereBoothId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothSystemUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BoothSystemUser whereSystemUserId($value)
 */
	class BoothSystemUser extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $location
 * @property string $start_time
 * @property string $end_time
 * @property int $duration
 * @property string|null $created_at
 * @property string|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusCatalog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusCatalog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusCatalog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusCatalog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusCatalog whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusCatalog whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusCatalog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusCatalog whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusCatalog whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusCatalog whereUpdatedAt($value)
 */
	class BusCatalog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \App\Enum\BusinessSectors $business_sector
 * @property array<array-key, mixed> $social_links
 * @property string $phone
 * @property int $year_founded
 * @property string $description
 * @property float $headquarters_lat
 * @property float $headquarters_lng
 * @property \App\Enum\Status $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BoothRequest> $boothRequests
 * @property-read int|null $booth_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booth> $booths
 * @property-read int|null $booths_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invitation> $invitations
 * @property-read int|null $invitations_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SystemUser> $systemUsers
 * @property-read int|null $system_users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereBusinessSector($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereHeadquartersLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereHeadquartersLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereSocialLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company whereYearFounded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Company withoutTrashed()
 */
	class Company extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $company_id
 * @property int $system_user_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanySystemUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanySystemUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanySystemUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanySystemUser whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanySystemUser whereSystemUserId($value)
 */
	class CompanySystemUser extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property string $fcm_token
 * @property string $device_type
 * @property string|null $oauth_access_token_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $tokenable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereFcmToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereOauthAccessTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereTokenableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereTokenableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereUpdatedAt($value)
 */
	class DeviceToken extends \Eloquent {}
}

namespace App\Models{
/**
 * @property Status $status
 * @property EventType $type
 * @property int $id
 * @property string $eventable_type
 * @property int $eventable_id
 * @property int $event_hall_id
 * @property string $title
 * @property string $description
 * @property numeric $avg_rating
 * @property string|null $qr_token
 * @property \Illuminate\Support\Carbon $start_at
 * @property int $duration
 * @property \Illuminate\Support\Carbon $end_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\EventHall $eventHall
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $eventable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lead> $leads
 * @property-read int|null $leads_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $reports
 * @property-read int|null $reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Saved> $savedItems
 * @property-read int|null $saved_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EventSpeaker> $speakers
 * @property-read int|null $speakers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event accessibleBy(\App\Models\SystemUser $systemUser)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereAvgRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventHallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEventableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereQrToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event withoutTrashed()
 */
	class Event extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $number
 * @property float $area
 * @property string|null $svg_id
 * @property numeric $price_per_hour
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventHall newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventHall newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventHall query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventHall whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventHall whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventHall whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventHall wherePricePerHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventHall whereSvgId($value)
 */
	class EventHall extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property-read \App\Models\Event|null $event
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSpeaker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSpeaker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSpeaker query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSpeaker whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSpeaker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventSpeaker whereName($value)
 */
	class EventSpeaker extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $number
 * @property string $gender
 * @property string|null $svg_id
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereSvgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereType($value)
 */
	class Facility extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $number
 * @property float $area
 * @property string $type
 * @property string|null $svg_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booth> $booths
 * @property-read int|null $booths_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall whereSvgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hall withoutTrashed()
 */
	class Hall extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sender_id
 * @property string $inviteable_type
 * @property int $inviteable_id
 * @property string $email
 * @property string $token
 * @property \App\Enum\Status $status
 * @property string $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $inviteable
 * @property-read \App\Models\SystemUser|null $sender
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereInviteableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereInviteableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invitation whereUpdatedAt($value)
 */
	class Invitation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $leadable_type
 * @property int $leadable_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $leadable
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereLeadableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereLeadableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lead whereUserId($value)
 */
	class Lead extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $phone
 * @property string $otp
 * @property string $type
 * @property int $attempts
 * @property int $is_used
 * @property string $expires_at
 * @property string $session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereIsUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereUpdatedAt($value)
 */
	class OtpCode extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $reporter_type
 * @property int $reporter_id
 * @property string|null $reportable_type
 * @property int|null $reportable_id
 * @property string $title
 * @property string $description
 * @property int|null $resolved_by
 * @property \App\Enum\ReportStatus $status
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $reportable
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $reporter
 * @property-read \App\Models\SystemUser|null $resolvedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReportableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReportableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReporterType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereResolvedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 */
	class Report extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $reviewable_type
 * @property int $reviewable_id
 * @property int $rating
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $reviewable
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereReviewableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereReviewableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUserId($value)
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $savedable_type
 * @property int $savedable_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $savedable
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saved newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saved newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saved query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saved whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saved whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saved whereSavedableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saved whereSavedableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Saved whereUserId($value)
 */
	class Saved extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property numeric $price
 * @property bool $is_active
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BoothRequestService> $boothRequestServices
 * @property-read int|null $booth_request_services_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service withoutTrashed()
 */
	class Service extends \Eloquent {}
}

namespace App\Models{
/**
 * @property SystemUserType $type
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $password
 * @property string|null $google_id
 * @property string|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BoothRequest> $boothRequests
 * @property-read int|null $booth_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booth> $booths
 * @property-read int|null $booths_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Company> $companies
 * @property-read int|null $companies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeviceToken> $deviceTokens
 * @property-read int|null $device_tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Client> $oauthApps
 * @property-read int|null $oauth_apps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $resolvedReports
 * @property-read int|null $resolved_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser withoutTrashed()
 */
	class SystemUser extends \Eloquent implements \Spatie\MediaLibrary\HasMedia, \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property string $job
 * @property string $location
 * @property \Illuminate\Support\Carbon $birthday
 * @property string $gender
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeviceToken> $deviceTokens
 * @property-read int|null $device_tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lead> $leads
 * @property-read int|null $leads_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Client> $oauthApps
 * @property-read int|null $oauth_apps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $reports
 * @property-read int|null $reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Saved> $savedItems
 * @property-read int|null $saved_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

