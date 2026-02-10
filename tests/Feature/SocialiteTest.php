<?php

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Mockery as m;

it('redirects to github provider', function () {
    $provider = m::mock(SocialiteProvider::class);
    $provider->shouldReceive('redirect')->andReturn(redirect('https://github.com/login/oauth/authorize'));

    $factory = m::mock(SocialiteFactory::class);
    $factory->shouldReceive('driver')->with('github')->andReturn($provider);

    $this->app->instance(SocialiteFactory::class, $factory);

    $response = $this->get(route('oauth.redirect', ['provider' => 'github']));

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('github.com');
});

it('handles github callback, creates and logs in the user', function () {
    $socialUser = m::mock(SocialiteUserContract::class);
    $socialUser->shouldReceive('getId')->andReturn('github-123');
    $socialUser->shouldReceive('getEmail')->andReturn('octo@example.com');
    $socialUser->shouldReceive('getName')->andReturn('Octo Cat');
    $socialUser->shouldReceive('getNickname')->andReturn('octocat');

    $provider = m::mock(SocialiteProvider::class);
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('user')->andReturn($socialUser);

    $factory = m::mock(SocialiteFactory::class);
    $factory->shouldReceive('driver')->with('github')->andReturn($provider);

    $this->app->instance(SocialiteFactory::class, $factory);

    $response = $this->get(route('oauth.callback', ['provider' => 'github']));

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'octo@example.com',
        'provider_name' => 'github',
        'provider_id' => 'github-123',
    ]);

    $this->assertTrue(Auth::check());
});
