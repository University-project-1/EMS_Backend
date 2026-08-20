<?php
require __DIR__.'/vendor/autoload.php';
$app=require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
$rows=DB::table('companies')->join('booths','booths.company_id','=','companies.id')->where('booths.hall_id',15)->orderBy('booths.number')->get(['companies.id','companies.name','booths.number']);
$all=DB::table('booths')->where('hall_id',15)->get(['id','number','company_id']);
echo json_encode(['total'=>$all->count(),'assigned'=>$all->whereNotNull('company_id')->count(),'available'=>$all->whereNull('company_id')->count(),'assigned_companies'=>$rows],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),PHP_EOL;
