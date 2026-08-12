<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shared\BusCatalogResource;
use App\Models\BusCatalog;
use Dedoc\Scramble\Attributes\Group;

#[Group('Visitor/BusCatalog')]
class BusCatalogController extends Controller
{
    /**
     * all
     */
    public function index()
    {
        $buses = BusCatalog::cursorPaginate(10);

        return successResponse(BusCatalogResource::collection($buses));
    }
}
