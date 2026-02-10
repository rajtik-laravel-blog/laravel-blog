<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

test('app locale is set to Czech', function () {
    expect(App::getLocale())->toBe('cs');
});

test('validation message is in Czech', function () {
    $validator = Validator::make(['email' => ''], ['email' => 'required|email']);
    expect($validator->fails())->toBeTrue();
    $messages = $validator->errors()->get('email');
    // Assert a Czech phrase is present
    expect(collect($messages)->implode(' '))->toContain('Pole');
});
