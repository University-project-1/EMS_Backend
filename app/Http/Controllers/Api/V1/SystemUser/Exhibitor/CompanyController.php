<?php

namespace App\Http\Controllers\Api\V1\SystemUser\Exhibitor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemUser\Shared\CompanyResource;
use App\Http\Resources\SystemUser\Shared\ProfileResource;
use App\Models\Company;
use App\Models\SystemUser;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /*
     No CRUD operation
     new company can be created only by booking event-hall or booth it is a part of booking story
     update company cant be available because of admin confirmation and cant be edited directly
    */

    public function show(Company $company){
        $company->load(['logoMedia', 'galleryMedia']);
        return successResponse(
            data: ['company' => new CompanyResource($company), 'exhibitor' => new ProfileResource(request()->user('system'))],
            message: "profile retrived successfully",
        );
    }
}
