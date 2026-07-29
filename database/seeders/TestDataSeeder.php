<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Team;
use App\Models\User;
use App\Models\Player;
use App\Models\Position;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\CompetitionSubtype;
use App\Models\Match_;
use App\Models\MatchEvent;
use App\Models\MatchLineup;
use App\Models\MatchStat;
use App\Models\Formation;
use App\Models\TeamStaff;
use App\Models\TeamTactic;
use App\Models\TeamMedicalRecord;
use App\Models\TeamSeasonStat;
use App\Models\PlayerSeasonStat;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // ── 1. Teams ──────────────────────────────────────────────────
            $this->command->info('Creating teams...');

            $teamNames = ['الهلال', 'الأهلي', 'النصر', 'الاتحاد', 'الشباب', 'الوحدة', 'الفيحاء', 'الأهلي السعودي'];
            $teams = [];
            foreach ($teamNames as $name) {
                $teams[] = Team::create([
                    'name' => $name,
                    'points' => rand(10, 30),
                ]);
            }

            // ── 2. Users & Players (3 per team = 24) ──────────────────────
            $this->command->info('Creating users & players...');

            $nationalities = [
                'السعودية', 'مصر', 'المغرب', 'تونس', 'الجزائر', 'العراق',
                'الكويت', 'قطر', 'الإمارات', 'عمان', 'البحرين', 'فلسطين',
                'الأردن', 'لبنان', 'اليمن', 'السودان', 'جيبوتي', 'الصومال',
                'موريتانيا', 'جزر القمر', 'السنغال', 'مالي', 'تشاد', 'نيجيريا',
            ];
            $footOptions = ['right', 'left', 'both'];
            $bios = [
                'لاعب موهوب يتميز بسرعته ومهارته العالية',
                'متوسط ميدان ذكي ينظم اللعب ويتحكم بالtempo',
                'مدافع قوي يمتاز في الكرات الهوائية والتدخلات',
                'حارس مرمى بارع يتميز بردود فعل سريعة',
                'جناح سريع يجيد المراوغة والتمرير العرضي',
                'مهاجم حاد يجيد التسجيل من مختلف الزوايا',
                'لاعب وسط متوازن يجيد الدفاع والهجوم',
                'ظهير أيمن صاعد يمتاز بالطاقة الهجومية',
                'لاعب خبرة كبيرة في المباريات الكبرى',
                'موهبة صاعدة يتنبأ لها بمستقبل مشرق',
                'قائد ميداني ي.organize اللعب ويحفز زملاءه',
                'مدافع مركزي يتميز بالقراءة التكتيكية',
                'جناح أيسر سريع يجيد التوغل من الأطراف',
                'صانع ألعاب مبدع يملك رؤية استثنائية',
                'مهاجم صريح يجيد حركات التوقيت في منطقة الجزاء',
                'حارس مرمى شاب يملك إمكانات كبيرة للتطور',
            ];

            $positions = Position::where('sport_type', 'football')->orderBy('sort_order')->get();
            if ($positions->isEmpty()) {
                $this->command->error('No football positions found. Run PositionSeeder first.');
                return;
            }

            $allPlayers = [];

            foreach ($teams as $teamIndex => $team) {
                $this->command->info("  Creating 3 players for {$team->name}...");

                for ($p = 0; $p < 3; $p++) {
                    $playerIndex = $teamIndex * 3 + $p;
                    $posIndex = ($playerIndex) % $positions->count();

                    $user = User::create([
                        'name' => $this->getPlayerName($playerIndex),
                        'username' => 'player_' . ($playerIndex + 1),
                        'email' => 'player_' . ($playerIndex + 1) . '@test.com',
                        'password' => bcrypt('password'),
                        'role' => 'player',
                        'is_verified' => true,
                    ]);

                    $dobYear = rand(1990, 2005);
                    $dobMonth = rand(1, 12);
                    $dobDay = rand(1, 28);
                    $nationality = $nationalities[array_rand($nationalities)];

                    $player = Player::create([
                        'user_id' => $user->id,
                        'team_id' => $team->id,
                        'number' => $this->getPlayerNumber($p),
                        'position_id' => $positions[$posIndex]->id,
                        'sport_type' => 'football',
                        'nationality' => $nationality,
                        'height' => rand(165, 195),
                        'weight' => rand(60, 90),
                        'foot' => $footOptions[array_rand($footOptions)],
                        'date_of_birth' => "{$dobYear}-{$dobDay}-{$dobMonth}",
                        'bio' => $bios[array_rand($bios)],
                        'is_captain' => $p === 0,
                    ]);

                    $allPlayers[] = $player;

                    if ($p === 0) {
                        $team->update(['captain_id' => $player->id]);
                    }
                }
            }

            // ── 3. Competition Subtypes & Types ────────────────────────────
            $this->command->info('Creating competition types...');

            $subtype = CompetitionSubtype::firstOrCreate(
                ['name' => 'الدوري'],
                ['en_name' => 'League']
            );

            $compTypes = [];
            $typeData = [
                ['name' => 'دوري المحترفين', 'slug' => 'pro-league'],
                ['name' => 'كأس الأبطال', 'slug' => 'champions-cup'],
                ['name' => 'الدوري الممتاز', 'slug' => 'premier-league'],
            ];
            foreach ($typeData as $td) {
                $compTypes[] = CompetitionType::create([
                    'subtype_id' => $subtype->id,
                    'name' => $td['name'],
                    'slug' => $td['slug'],
                    'sort_order' => count($compTypes) + 1,
                    'is_active' => true,
                ]);
            }

            // ── 4. Use existing admin from DatabaseSeeder ──────────────────
            $admin = User::where('email', 'admin@tournatak.com')->first();
            if (!$admin) {
                $admin = User::firstOrCreate(
                    ['email' => 'admin@test.com'],
                    [
                        'name' => 'المدير العام',
                        'username' => 'admin2',
                        'password' => bcrypt('password'),
                        'role' => 'admin',
                        'is_verified' => true,
                    ]
                );
            }

            // ── 5. Competitions (3) ───────────────────────────────────────
            $this->command->info('Creating competitions...');

            $compData = [
                [
                    'type' => $compTypes[0],
                    'name' => 'دوري المحترفين 2026',
                    'status' => 'ongoing',
                    'start' => '2026-01-15',
                    'end' => '2026-06-30',
                ],
                [
                    'type' => $compTypes[1],
                    'name' => 'كأس الأبطال 2026',
                    'status' => 'ongoing',
                    'start' => '2026-02-10',
                    'end' => '2026-07-15',
                ],
                [
                    'type' => $compTypes[2],
                    'name' => 'الدوري الممتاز 2026',
                    'status' => 'upcoming',
                    'start' => '2026-09-01',
                    'end' => '2027-01-31',
                ],
            ];

            $competitions = [];
            foreach ($compData as $cd) {
                $competitions[] = Competition::create([
                    'type_id' => $cd['type']->id,
                    'subtype_id' => $subtype->id,
                    'organizer_id' => $admin->id,
                    'name' => $cd['name'],
                    'status' => $cd['status'],
                    'approval_status' => 'approved',
                    'start_date' => $cd['start'],
                    'end_date' => $cd['end'],
                ]);
            }

            // ── 6. Matches (4 per competition = 12) ───────────────────────
            $this->command->info('Creating matches...');

            $teamIds = array_map(fn($t) => $t->id, $teams);
            $allMatches = [];
            $matchCounter = 0;

            foreach ($competitions as $cIdx => $comp) {
                for ($m = 0; $m < 4; $m++) {
                    $t1Idx = ($cIdx * 4 + $m) % count($teamIds);
                    $t2Idx = ($cIdx * 4 + $m + 1 + ($m % 3)) % count($teamIds);

                    if ($t1Idx === $t2Idx) {
                        $t2Idx = ($t2Idx + 1) % count($teamIds);
                    }

                    $statuses = ['completed', 'completed', 'completed', 'in_progress', 'scheduled'];
                    $status = $statuses[array_rand($statuses)];

                    $score1 = $status === 'completed' ? rand(0, 4) : null;
                    $score2 = $status === 'completed' ? rand(0, 4) : null;

                    if ($status === 'scheduled') {
                        $score1 = null;
                        $score2 = null;
                    } elseif ($status === 'in_progress') {
                        $score1 = rand(0, 3);
                        $score2 = rand(0, 3);
                    }

                    $matchDate = match ($status) {
                        'completed' => "2026-" . str_pad((string) rand(1, 6), 2, '0', STR_PAD_LEFT) . "-" . str_pad((string) rand(1, 28), 2, '0', STR_PAD_LEFT) . " 20:00:00",
                        'in_progress' => now()->format('Y-m-d') . ' 21:00:00',
                        'scheduled' => now()->addDays(rand(1, 30))->format('Y-m-d') . ' 20:00:00',
                        default => now()->format('Y-m-d'),
                    };

                    $allMatches[] = Match_::create([
                        'competition_id' => $comp->id,
                        'team1_id' => $teamIds[$t1Idx],
                        'team2_id' => $teamIds[$t2Idx],
                        'match_date' => $matchDate,
                        'score_team1' => $score1,
                        'score_team2' => $score2,
                        'status' => $status,
                    ]);

                    $matchCounter++;
                }
            }

            // ── 7. Match Events (for completed matches) ───────────────────
            $this->command->info('Creating match events...');

            $completedMatches = array_filter($allMatches, fn($m) => $m->status === 'completed');

            foreach ($completedMatches as $match) {
                $team1Players = Player::where('team_id', $match->team1_id)->get();
                $team2Players = Player::where('team_id', $match->team2_id)->get();

                $totalGoals = ($match->score_team1 ?? 0) + ($match->score_team2 ?? 0);

                $goalsForTeam1 = $match->score_team1 ?? 0;
                $goalsForTeam2 = $match->score_team2 ?? 0;

                for ($g = 0; $g < $goalsForTeam1; $g++) {
                    $scorer = $team1Players->random();
                    $minute = rand(1, 88);
                    MatchEvent::create([
                        'match_id' => $match->id,
                        'team_id' => $match->team1_id,
                        'player_id' => $scorer->id,
                        'event_type' => 'goal',
                        'minute' => $minute,
                        'added_time' => rand(0, 3),
                    ]);

                    if ($team1Players->count() > 1) {
                        $assistPlayer = $team1Players->where('id', '!=', $scorer->id)->random();
                        MatchEvent::create([
                            'match_id' => $match->id,
                            'team_id' => $match->team1_id,
                            'player_id' => $assistPlayer->id,
                            'event_type' => 'assist',
                            'minute' => $minute,
                            'added_time' => 0,
                        ]);
                    }
                }

                for ($g = 0; $g < $goalsForTeam2; $g++) {
                    $scorer = $team2Players->random();
                    $minute = rand(1, 88);
                    MatchEvent::create([
                        'match_id' => $match->id,
                        'team_id' => $match->team2_id,
                        'player_id' => $scorer->id,
                        'event_type' => 'goal',
                        'minute' => $minute,
                        'added_time' => rand(0, 3),
                    ]);

                    if ($team2Players->count() > 1) {
                        $assistPlayer = $team2Players->where('id', '!=', $scorer->id)->random();
                        MatchEvent::create([
                            'match_id' => $match->id,
                            'team_id' => $match->team2_id,
                            'player_id' => $assistPlayer->id,
                            'event_type' => 'assist',
                            'minute' => $minute,
                            'added_time' => 0,
                        ]);
                    }
                }

                $yellowCardsCount = rand(1, 4);
                for ($y = 0; $y < $yellowCardsCount; $y++) {
                    $isTeam1 = $y % 2 === 0;
                    $players = $isTeam1 ? $team1Players : $team2Players;
                    MatchEvent::create([
                        'match_id' => $match->id,
                        'team_id' => $isTeam1 ? $match->team1_id : $match->team2_id,
                        'player_id' => $players->random()->id,
                        'event_type' => 'yellow_card',
                        'minute' => rand(10, 85),
                        'added_time' => 0,
                    ]);
                }

                $subCount = rand(1, 3);
                for ($s = 0; $s < $subCount; $s++) {
                    $isTeam1 = $s % 2 === 0;
                    $players = $isTeam1 ? $team1Players : $team2Players;
                    if ($players->count() >= 2) {
                        $out = $players->random();
                        $in = $players->where('id', '!=', $out->id)->random();
                        MatchEvent::create([
                            'match_id' => $match->id,
                            'team_id' => $isTeam1 ? $match->team1_id : $match->team2_id,
                            'player_id' => $in->id,
                            'event_type' => 'substitution_in',
                            'minute' => rand(45, 85),
                            'related_player_id' => $out->id,
                        ]);
                        MatchEvent::create([
                            'match_id' => $match->id,
                            'team_id' => $isTeam1 ? $match->team1_id : $match->team2_id,
                            'player_id' => $out->id,
                            'event_type' => 'substitution_out',
                            'minute' => rand(45, 85),
                            'related_player_id' => $in->id,
                        ]);
                    }
                }
            }

            // ── 8. Match Lineups ──────────────────────────────────────────
            $this->command->info('Creating match lineups...');

            foreach ($allMatches as $match) {
                $team1Players = Player::where('team_id', $match->team1_id)->get();
                $team2Players = Player::where('team_id', $match->team2_id)->get();

                $lineupPositions = $positions->pluck('id')->toArray();
                $startCount = min(11, $team1Players->count());

                foreach ($team1Players->take($startCount) as $idx => $player) {
                    $posId = $lineupPositions[$idx % count($lineupPositions)];
                    MatchLineup::create([
                        'match_id' => $match->id,
                        'player_id' => $player->id,
                        'team_id' => $match->team1_id,
                        'position_id' => $posId,
                        'is_starter' => true,
                        'jersey_number' => $player->number,
                        'is_captain' => $player->is_captain,
                        'minute_in' => 0,
                        'minute_out' => $match->status === 'completed' ? 90 : null,
                    ]);
                }

                foreach ($team2Players->take($startCount) as $idx => $player) {
                    $posId = $lineupPositions[$idx % count($lineupPositions)];
                    MatchLineup::create([
                        'match_id' => $match->id,
                        'player_id' => $player->id,
                        'team_id' => $match->team2_id,
                        'position_id' => $posId,
                        'is_starter' => true,
                        'jersey_number' => $player->number,
                        'is_captain' => $player->is_captain,
                        'minute_in' => 0,
                        'minute_out' => $match->status === 'completed' ? 90 : null,
                    ]);
                }
            }

            // ── 9. Match Stats ────────────────────────────────────────────
            $this->command->info('Creating match stats...');

            foreach ($completedMatches as $match) {
                foreach ([$match->team1_id, $match->team2_id] as $teamId) {
                    $possession = rand(30, 70);
                    $shotsTotal = rand(5, 20);
                    $shotsOnTarget = rand(1, min($shotsTotal, 10));
                    $passesTotal = rand(200, 600);

                    MatchStat::create([
                        'match_id' => $match->id,
                        'team_id' => $teamId,
                        'possession' => $possession,
                        'shots_total' => $shotsTotal,
                        'shots_on_target' => $shotsOnTarget,
                        'shots_off_target' => $shotsTotal - $shotsOnTarget,
                        'corners' => rand(0, 12),
                        'fouls' => rand(5, 25),
                        'offsides' => rand(0, 5),
                        'yellow_cards' => rand(0, 3),
                        'red_cards' => rand(0, 1),
                        'passes_total' => $passesTotal,
                        'passes_accurate' => (int) ($passesTotal * (rand(60, 90) / 100)),
                        'tackles' => rand(10, 35),
                        'saves' => rand(2, 8),
                        'hit_woodwork' => rand(0, 2),
                        'blocked_shots' => rand(0, 5),
                    ]);
                }
            }

            // ── 10. Formations (per team) ──────────────────────────────────
            $this->command->info('Creating formations...');

            $positionsData442 = [
                ['x' => 50, 'y' => 90, 'role' => 'GK'],
                ['x' => 20, 'y' => 70, 'role' => 'LB'],
                ['x' => 40, 'y' => 72, 'role' => 'CB'],
                ['x' => 60, 'y' => 72, 'role' => 'CB'],
                ['x' => 80, 'y' => 70, 'role' => 'RB'],
                ['x' => 20, 'y' => 45, 'role' => 'LM'],
                ['x' => 40, 'y' => 48, 'role' => 'CM'],
                ['x' => 60, 'y' => 48, 'role' => 'CM'],
                ['x' => 80, 'y' => 45, 'role' => 'RM'],
                ['x' => 35, 'y' => 22, 'role' => 'ST'],
                ['x' => 65, 'y' => 22, 'role' => 'ST'],
            ];

            foreach ($teams as $team) {
                Formation::create([
                    'team_id' => $team->id,
                    'name' => '4-4-2 أساسي',
                    'sport_type' => 'football',
                    'formation_code' => '4-4-2',
                    'positions_data' => $positionsData442,
                    'description' => 'التشكيل الأساسي للفريق - تشكيل 4-4-2 متوازن',
                    'is_default' => true,
                    'is_active' => true,
                ]);
            }

            // ── 12. Team Staff (2 per team) ───────────────────────────────
            $this->command->info('Creating team staff...');

            $staffRoles = ['head_coach', 'assistant_coach'];
            $specializations = ['tactical_training', 'physical_fitness'];

            foreach ($teams as $tIdx => $team) {
                for ($s = 0; $s < 2; $s++) {
                    $staffUser = User::firstOrCreate(
                        ['email' => "staff_{$tIdx}_{$s}@test.com"],
                        [
                            'name' => "مدرب {$team->name} " . ($s === 0 ? 'الرئيسي' : 'المساعد'),
                            'username' => "staff_{$tIdx}_{$s}",
                            'password' => bcrypt('password'),
                            'role' => 'user',
                            'is_verified' => true,
                        ]
                    );

                    TeamStaff::create([
                        'team_id' => $team->id,
                        'user_id' => $staffUser->id,
                        'staff_role' => $staffRoles[$s],
                        'specialization' => $specializations[$s],
                        'start_date' => now()->subYear(),
                        'is_active' => true,
                    ]);
                }
            }

            // ── 13. Team Tactics ───────────────────────────────────────────
            $this->command->info('Creating team tactics...');

            $pressingStyles = ['high', 'medium', 'low', 'mixed'];
            $buildUpStyles = ['from_back', 'quick_counter', 'long_ball', 'mixed'];
            $defenseStyles = ['zone', 'man_to_man', 'mixed'];
            $attackStyles = ['wing_play', 'central', 'balanced', 'counter_attack'];

            foreach ($teams as $team) {
                TeamTactic::create([
                    'team_id' => $team->id,
                    'name' => 'التكتيك الأساسي - ' . $team->name,
                    'pressing_style' => $pressingStyles[array_rand($pressingStyles)],
                    'build_up_style' => $buildUpStyles[array_rand($buildUpStyles)],
                    'defense_style' => $defenseStyles[array_rand($defenseStyles)],
                    'attack_style' => $attackStyles[array_rand($attackStyles)],
                    'formation_used' => '4-4-2',
                    'notes' => 'التكتيك الأساسي المستخدم في مباريات الدوري',
                    'is_default' => true,
                ]);
            }

            // ── 14. Team Medical Records ───────────────────────────────────
            $this->command->info('Creating medical records...');

            $injuries = [
                ['name' => 'تمزق في العضلة الساقية', 'severity' => 'minor', 'days' => 14],
                ['name' => 'التواء في الكاحل', 'severity' => 'moderate', 'days' => 30],
                ['name' => 'إصابة في الرباط الصليبي', 'severity' => 'severe', 'days' => 180],
                ['name' => 'كسر في عظم مشط القدم', 'severity' => 'moderate', 'days' => 60],
                ['name' => 'كدمة في الركبة', 'severity' => 'minor', 'days' => 10],
                ['name' => 'تمزق في الغضروف', 'severity' => 'moderate', 'days' => 45],
                ['name' => 'إصابة في الكتف', 'severity' => 'minor', 'days' => 21],
                ['name' => 'شد في العضلة الفخذية', 'severity' => 'minor', 'days' => 14],
            ];
            $medStatuses = ['active', 'recovering', 'returned', 'long_term'];

            foreach ($teams as $team) {
                $teamPlayers = Player::where('team_id', $team->id)->get();
                $recordsCount = rand(2, 3);

                for ($r = 0; $r < $recordsCount; $r++) {
                    $inj = $injuries[array_rand($injuries)];
                    $player = $teamPlayers->random();
                    $injuryDate = now()->subDays(rand(5, 60));
                    $expectedReturn = (clone $injuryDate)->addDays($inj['days']);

                    TeamMedicalRecord::create([
                        'team_id' => $team->id,
                        'player_id' => $player->id,
                        'record_type' => 'injury',
                        'injury_name' => $inj['name'],
                        'severity' => $inj['severity'],
                        'status' => $medStatuses[array_rand($medStatuses)],
                        'injury_date' => $injuryDate->format('Y-m-d'),
                        'expected_return' => $expectedReturn->format('Y-m-d'),
                        'treatment' => 'علاج طبيعي وتأهيل تحت إشراف طبيب الفريق',
                        'notes' => 'متابعة دورية كل أسبوع',
                    ]);
                }
            }

            // ── 15. Team Season Stats ──────────────────────────────────────
            $this->command->info('Creating team season stats...');

            foreach ($competitions as $comp) {
                foreach ($teams as $team) {
                    $matchesPlayed = rand(4, 12);
                    $wins = rand(0, $matchesPlayed);
                    $draws = rand(0, $matchesPlayed - $wins);
                    $losses = $matchesPlayed - $wins - $draws;
                    $goalsFor = $wins * rand(1, 3) + $draws * rand(0, 1) + rand(1, 5);
                    $goalsAgainst = $losses * rand(1, 2) + $draws * rand(0, 1) + rand(0, 3);
                    $points = $wins * 3 + $draws;

                    TeamSeasonStat::create([
                        'team_id' => $team->id,
                        'competition_id' => $comp->id,
                        'season_year' => 2026,
                        'matches_played' => $matchesPlayed,
                        'wins' => $wins,
                        'draws' => $draws,
                        'losses' => $losses,
                        'goals_for' => $goalsFor,
                        'goals_against' => $goalsAgainst,
                        'clean_sheets' => rand(0, (int) ceil($matchesPlayed / 2)),
                        'points' => $points,
                        'yellow_cards' => rand(5, 30),
                        'red_cards' => rand(0, 4),
                        'possession_avg' => round(rand(35, 65) / 10, 1),
                        'shots_per_match' => round(rand(8, 18) / 10, 1),
                    ]);
                }
            }

            // ── 16. Player Season Stats ────────────────────────────────────
            $this->command->info('Creating player season stats...');

            foreach ($competitions as $comp) {
                foreach ($teams as $team) {
                    $teamPlayers = Player::where('team_id', $team->id)->get();

                    foreach ($teamPlayers as $player) {
                        $matchesPlayed = rand(3, 12);
                        $isAttacker = in_array($player->position_id, [9, 10, 11, 8]);

                        PlayerSeasonStat::create([
                            'player_id' => $player->id,
                            'competition_id' => $comp->id,
                            'season_year' => 2026,
                            'matches_played' => $matchesPlayed,
                            'matches_started' => rand(1, $matchesPlayed),
                            'minutes_played' => $matchesPlayed * rand(45, 90),
                            'goals' => $isAttacker ? rand(2, 15) : rand(0, 5),
                            'assists' => $isAttacker ? rand(1, 8) : rand(0, 4),
                            'yellow_cards' => rand(0, 5),
                            'red_cards' => rand(0, 1),
                            'saves' => $player->position_id === 1 ? rand(10, 60) : 0,
                            'clean_sheets' => $player->position_id === 1 ? rand(0, 5) : 0,
                            'tackles' => $isAttacker ? rand(2, 10) : rand(10, 40),
                            'interceptions' => rand(5, 25),
                            'key_passes' => $isAttacker ? rand(5, 20) : rand(2, 10),
                            'dribbles' => $isAttacker ? rand(5, 30) : rand(1, 10),
                        ]);
                    }
                }
            }

            $this->command->info('All test data seeded successfully!');
        });
    }

    private function getPlayerName(int $index): string
    {
        $names = [
            'ياسر القحطاني', 'سالم الدوسري', 'ناصر العويرضي',
            'عمر السومة', 'دجانيني', 'عبدالله الحافظ',
            'يحيى الشهري', 'خالد السالم', 'محمد البريك',
            'سلمان الفرج', 'نواف العابد', 'عبدالرحمن البراق',
            'فهد المولد', 'عمر هوساوي', 'محمد فوزير',
            'سعود عبدالحميد', 'حسن معاذ', 'فهد الشمري',
            'ماجد عبدالله', 'خالد البلوشي', 'أحمد حجازي',
            'معاذ القحطاني', 'تيسير الجاسم', 'ناصر الشمري',
        ];

        return $names[$index] ?? 'لاعب رقم ' . ($index + 1);
    }

    private function getPlayerNumber(int $positionInTeam): int
    {
        $numbers = [1, 10, 9];
        return $numbers[$positionInTeam] ?? ($positionInTeam + 1);
    }
}
