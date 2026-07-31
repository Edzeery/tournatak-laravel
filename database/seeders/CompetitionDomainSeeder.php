<?php

namespace Database\Seeders;

use App\Models\CompetitionDomain;
use Illuminate\Database\Seeder;

class CompetitionDomainSeeder extends Seeder
{
    public function run(): void
    {
        $domains = [
            [
                'name' => 'الرياضات',
                'name_en' => 'Sports',
                'name_fr' => 'Sports',
                'name_es' => 'Deportes',
                'slug' => 'sports',
                'icon' => 'bi-trophy',
                'description' => 'Football, futsal and other athletic competitions.',
                'evaluation_basis' => 'match',
                'participant_basis' => 'both',
                'sort_order' => 1,
            ],
            [
                'name' => 'الرياضات الإلكترونية',
                'name_en' => 'Esports',
                'name_fr' => 'Esports',
                'name_es' => 'Esports',
                'slug' => 'esports',
                'icon' => 'bi-controller',
                'description' => 'Competitive video gaming competitions and tournaments.',
                'evaluation_basis' => 'match',
                'participant_basis' => 'both',
                'sort_order' => 2,
            ],
            [
                'name' => 'أكاديمي ومسابقات علمية',
                'name_en' => 'Academic & Quiz',
                'name_fr' => 'Académique & Quiz',
                'name_es' => 'Académico y Quiz',
                'slug' => 'academic',
                'icon' => 'bi-mortarboard',
                'description' => 'Academic olympiads, quiz bowls and knowledge competitions.',
                'evaluation_basis' => 'submission',
                'participant_basis' => 'individual',
                'sort_order' => 3,
            ],
            [
                'name' => 'هاكاثون',
                'name_en' => 'Hackathons',
                'name_fr' => 'Hackathons',
                'name_es' => 'Hackatones',
                'slug' => 'hackathon',
                'icon' => 'bi-code-slash',
                'description' => 'Coding marathons and innovation challenges.',
                'evaluation_basis' => 'submission',
                'participant_basis' => 'both',
                'sort_order' => 4,
            ],
            [
                'name' => 'فنون إبداعية',
                'name_en' => 'Creative Arts',
                'name_fr' => 'Arts créatifs',
                'name_es' => 'Artes creativas',
                'slug' => 'creative',
                'icon' => 'bi-palette',
                'description' => 'Photography, writing, design and artistic competitions.',
                'evaluation_basis' => 'submission',
                'participant_basis' => 'both',
                'sort_order' => 5,
            ],
        ];

        foreach ($domains as $domain) {
            CompetitionDomain::query()->updateOrCreate(
                ['slug' => $domain['slug']],
                array_merge($domain, ['is_active' => true]),
            );
        }
    }
}
