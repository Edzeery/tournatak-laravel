<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

test('isRtlLocale returns true for ar', function () {
    $this->assertTrue(isRtlLocale('ar'));
});

test('isRtlLocale returns false for en', function () {
    $this->assertFalse(isRtlLocale('en'));
});

test('isRtlLocale returns false for fr', function () {
    $this->assertFalse(isRtlLocale('fr'));
});

test('formatDate returns formatted date', function () {
    $date = \Carbon\Carbon::create(2025, 6, 15);
    $this->assertEquals('15/06/2025', formatDate($date));
});

test('formatDate returns null for null date', function () {
    $this->assertNull(formatDate(null));
});

test('formatDate accepts custom format', function () {
    $date = \Carbon\Carbon::create(2025, 6, 15);
    $this->assertEquals('15/06', formatDate($date, 'd/m'));
});

test('formatDateTime returns formatted datetime', function () {
    $date = \Carbon\Carbon::create(2025, 6, 15, 14, 30);
    $this->assertEquals('15/06/2025 14:30', formatDateTime($date));
});

test('formatDateTime returns null for null date', function () {
    $this->assertNull(formatDateTime(null));
});
