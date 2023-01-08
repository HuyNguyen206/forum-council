<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\URL;

trait VerifyEmail
{
    private function verifyEmail($user)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        $this->get($verificationUrl);

        return $user;
    }
}
