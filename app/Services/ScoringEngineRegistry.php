<?php

namespace App\Services;

use App\Contracts\ScoringEngineInterface;
use App\Models\Competition;

class ScoringEngineRegistry
{
    /** @var list<ScoringEngineInterface> */
    protected array $engines = [];

    public function __construct(ScoringEngineInterface ...$engines)
    {
        $this->engines = $engines;
    }

    public function register(ScoringEngineInterface $engine): void
    {
        $this->engines[] = $engine;
    }

    public function forBasis(string $evaluationBasis): ScoringEngineInterface
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($evaluationBasis)) {
                return $engine;
            }
        }

        return $this->defaultEngine();
    }

    public function forCompetition(Competition $competition): ScoringEngineInterface
    {
        return $this->forBasis($competition->evaluationBasis());
    }

    public function defaultEngine(): ScoringEngineInterface
    {
        if (empty($this->engines)) {
            throw new \RuntimeException('No scoring engines registered.');
        }

        return $this->engines[0];
    }

    /** @return list<ScoringEngineInterface> */
    public function all(): array
    {
        return $this->engines;
    }
}
