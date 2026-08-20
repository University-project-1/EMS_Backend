<?php
$tables = DB::select('SHOW TABLES');
foreach ($tables as $row) { echo 'TABLE|'.implode('|', (array) $row).PHP_EOL; }

$queries = [
    'users_total' => "select count(*) c from users",
    'ordinary_users' => "select count(*) c from users where email like '%@approved-users.test'",
    'system_users' => "select count(*) c from system_users",
    'companies' => "select count(*) c from companies",
    'company_memberships' => "select count(*) c from company_system_users",
    'halls' => "select count(*) c from halls",
    'booths' => "select count(*) c from booths",
    'booth_memberships' => "select count(*) c from booth_system_users",
    'booth_requests' => "select count(*) c from booth_requests",
    'services' => "select count(*) c from services",
    'events' => "select count(*) c from events",
    'event_speakers' => "select count(*) c from event_speakers",
    'announcements' => "select count(*) c from announcements",
    'reviews' => "select count(*) c from reviews",
    'leads' => "select count(*) c from leads",
    'saved' => "select count(*) c from saved",
    'reports' => "select count(*) c from reports",
    'event_reminders' => "select count(*) c from event_reminders",
    'media' => "select count(*) c from media",
    'qr_media' => "select count(*) c from media where collection_name='qr_code'",
    'user_avatar_media' => "select count(*) c from media where collection_name='user-avatars'",
    'system_avatar_media' => "select count(*) c from media where collection_name='avatar'",
    'company_logo_media' => "select count(*) c from media where collection_name='logo'",
    'company_gallery_media' => "select count(*) c from media where collection_name='gallery'",
];
foreach ($queries as $key => $sql) { echo $key.'|'.DB::selectOne($sql)->c.PHP_EOL; }

foreach (['booth_requests'=>'status','events'=>'status','reports'=>'status'] as $table=>$column) {
    echo strtoupper($table).'_STATUS'.PHP_EOL;
    foreach (DB::select("select $column status,count(*) c from $table group by $column order by $column") as $r) echo ($r->status ?? 'NULL').'|'.$r->c.PHP_EOL;
}

echo 'INTERACTION_TYPES'.PHP_EOL;
foreach ([['reviews','reviewable_type'],['leads','leadable_type'],['saved','savedable_type'],['reports','reportable_type']] as [$table,$col]) {
    foreach (DB::select("select $col target_type,count(*) c from $table group by $col order by $col") as $r) echo "$table|$r->target_type|$r->c".PHP_EOL;
}

echo 'MEDIA_COLLECTIONS'.PHP_EOL;
foreach (DB::select('select collection_name,count(*) c from media group by collection_name order by collection_name') as $r) echo $r->collection_name.'|'.$r->c.PHP_EOL;

echo 'EVENT_REVIEW_RANGE'.PHP_EOL;
foreach (DB::select("select min(c) min_c,max(c) max_c from (select reviewable_id,count(*) c from reviews where reviewable_type='App\\Models\\Event' group by reviewable_id) x") as $r) echo $r->min_c.'|'.$r->max_c.PHP_EOL;
echo 'BOOTH_REVIEW_RANGE'.PHP_EOL;
foreach (DB::select("select min(c) min_c,max(c) max_c from (select reviewable_id,count(*) c from reviews where reviewable_type='App\\Models\\Booth' group by reviewable_id) x") as $r) echo $r->min_c.'|'.$r->max_c.PHP_EOL;
foreach (['reviews','leads','saved'] as $table) {
    echo 'USER_'.$table.'_RANGE'.PHP_EOL;
    foreach (DB::select("select min(c) min_c,max(c) max_c from (select user_id,count(*) c from $table group by user_id) x") as $r) echo $r->min_c.'|'.$r->max_c.PHP_EOL;
}

echo 'TOP_COMPANIES_BY_MEMBERS'.PHP_EOL;
foreach (DB::select('select c.name,count(*) members from companies c join company_system_users m on m.company_id=c.id group by c.id,c.name order by members desc limit 10') as $r) echo $r->name.'|'.$r->members.PHP_EOL;
echo 'TOP_BOOTH_TARGETS_BY_REVIEWS'.PHP_EOL;
foreach (DB::select("select reviewable_id,count(*) c from reviews where reviewable_type='App\\Models\\Booth' group by reviewable_id order by c desc limit 10") as $r) echo $r->reviewable_id.'|'.$r->c.PHP_EOL;
echo 'TOP_EVENT_TARGETS_BY_REVIEWS'.PHP_EOL;
foreach (DB::select("select e.title,count(*) c from reviews r join events e on e.id=r.reviewable_id where r.reviewable_type='App\\Models\\Event' group by e.id,e.title order by c desc limit 10") as $r) echo $r->title.'|'.$r->c.PHP_EOL;

$eventName = 'Founder Stories from Damascus to the Region';
$r = DB::selectOne("select count(*) c from reviews r join events e on e.id=r.reviewable_id where r.reviewable_type='App\\Models\\Event' and e.title=?", [$eventName]);
$l = DB::selectOne("select count(*) c from leads l join events e on e.id=l.leadable_id where l.leadable_type='App\\Models\\Event' and e.title=?", [$eventName]);
$s = DB::selectOne("select count(*) c from saved s join events e on e.id=s.savedable_id where s.savedable_type='App\\Models\\Event' and e.title=?", [$eventName]);
echo 'NAMED_EVENT|'.$eventName.'|'.$r->c.'|'.$l->c.'|'.$s->c.PHP_EOL;
