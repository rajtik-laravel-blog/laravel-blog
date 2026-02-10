<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = User::query()
            ->where('provider_name', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (! $user && $socialUser->getEmail()) {
            $user = User::query()->where('email', $socialUser->getEmail())->first();
        }

        if (! $user) {
            $user = User::create([
                'name' => $socialUser->getName() ?: ($socialUser->getNickname() ?: ucfirst($provider).' User'),
                'email' => $socialUser->getEmail() ?: sprintf('%s@users.noreply.%s.com', $socialUser->getId(), $provider),
                'password' => null,
            ]);
        }

        if (! $user->provider_name || ! $user->provider_id) {
            $user->forceFill([
                'provider_name' => $provider,
                'provider_id' => (string) $socialUser->getId(),
            ])->save();
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }
}
