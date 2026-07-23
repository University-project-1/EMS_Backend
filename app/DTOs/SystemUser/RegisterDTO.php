<?php

namespace App\DTOs\SystemUser;

use App\Models\Invitation;
use App\Trait\HasSnakeCaseArray;

class RegisterDTO
{
    use HasSnakeCaseArray;
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $inviteToken
    ){}

    public static function fromRequest(array $data){
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            inviteToken: null
        );
    }

    public static function fromInvitationRequest(array $data, Invitation $invitation){
        return new self(
            name: $data['name'],
            email: $invitation->email,
            password: $data['password'],
            inviteToken: $invitation->token
        );
    }
}
