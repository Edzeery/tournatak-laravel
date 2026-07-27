<?php

test('home page renders successfully', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

test('teams page renders', function () {
    $response = $this->get('/teams');
    $response->assertStatus(200);
});

test('players page renders', function () {
    $response = $this->get('/players');
    $response->assertStatus(200);
});

test('locale can be switched', function () {
    $response = $this->get('/lang/en');
    $response->assertRedirect();
    $this->assertEquals('en', app()->getLocale());
});

test('404 page renders for non-existent route', function () {
    $response = $this->get('/non-existent-route-xyz');
    $response->assertStatus(404);
});
