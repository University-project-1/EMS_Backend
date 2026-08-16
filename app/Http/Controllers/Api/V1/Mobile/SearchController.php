<?php
namespace App\Http\Controllers\Api\V1\Mobile;

use App\Enum\Status;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Event;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $v = $request->validate(['q' => ['required', 'string', 'min:2', 'max:100'], 'limit' => ['sometimes', 'integer', 'min:1', 'max:20']]);
        $q = trim($v['q']);
        $l = $v['limit'] ?? 10;
        
        $c = Company::query()->where('status', Status::APPROVED->value)->where('name', 'like', "%{$q}%")->orderBy('name')->limit($l)->get(['id', 'name'])->map(fn($x) => ['id' => $x->id, 'type' => 'company', 'title' => $x->name, 'show_endpoint' => "/api/v1/companies/{$x->id}"]);
        $e = Event::query()->where('status', Status::APPROVED->value)->where('title', 'like', "%{$q}%")->orderBy('start_at')->limit($l)->get(['id', 'title', 'start_at'])->map(fn($x) => ['id' => $x->id, 'type' => 'event', 'title' => $x->title, 'start_at' => $x->start_at?->toISOString(), 'show_endpoint' => "/api/v1/events/{$x->id}"]);
        return successResponse(['query' => $q, 'results' => $c->concat($e)->values(), 'companies' => $c->values(), 'events' => $e->values()]);
    }
}
