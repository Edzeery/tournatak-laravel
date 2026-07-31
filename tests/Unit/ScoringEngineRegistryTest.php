<?php

namespace Tests\Unit;

use App\Services\ScoringEngineRegistry;
use App\Services\SportsScoringEngine;
use App\Services\StandingService;
use App\Services\SubmissionScoringEngine;
use Tests\TestCase;

class ScoringEngineRegistryTest extends TestCase
{
    private function makeRegistry(): ScoringEngineRegistry
    {
        return new ScoringEngineRegistry(
            new SportsScoringEngine(app(StandingService::class)),
            new SubmissionScoringEngine,
        );
    }

    public function test_resolves_sports_engine_for_match_basis(): void
    {
        $registry = $this->makeRegistry();

        $engine = $registry->forBasis('match');

        $this->assertInstanceOf(SportsScoringEngine::class, $engine);
    }

    public function test_resolves_submission_engine_for_submission_basis(): void
    {
        $registry = $this->makeRegistry();

        $engine = $registry->forBasis('submission');

        $this->assertInstanceOf(SubmissionScoringEngine::class, $engine);
    }

    public function test_unknown_basis_falls_back_to_default_engine(): void
    {
        $registry = $this->makeRegistry();

        $engine = $registry->forBasis('unknown');

        $this->assertInstanceOf(SportsScoringEngine::class, $engine);
    }

    public function test_all_returns_registered_engines(): void
    {
        $registry = $this->makeRegistry();

        $this->assertCount(2, $registry->all());
    }

    public function test_default_engine_throws_when_none_registered(): void
    {
        $this->expectException(\RuntimeException::class);

        (new ScoringEngineRegistry)->defaultEngine();
    }
}
