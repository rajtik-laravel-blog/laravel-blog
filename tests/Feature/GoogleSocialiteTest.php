<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

it('redirects to google provider', function () {
    $factory = mock(SocialiteFactory::class);
    app()->instance('Laravel\Socialite\Contracts\Factory', $factory);

    $factory->shouldReceive('driver')->with('google')->andReturnSelf();
    $factory->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

    $response = $this->get('/auth/google/redirect');

    $response->assertRedirect();
});

it('handles google callback, creates and logs in the user', function () {
    $factory = mock(SocialiteFactory::class);
    app()->instance('Laravel\Socialite\Contracts\Factory', $factory);

    $socialUser = mock(SocialiteUserContract::class);
    $socialUser->shouldReceive('getId')->andReturn('google-123');
    $socialUser->shouldReceive('getEmail')->andReturn('guser@example.com');
    $socialUser->shouldReceive('getName')->andReturn('Google User');
    $socialUser->shouldReceive('getNickname')->andReturn('guser');

    $factory->shouldReceive('driver')->with('google')->andReturnSelf();
    $factory->shouldReceive('stateless')->andReturnSelf();
    $factory->shouldReceive('user')->andReturn($socialUser);

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'guser@example.com',
        'provider_name' => 'google',
        'provider_id' => 'google-123',
    ]);

    $this->assertTrue(Auth::check());
    $this->assertInstanceOf(User::class, Auth::user());
});
