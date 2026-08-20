<?php
require __DIR__.'/vendor/autoload.php';
$app=require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
$rows=DB::table('booths')->where('hall_id',15)->orderBy('id')->get(['id','number','company_id','qr_token']);
echo json_encode(['count'=>$rows->count(),'assigned'=>$rows->whereNotNull('company_id')->count(),'available'=>$rows->whereNull('company_id')->count(),'rows'=>$rows],JSON_PRETTY_PRINT),PHP_EOL;
