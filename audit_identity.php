<?php
require __DIR__.'/vendor/autoload.php';
$app=require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$duplicates=App\Models\SystemUser::selectRaw('LOWER(TRIM(name)) as n, COUNT(*) as c')->groupBy('n')->having('c','>',1)->get();
$duplicateEmails=App\Models\SystemUser::selectRaw('LOWER(TRIM(email)) as e, COUNT(*) as c')->groupBy('e')->having('c','>',1)->get();
$user=App\Models\SystemUser::where('email','abdelmalek.mouzayen@expanded-tech.test')->first();
echo json_encode([
 'duplicate_names'=>$duplicates,
 'duplicate_emails'=>$duplicateEmails,
 'abdel_user_id'=>$user?->id,
 'abdel_companies'=>$user?->companies()->pluck('name')->sort()->values(),
 'abdel_pivot_count'=>$user ? DB::table('company_system_users')->where('system_user_id',$user->id)->count() : 0,
 'system_users'=>App\Models\SystemUser::count(),
 'companies'=>App\Models\Company::count(),
 'pivots'=>DB::table('company_system_users')->count(),
 'approved_qr_booths'=>App\Models\Booth::whereNotNull('qr_token')->count(),
], JSON_PRETTY_PRINT).PHP_EOL;
