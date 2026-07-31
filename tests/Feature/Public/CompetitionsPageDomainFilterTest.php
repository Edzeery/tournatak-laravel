<?php

use App\Models\Competition;
use App\Models\CompetitionDomain;

test('competitions listing without filter shows all approved competitions', function () {
    $sports = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();
    $hackathon = CompetitionDomain::where('slug', CompetitionDomain::SLUG_HACKATHON)->firstOrFail();

    Competition::factory()->create([
        'domain_id' => $sports->id,
        'approval_status' => 'approved',
        'name' => 'Sports League One',
    ]);
    Competition::factory()->create([
        'domain_id' => $hackathon->id,
        'approval_status' => 'approved',
        'name' => 'Hackathon Sprint',
    ]);

    $this->get('/competitions')
        ->assertOk()
        ->assertSee('Sports League One')
        ->assertSee('Hackathon Sprint');
});

test('competitions listing filtered by domain returns only that domain', function () {
    $sports = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();
    $hackathon = CompetitionDomain::where('slug', CompetitionDomain::SLUG_HACKATHON)->firstOrFail();

    Competition::factory()->create([
        'domain_id' => $sports->id,
        'approval_status' => 'approved',
        'name' => 'Sports League One',
    ]);
    Competition::factory()->create([
        'domain_id' => $hackathon->id,
        'approval_status' => 'approved',
        'name' => 'Hackathon Sprint',
    ]);

    $this->get('/competitions?domain=hackathon')
        ->assertOk()
        ->assertSee('Hackathon Sprint')
        ->assertDontSee('Sports League One');
});

test('competitions listing filtered by sports domain excludes other domains', function () {
    $sports = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();
    $creative = CompetitionDomain::where('slug', CompetitionDomain::SLUG_CREATIVE)->firstOrFail();

    Competition::factory()->create([
        'domain_id' => $sports->id,
        'approval_status' => 'approved',
        'name' => 'Regional Cup',
    ]);
    Competition::factory()->create([
        'domain_id' => $creative->id,
        'approval_status' => 'approved',
        'name' => 'Photo Contest',
    ]);

    $this->get('/competitions?domain=sports')
        ->assertOk()
        ->assertSee('Regional Cup')
        ->assertDontSee('Photo Contest');
});
