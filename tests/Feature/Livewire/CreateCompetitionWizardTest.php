<?php

use App\Livewire\Admin\Competitions\CreateCompetitionPage;
use App\Models\Competition;
use App\Models\CompetitionDomain;
use App\Models\CompetitionSubtype;
use App\Models\CompetitionType;
use App\Models\Sport;
use App\Models\User;
use App\Services\ScoringEngineRegistry;
use App\Services\SubmissionScoringEngine;
use Livewire\Livewire;

test('wizard stays on domain step when no domain selected', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    Livewire::actingAs($admin)
        ->test(CreateCompetitionPage::class)
        ->set('name', 'No Domain Competition')
        ->call('store')
        ->assertSet('step', 'domain');
});

test('choosing sports domain produces official competition preserving type subtype and sport', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $sports = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();
    $type = CompetitionType::factory()->create();
    $subtype = CompetitionSubtype::factory()->create();

    Livewire::actingAs($admin)
        ->test(CreateCompetitionPage::class)
        ->call('selectDomain', $sports->id)
        ->assertSet('domain_id', $sports->id)
        ->assertSet('step', 'basics')
        ->set('name', 'Champions League Local')
        ->set('type_id', $type->id)
        ->set('subtype_id', $subtype->id)
        ->set('location', 'Rabat Stadium')
        ->set('start_date', '2026-09-01 18:00')
        ->set('end_date', '2026-09-05 22:00')
        ->set('description', 'Local sports competition')
        ->call('nextStep')
        ->assertSet('step', 'review')
        ->call('store')
        ->assertRedirect(route('admin.competitions.index'));

    $this->assertDatabaseHas('competitions', [
        'name' => 'Champions League Local',
        'domain_id' => $sports->id,
        'type_id' => $type->id,
        'subtype_id' => $subtype->id,
        'sport_id' => Sport::where('slug', 'football')->value('id'),
        'organizer_id' => $admin->id,
    ]);
});

test('sports wizard basics step requires type and subtype', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $sports = CompetitionDomain::where('slug', CompetitionDomain::SLUG_SPORTS)->firstOrFail();

    Livewire::actingAs($admin)
        ->test(CreateCompetitionPage::class)
        ->call('selectDomain', $sports->id)
        ->set('name', 'Missing Type Competition')
        ->call('nextStep')
        ->assertHasErrors(['type_id', 'subtype_id'])
        ->assertSet('step', 'basics');
});

test('choosing hackathon domain produces competition with domain and provisioned type and subtype', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $hackathon = CompetitionDomain::where('slug', CompetitionDomain::SLUG_HACKATHON)->firstOrFail();

    Livewire::actingAs($admin)
        ->test(CreateCompetitionPage::class)
        ->call('selectDomain', $hackathon->id)
        ->assertSet('domain_id', $hackathon->id)
        ->assertSet('step', 'basics')
        ->set('name', 'AI Hackathon 2026')
        ->set('location', 'Rabat')
        ->set('start_date', '2026-10-01 09:00')
        ->set('end_date', '2026-10-03 18:00')
        ->set('description', 'Build something great')
        ->call('nextStep')
        ->assertSet('step', 'rounds')
        ->set('rounds_count', 3)
        ->set('judging_criteria', 'Innovation, feasibility, impact')
        ->call('nextStep')
        ->assertSet('step', 'review')
        ->call('store')
        ->assertRedirect(route('admin.competitions.index'));

    $competition = Competition::where('name', 'AI Hackathon 2026')->firstOrFail();

    $this->assertSame($hackathon->id, $competition->domain_id);
    $this->assertNull($competition->sport_id);
    $this->assertNotNull($competition->type_id);
    $this->assertNotNull($competition->subtype_id);
    $this->assertSame(3, $competition->format_config['rounds_count'] ?? null);
    $this->assertSame('Innovation, feasibility, impact', $competition->format_config['judging_criteria'] ?? null);
});

test('hackathon wizard step navigation validates rounds fields', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $hackathon = CompetitionDomain::where('slug', CompetitionDomain::SLUG_HACKATHON)->firstOrFail();

    Livewire::actingAs($admin)
        ->test(CreateCompetitionPage::class)
        ->call('selectDomain', $hackathon->id)
        ->set('name', 'Rounds Validation Comp')
        ->call('nextStep')
        ->assertSet('step', 'rounds')
        ->set('rounds_count', 25)
        ->call('nextStep')
        ->assertHasErrors(['rounds_count'])
        ->assertSet('step', 'rounds');
});

test('design domain extension point produces submission competition without new engine', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $design = CompetitionDomain::where('slug', CompetitionDomain::SLUG_DESIGN)->firstOrFail();

    Livewire::actingAs($admin)
        ->test(CreateCompetitionPage::class)
        ->call('selectDomain', $design->id)
        ->assertSet('domain_id', $design->id)
        ->assertSet('step', 'basics')
        ->set('name', 'Poster Design Contest 2026')
        ->set('location', 'Rabat')
        ->set('start_date', '2026-11-01 09:00')
        ->set('end_date', '2026-11-02 18:00')
        ->set('description', 'Design a competition poster')
        ->call('nextStep')
        ->assertSet('step', 'rounds')
        ->set('rounds_count', 2)
        ->set('judging_criteria', 'Originality, composition, execution')
        ->call('nextStep')
        ->assertSet('step', 'review')
        ->call('store')
        ->assertRedirect(route('admin.competitions.index'));

    $competition = Competition::where('name', 'Poster Design Contest 2026')->firstOrFail();

    $this->assertSame($design->id, $competition->domain_id);
    $this->assertNull($competition->sport_id);
    $this->assertSame('submission', $competition->evaluationBasis());
    $this->assertInstanceOf(
        SubmissionScoringEngine::class,
        app(ScoringEngineRegistry::class)->forCompetition($competition),
    );
});
