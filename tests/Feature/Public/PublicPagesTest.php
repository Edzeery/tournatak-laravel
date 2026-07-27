<?php

test('public teams page renders', function () {
    $this->get('/teams')->assertStatus(200);
});

test('public players page renders', function () {
    $this->get('/players')->assertStatus(200);
});

test('public competitions page renders', function () {
    $this->get('/competitions')->assertStatus(200);
});

test('home page renders', function () {
    $this->get('/')->assertStatus(200);
});

test('locale can be switched to ar', function () {
    $this->get('/?locale=ar')->assertStatus(200);
});

test('login page renders', function () {
    $this->get('/login')->assertStatus(200);
});

test('register page renders', function () {
    $this->get('/register')->assertStatus(200);
});

test('forgot password page renders', function () {
    $this->get('/forgot-password')->assertStatus(200);
});

test('404 page renders for non-existent route', function () {
    $this->get('/non-existent-page')->assertStatus(404);
});
