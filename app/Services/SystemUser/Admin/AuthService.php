<?php

namespace App\Services\SystemUser\Admin;

use App\DTOs\SystemUser\LoginDTO;
use App\Models\SystemUser;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function login(LoginDTO $dto){
        $exhibitor = SystemUser::where('email', $dto->email)->first();
        if(!$exhibitor || !Hash::check($dto->password, $exhibitor->password)){
            return ['error'=>'no match', 'statusCode'=>401];
        }
        $token = $exhibitor->createToken('exhibitor_token')->accessToken;
        return ['success', 'token'=>$token, 'user' => $exhibitor];
    }

}
