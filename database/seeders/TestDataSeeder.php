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
use App\Models\Registration;
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
use App\Models\News;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Profile;
use App\Models\UserPreference;
use App\Models\SecuritySetting;
use App\Models\Activity;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    private array $teamDefs = [
        ['key' => 'HL',  'name' => 'الهلال'],
        ['key' => 'NSR', 'name' => 'النصر'],
        ['key' => 'ITT', 'name' => 'الاتحاد'],
        ['key' => 'AHL', 'name' => 'الأهلي'],
        ['key' => 'SHB', 'name' => 'الشباب'],
        ['key' => 'TAW', 'name' => 'التعاون'],
        ['key' => 'FHA', 'name' => 'الفيحاء'],
        ['key' => 'WHD', 'name' => 'الوحدة'],
    ];

    private array $squads = [
        'HL' => [
            'ياسر القحطاني', 'سالم الدوسري', 'حسان تمبكتي', 'علي البليهي', 'سلمان الفرج',
            'ناصر الدوسري', 'مالكوم أوليفيرا', 'ألكسندر ميتروفيتش', 'روبن نيفيز', 'محمد العويس',
            'خالد الغنام', 'عبدالله الحمدان', 'محمد كنو', 'سعد الناصر', 'مشعل العنزي',
            'فواز الطريس', 'عبدالرحمن الدخيل', 'سامي النجعي', 'نواف بوشل', 'محمد جحفلي',
            'تركي المطيري', 'عبدالإله المالكي', 'حمد اليامي',
        ],
        'NSR' => [
            'كريستيانو رونالدو', 'ساديو ماني', 'أندرسون تاليسكا', 'مارسيلو بروزوفيتش', 'أيمن يحيى',
            'سلطان الغنام', 'عبدالإله العمري', 'أليكس تيليس', 'أوتافيو مونتيرو', 'محمد الفهيد',
            'عبدالرحمن غريب', 'خالد الشنيف', 'عبدالله السبيعي', 'نواف العقيدي', 'مختار علي',
            'حمد المنصور', 'عبدالعزيز العليوه', 'محمد البريك', 'علي لاجامي', 'وليد عبدالله',
            'عبدالمجيد الصليهم', 'أيمن السليمان', 'فهد الجميعة',
        ],
        'ITT' => [
            'كريم بنزيما', 'نغولو كانتي', 'فابينيو تافاريس', 'جوتا فيليبي', 'عبدالرزاق حمدالله',
            'مروان الصحفي', 'حسن كادش', 'أحمد بامسعود', 'فهد المولد', 'مارسيلو غروهي',
            'عبدالله الجدعاني', 'حمدان الشمراني', 'زياد الصحفي', 'مهند الشنقيطي', 'عمر هوساوي',
            'سعد آل منصور', 'علي الشيخي', 'نور الدين مرابط', 'رائد العلي', 'عبدالله الجبرين',
            'صالح العمري', 'تركي الجعدان', 'ياسر الشهراني',
        ],
        'AHL' => [
            'رياض محرز', 'روبيرتو فيرمينو', 'فرانك كيسي', 'إدوارد ميندي', 'علي الأسمري',
            'عبدالباسط هوساوي', 'إبراهيم الزبيدي', 'فهد الرشيدي', 'محمد آل فتيل', 'عبدالله العمار',
            'عبدالكريم القحطاني', 'بندر الوقداني', 'غازي المزيدي', 'حسين المقهوي', 'سعيد الربيعي',
            'عبدالرحمن البيشي', 'نايف الموسى', 'ماجد هوساوي', 'خالد الزيلعي', 'سالم النجدي',
            'أحمد الحبيب', 'تركي الغامدي', 'فيصل العيسى',
        ],
        'SHB' => [
            'إيفر بانيغا', 'أوديون إيغالو', 'كارلوس جونيور', 'سيبا فيرنانديز', 'حسين القحطاني',
            'نادر المطيري', 'حبيب الوطيان', 'إيغور بانيتش', 'محمد الشلهوب', 'عبدالله الحافظ',
            'رياض شراحيلي', 'معاذ فقيهي', 'ناصيف البويك', 'فهد العتيبي', 'صقر عطيف',
            'علي آل حافظ', 'خالد الدبيش', 'عبدالله الشمراني', 'حسن معاذ', 'سعود العنزي',
            'أحمد الأحمري', 'مشعل القحطاني', 'بدر الرشيدي',
        ],
        'TAW' => [
            'ألفارو ميدران', 'سيدو بيه', 'موسى ناجي', 'مايلسون ديفيد', 'أسامة الخلف',
            'أحمد الحربي', 'رائد الشمري', 'عبدالعزيز الحربي', 'ناصر الحمدان', 'محمد الصيعري',
            'عبدالملك الخيبري', 'فواز الصحفي', 'عبدالرحمن الشهري', 'سلطان اليامي', 'سعد العتيبي',
            'عبدالعزيز القحطاني', 'فيصل الغنام', 'محمد البقعاوي', 'تركي الشويعر', 'نواف العلي',
            'خالد الحربي', 'صالح الجعيد', 'أيمن آل عباس',
        ],
        'FHA' => [
            'أوزفالدو فيليبي', 'فلاديمير ستويكوفيتش', 'عبدالله الخيبري', 'سامي الخيبري', 'محمد القبيعي',
            'مختار فلاته', 'عبدالعزيز الشلهوب', 'وليد اليامي', 'أنس العمران', 'حمدان الشمري',
            'عبدالله الظفيري', 'نواف الصبحي', 'فهد الدوسري', 'مشاري العنزي', 'سعد السلطان',
            'إبراهيم القحطاني', 'خالد الرشيدي', 'عبدالرحمن الربيع', 'عبدالعزيز الشهراني', 'علي العبدالله',
            'تركي الخليفي', 'ناصر السهلي', 'منصور الحميد',
        ],
        'WHD' => [
            'فيليبي كوستا', 'كريم رقيق', 'نور الدين عمار', 'عبدالله السفري', 'علي العبسي',
            'جابر الجابري', 'ماجد الخيبري', 'منصور الراشدي', 'أنس الشريف', 'عبدالرحمن الحريب',
            'عبدالعزيز نور', 'مشعل النخلي', 'بدر القحطاني', 'حمزة الجابر', 'فهد الحربي',
            'عبدالله عطيف', 'سامي الجابر', 'نواف الدوسري', 'راشد المطيري', 'عمر المولد',
            'عبدالهادي الصقير', 'فيصل الخميس', 'محمد الحارثي',
        ],
    ];

    private array $nationalities = [
        'السعودية','مصر','المغرب','تونس','الجزائر','العراق','الكويت','قطر','الإمارات','عمان','البحرين',
        'السنغال','مالي','نيجيريا','الكاميرون','ساحل العاج','غانا','البرازيل','الأرجنتين','فرنسا',
    ];

    private array $bios = [
        'حارس مرمى متألق يتميز بردود فعل سريعة وتمركز ممتاز',
        'مدافع قوي يمتاز بالكرات الهوائية والتدخلات النظيفة',
        'ظهير أيمن سريع يجيد التقدم الهجومي والتمرير العرضي',
        'مدافع مركزي يتميز بالقراءة التكتيكية وبناء الهجمات',
        'ظهير أيسر متوازن يجيد الدفاع والهجوم',
        'وسط دفاعي يجيد قطع الكرات وتوزيع اللعب',
        'وسط متعدد المهام يجيد الربط بين الدفاع والهجوم',
        'وسط هجومي مبدع يملك رؤية استثنائية وتمريرات حاسمة',
        'جناح أيمن سريع يجيد المراوغة والتسديد',
        'جناح أيسر مهارة عالية في التوغل من الأطراف',
        'مهاجم صريح يجيد إنهاء الهجمات والتوقيت المثالي',
        'مهاجم مركزي قوي بدنياً يجيد اللعب بالرأس',
        'صانع ألعاب يتمتع بمهارات فردية عالية',
        'لاعب خبرة كبيرة يقود خط الوسط بذكاء وحنكة',
    ];

    private array $footOptions = ['right', 'left', 'both'];

    public function run(): void
    {
        DB::transaction(function () {
            $admin = $this->createAdmin();
            $organizer = $this->createOrganizer();
            $coach = $this->createCoach();
            $teams = $this->createTeams();
            $captains = $this->createCaptains($teams);
            $this->linkTeamCaptains($teams, $captains);
            $allPlayers = $this->createPlayers($teams);
            $compTypes = $this->createCompetitionTypes();
            $competitions = $this->createCompetitions($compTypes, $admin);
            $this->createRegistrations($competitions, $teams);
            $allMatches = $this->createMatches($competitions, $teams);
            $this->createMatchLineups($allMatches, $allPlayers);
            $this->createMatchEvents($allMatches, $allPlayers);
            $this->createMatchStats($allMatches);
            $this->createTeamFormations($teams);
            $this->createTeamStaff($teams);
            $this->createTeamTactics($teams);
            $this->createMedicalRecords($teams, $allPlayers);
            $this->createSeasonStats($competitions, $teams, $allPlayers);
            $this->createNews();
            $this->createPlansAndSubscriptions($admin);
            $this->createUserProfiles($admin, $organizer, $coach);
            $this->createUserPreferences($admin, $organizer, $coach);
            $this->createSecuritySettings($admin, $organizer, $coach);
            $this->createActivities($admin);
        });
    }

    private function createAdmin(): User
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@tournatak.com'],
            [
                'name' => 'المدير العام للنظام', 'username' => 'admin',
                'password' => bcrypt('password'), 'role' => 'admin',
                'is_verified' => true, 'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');
        return $admin;
    }

    private function createOrganizer(): User
    {
        $org = User::firstOrCreate(
            ['email' => 'ahmed@tournatak.com'],
            [
                'name' => 'أحمد المنظم', 'username' => 'ahmed_org',
                'password' => bcrypt('password'), 'role' => 'organizer',
                'is_verified' => true, 'email_verified_at' => now(),
            ]
        );
        $org->assignRole('organizer');
        return $org;
    }

    private function createCoach(): User
    {
        $coach = User::firstOrCreate(
            ['email' => 'hassan@tournatak.com'],
            [
                'name' => 'حسن المدرب', 'username' => 'hassan_coach',
                'password' => bcrypt('password'), 'role' => 'coach',
                'is_verified' => true, 'email_verified_at' => now(),
            ]
        );
        $coach->assignRole('coach');
        return $coach;
    }

    private function createTeams(): array
    {
        $this->command->info('Creating 8 teams...');
        $teams = [];
        foreach ($this->teamDefs as $td) {
            $teams[] = Team::create(['name' => $td['name'], 'points' => rand(5, 35)]);
        }
        return $teams;
    }

    private function createCaptains(array $teams): array
    {
        $captains = [];
        foreach ($teams as $idx => $team) {
            $captains[] = User::create([
                'name' => 'كابتن ' . $team->name,
                'username' => 'cap_' . $this->teamDefs[$idx]['key'],
                'email' => 'cap_' . $this->teamDefs[$idx]['key'] . '@test.com',
                'password' => bcrypt('password'), 'role' => 'captain',
                'is_verified' => true, 'email_verified_at' => now(),
            ])->assignRole('captain');
        }
        return $captains;
    }

    private function linkTeamCaptains(array $teams, array $captains): void
    {
        foreach ($teams as $idx => $team) {
            $team->update(['captain_id' => $captains[$idx]->id]);
        }
    }

    private function createPlayers(array $teams): array
    {
        $this->command->info('Creating 23 players per team (184 total)...');
        $positions = Position::where('sport_type', 'football')->get()->keyBy('abbreviation');
        if ($positions->isEmpty()) {
            $this->command->error('No football positions found. Run PositionSeeder first.');
            return [];
        }

        $usedNames = [];
        $allPlayers = [];

        $positionMap = [
            1 => 'GK', 2 => 'RB', 3 => 'CB', 4 => 'CB', 5 => 'LB',
            6 => 'CDM', 7 => 'CM', 8 => 'CM', 9 => 'CAM', 10 => 'RW', 11 => 'ST',
            12 => 'GK', 13 => 'CB', 14 => 'LB', 15 => 'RB', 16 => 'CDM',
            17 => 'CM', 18 => 'CAM', 19 => 'LW', 20 => 'LB', 21 => 'CB',
            22 => 'RW', 23 => 'GK',
        ];

        foreach ($teams as $tIdx => $team) {
            $key = $this->teamDefs[$tIdx]['key'];
            $names = $this->squads[$key];

            foreach ($names as $pIdx => $name) {
                if (in_array($name, $usedNames)) {
                    $this->command->warn("  Duplicate name '$name' — adding suffix");
                    $name .= ' ' . $key;
                }
                $usedNames[] = $name;

                $user = User::create([
                    'name' => $name,
                    'username' => 'pl_' . $key . '_' . ($pIdx + 1),
                    'email' => 'pl_' . $key . '_' . ($pIdx + 1) . '@test.com',
                    'password' => bcrypt('password'), 'role' => 'player',
                    'is_verified' => true, 'email_verified_at' => now(),
                ]);
                $user->assignRole('player');

                $number = $this->getShirtNumber($pIdx);
                $abbr = $positionMap[$number] ?? 'ST';
                $positionId = isset($positions[$abbr]) ? $positions[$abbr]->id : $positions->first()->id;
                $dob = Carbon::create(rand(1990, 2005), rand(1, 12), rand(1, 28));

                $allPlayers[] = Player::create([
                    'user_id' => $user->id,
                    'team_id' => $team->id,
                    'number' => $number,
                    'position_id' => $positionId,
                    'sport_type' => 'football',
                    'nationality' => $this->nationalities[array_rand($this->nationalities)],
                    'height' => rand(165, 198),
                    'weight' => rand(60, 95),
                    'foot' => $this->footOptions[array_rand($this->footOptions)],
                    'date_of_birth' => $dob->format('Y-m-d'),
                    'bio' => $this->bios[$pIdx % count($this->bios)],
                    'is_captain' => $pIdx === 0,
                ]);
            }
        }

        return $allPlayers;
    }

    private function getShirtNumber(int $index): int
    {
        $numbers = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23];
        return $numbers[$index] ?? ($index + 1);
    }

    private function createCompetitionTypes(): array
    {
        $this->command->info('Creating competition types...');
        $subtype = CompetitionSubtype::firstOrCreate(
            ['name' => 'الدوري'], ['en_name' => 'League']
        );

        $types = [];
        $data = [
            ['name' => 'دوري المحترفين', 'slug' => 'pro-league'],
            ['name' => 'كأس الأبطال',    'slug' => 'champions-cup'],
            ['name' => 'الدوري الممتاز',  'slug' => 'premier-league'],
        ];
        foreach ($data as $td) {
            $types[] = CompetitionType::firstOrCreate(
                ['slug' => $td['slug']],
                ['subtype_id' => $subtype->id, 'name' => $td['name'],
                 'sort_order' => count($types) + 1, 'is_active' => true]
            );
        }
        return $types;
    }

    private function createCompetitions(array $compTypes, User $admin): array
    {
        $this->command->info('Creating 3 competitions...');
        $comps = [];
        $data = [
            ['type' => $compTypes[0], 'name' => 'دوري المحترفين 2026', 'status' => 'ongoing',  'start' => '2026-01-15', 'end' => '2026-06-30'],
            ['type' => $compTypes[1], 'name' => 'كأس الأبطال 2026',    'status' => 'ongoing',  'start' => '2026-02-10', 'end' => '2026-07-15'],
            ['type' => $compTypes[2], 'name' => 'الدوري الممتاز 2026', 'status' => 'upcoming', 'start' => '2026-09-01', 'end' => '2027-01-31'],
        ];
        $admin = User::where('email', 'admin@tournatak.com')->first() ?? $admin;
        foreach ($data as $cd) {
            $comps[] = Competition::create([
                'type_id' => $cd['type']->id, 'subtype_id' => $cd['type']->subtype_id,
                'organizer_id' => $admin->id, 'name' => $cd['name'],
                'status' => $cd['status'], 'approval_status' => 'approved',
                'start_date' => $cd['start'], 'end_date' => $cd['end'],
                'location' => 'الرياض', 'format' => 'league',
            ]);
        }
        return $comps;
    }

    private function createRegistrations(array $competitions, array $teams): void
    {
        $this->command->info('Creating team registrations...');
        foreach ($competitions as $comp) {
            foreach ($teams as $team) {
                Registration::firstOrCreate(
                    ['competition_id' => $comp->id, 'team_id' => $team->id],
                    ['status' => 'approved']
                );
            }
        }
    }

    private function createMatches(array $competitions, array $teams): array
    {
        $this->command->info('Creating 20 matches with realistic dates...');
        $teamIds = array_map(fn($t) => $t->id, $teams);
        $teamCount = count($teamIds);
        $allMatches = [];

        $usedDates = [];
        $teamSchedule = [];

        $venues = ['استاد الملك فهد','استاد مدينة الأمير فيصل بن فهد','استاد الملك عبدالله','استاد الأمير محمد بن فهد'];
        $referees = ['محمد الهويش','خالد الطويرش','عبدالعزيز الدخيل','فهد المرداسي','ماجد الشمراني'];
        $today = today();

        foreach ($competitions as $cIdx => $comp) {
            $matchCount = $cIdx < 2 ? 8 : 4;
            $pastCount = intval(floor($matchCount * 0.5));
            $liveCount = $cIdx === 0 ? 2 : 0;

            for ($m = 0; $m < $matchCount; $m++) {
                $t1Idx = ($cIdx * 7 + $m * 3) % $teamCount;
                $t2Idx = ($cIdx * 7 + $m * 3 + 1 + ($m % 2)) % $teamCount;
                if ($t1Idx === $t2Idx) $t2Idx = ($t2Idx + 1) % $teamCount;

                $t1Id = $teamIds[$t1Idx];
                $t2Id = $teamIds[$t2Idx];

                if ($m < $pastCount) {
                    $status = 'completed';
                    $dayOffset = -rand(5, 90);
                    $hour = 20;
                    $minute = 0;
                } elseif ($m < $pastCount + $liveCount) {
                    $status = 'in_progress';
                    $dayOffset = 0;
                    $hour = (int) now()->subMinutes(rand(30, 90))->format('H');
                    $minute = (int) now()->subMinutes(rand(30, 90))->format('i');
                } else {
                    $status = 'scheduled';
                    $dayOffset = rand(3, 60);
                    $hour = rand(18, 22);
                    $minute = 0;
                }

                $date = $this->pickUniqueDateRelative($dayOffset, $usedDates, $teamSchedule, $t1Id, $t2Id);
                $usedDates[] = $date;
                $teamSchedule[$t1Id][] = $date;
                $teamSchedule[$t2Id][] = $date;

                $dateTime = $date . ' ' . sprintf('%02d:%02d:00', $hour, $minute);

                if ($status === 'completed') {
                    [$s1, $s2] = $this->generateScore();
                    $attendance = rand(5000, 60000);
                    $extraData = [];
                } elseif ($status === 'in_progress') {
                    [$s1, $s2] = $this->generateScore();
                    $s1 = min($s1, rand(0, 3));
                    $s2 = min($s2, rand(0, 3));
                    $attendance = rand(3000, 40000);
                    $matchStart = now()->subMinutes(rand(30, 90));
                    $extraData = [
                        'phase' => 'second_half',
                        'first_half_started_at' => (clone $matchStart)->subSeconds(rand(0, 10))->toIso8601String(),
                        'second_half_started_at' => (clone $matchStart)->addMinutes(rand(15, 25))->toIso8601String(),
                    ];
                } else {
                    $s1 = $s2 = null;
                    $attendance = null;
                    $extraData = [];
                }

                $allMatches[] = Match_::create([
                    'competition_id' => $comp->id,
                    'team1_id' => $t1Id, 'team2_id' => $t2Id,
                    'match_date' => $dateTime,
                    'score_team1' => $s1, 'score_team2' => $s2,
                    'status' => $status,
                    'extra_data' => $extraData,
                    'venue' => $venues[array_rand($venues)],
                    'referee' => $referees[array_rand($referees)],
                    'attendance' => $attendance,
                ]);
            }
        }

        return $allMatches;
    }

    private function generateScore(): array
    {
        $dist = [
            [0,0,10],[1,0,15],[0,1,9],[1,1,14],[2,1,13],[1,2,9],
            [2,0,8],[0,2,5],[3,1,5],[2,2,4],[3,0,3],[0,3,2],[3,2,2],[2,3,1],
        ];
        $r = rand(1, 100);
        foreach ($dist as $d) {
            $r -= $d[2];
            if ($r <= 0) return [$d[0], $d[1]];
        }
        return [rand(0, 4), rand(0, 4)];
    }

    private function pickUniqueDateRelative(int $dayOffset, array $usedDates, array $teamSchedule, int $t1Id, int $t2Id): string
    {
        $candidate = today()->addDays($dayOffset);

        $maxAttempts = 30;
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $dateStr = $candidate->format('Y-m-d');

            $usedByT1 = in_array($dateStr, $teamSchedule[$t1Id] ?? []);
            $usedByT2 = in_array($dateStr, $teamSchedule[$t2Id] ?? []);
            $alreadyUsed = in_array($dateStr, $usedDates);

            if (!$usedByT1 && !$usedByT2 && !$alreadyUsed) {
                return $dateStr;
            }
            $dayOffset >= 0 ? $candidate->addDay() : $candidate->subDay();
        }

        return $candidate->format('Y-m-d');
    }

    private function createMatchLineups(array $allMatches, array $allPlayers): void
    {
        $this->command->info('Creating match lineups with formation slots...');
        $teamFormations = Formation::where('is_default', true)->get()->keyBy('team_id');

        foreach ($allMatches as $match) {
            foreach ([$match->team1_id, $match->team2_id] as $teamId) {
                $pool = array_values(array_filter($allPlayers, fn($p) => $p->team_id === $teamId));
                $formation = $teamFormations->get($teamId);
                $slotCount = $formation ? count($formation->positions_data) : 11;
                $starterCount = min($slotCount, count($pool));

                foreach ($pool as $idx => $player) {
                    $isStarter = $idx < $starterCount;
                    MatchLineup::create([
                        'match_id' => $match->id,
                        'player_id' => $player->id,
                        'team_id' => $teamId,
                        'is_starter' => $isStarter,
                        'formation_slot' => $isStarter ? $idx : null,
                        'jersey_number' => $player->number,
                        'is_captain' => $idx === 0,
                        'minute_in' => 0,
                        'minute_out' => ($match->status === 'completed' && $isStarter) ? 90 : null,
                    ]);
                }
            }
        }
    }

    private function createMatchEvents(array $allMatches, array $allPlayers): void
    {
        $this->command->info('Creating match events from lineups...');
        foreach ($allMatches as $match) {
            if ($match->status !== 'completed') continue;

            $lineups = MatchLineup::where('match_id', $match->id)->get();

            $t1Starters = $lineups->where('team_id', $match->team1_id)->where('is_starter', true)->pluck('player_id')->toArray();
            $t2Starters = $lineups->where('team_id', $match->team2_id)->where('is_starter', true)->pluck('player_id')->toArray();
            $t1Subs = $lineups->where('team_id', $match->team1_id)->where('is_starter', false)->pluck('player_id')->toArray();
            $t2Subs = $lineups->where('team_id', $match->team2_id)->where('is_starter', false)->pluck('player_id')->toArray();

            $this->addGoalsForTeam($match, $t1Starters, $match->score_team1 ?? 0, $match->team1_id);
            $this->addGoalsForTeam($match, $t2Starters, $match->score_team2 ?? 0, $match->team2_id);

            $yc = rand(1, 5);
            for ($y = 0; $y < $yc; $y++) {
                $pool = $y % 2 === 0 ? $t1Starters : $t2Starters;
                if (empty($pool)) continue;
                MatchEvent::create([
                    'match_id' => $match->id,
                    'team_id' => $y % 2 === 0 ? $match->team1_id : $match->team2_id,
                    'player_id' => $pool[array_rand($pool)],
                    'event_type' => 'yellow_card', 'minute' => rand(10, 85),
                ]);
            }

            $subs = rand(1, 3);
            for ($s = 0; $s < $subs; $s++) {
                $starterPool = $s % 2 === 0 ? $t1Starters : $t2Starters;
                $subPool = $s % 2 === 0 ? $t1Subs : $t2Subs;
                if (empty($starterPool) || empty($subPool)) continue;
                $out = $starterPool[array_rand($starterPool)];
                $in = $subPool[array_rand($subPool)];
                $min = rand(45, 80);
                $teamId = $s % 2 === 0 ? $match->team1_id : $match->team2_id;
                MatchEvent::create(['match_id' => $match->id, 'team_id' => $teamId, 'player_id' => $in, 'event_type' => 'substitution_in', 'minute' => $min, 'related_player_id' => $out]);
                MatchEvent::create(['match_id' => $match->id, 'team_id' => $teamId, 'player_id' => $out, 'event_type' => 'substitution_out', 'minute' => $min, 'related_player_id' => $in]);
            }
        }
    }

    private function addGoalsForTeam(Match_ $match, array $starterIds, int $goalCount, int $teamId): void
    {
        if ($goalCount <= 0 || empty($starterIds)) return;
        for ($g = 0; $g < $goalCount; $g++) {
            $scorerId = $starterIds[array_rand($starterIds)];
            $min = rand(3, 89);
            MatchEvent::create([
                'match_id' => $match->id, 'team_id' => $teamId,
                'player_id' => $scorerId, 'event_type' => 'goal',
                'minute' => $min, 'added_time' => rand(0, 5),
            ]);
            if (count($starterIds) > 1) {
                $assistId = $starterIds[array_rand($starterIds)];
                if ($assistId !== $scorerId) {
                    MatchEvent::create([
                        'match_id' => $match->id, 'team_id' => $teamId,
                        'player_id' => $assistId, 'event_type' => 'assist',
                        'minute' => $min,
                    ]);
                }
            }
        }
    }

    private function createMatchStats(array $allMatches): void
    {
        $this->command->info('Creating match stats correlated with events...');
        foreach ($allMatches as $match) {
            if ($match->status !== 'completed') continue;

            $events = MatchEvent::where('match_id', $match->id)->get();

            foreach ([$match->team1_id, $match->team2_id] as $tid) {
                $teamEvents = $events->where('team_id', $tid);
                $actualYellows = $teamEvents->whereIn('event_type', ['yellow_card', 'second_yellow'])->count();
                $actualReds = $teamEvents->where('event_type', 'red_card')->count();

                $pos = rand(35, 65);
                $shots = rand(4, 20);
                $passes = rand(250, 600);

                MatchStat::create([
                    'match_id' => $match->id, 'team_id' => $tid,
                    'possession' => $pos, 'shots_total' => $shots,
                    'shots_on_target' => max(1, (int)($shots * rand(25, 55) / 100)),
                    'shots_off_target' => max(0, $shots - (int)($shots * rand(25, 55) / 100)),
                    'corners' => rand(1, 10), 'fouls' => rand(8, 22), 'offsides' => rand(0, 5),
                    'yellow_cards' => $actualYellows, 'red_cards' => $actualReds,
                    'passes_total' => $passes, 'passes_accurate' => (int)($passes * rand(65, 90) / 100),
                    'tackles' => rand(10, 35), 'saves' => rand(2, 8),
                    'hit_woodwork' => rand(0, 2), 'blocked_shots' => rand(0, 5),
                ]);
            }
        }
    }

    private function createTeamFormations(array $teams): void
    {
        $this->command->info('Creating team formations (2 per team)...');
        $formation442 = [
            ['position' => 'GK', 'x' => 50, 'y' => 92],
            ['position' => 'RB', 'x' => 78, 'y' => 75],
            ['position' => 'CB', 'x' => 58, 'y' => 78],
            ['position' => 'CB', 'x' => 42, 'y' => 78],
            ['position' => 'LB', 'x' => 22, 'y' => 75],
            ['position' => 'RM', 'x' => 78, 'y' => 52],
            ['position' => 'CM', 'x' => 58, 'y' => 55],
            ['position' => 'CM', 'x' => 42, 'y' => 55],
            ['position' => 'LM', 'x' => 22, 'y' => 52],
            ['position' => 'ST', 'x' => 58, 'y' => 28],
            ['position' => 'ST', 'x' => 42, 'y' => 28],
        ];
        $formation433 = [
            ['position' => 'GK', 'x' => 50, 'y' => 92],
            ['position' => 'RB', 'x' => 78, 'y' => 75],
            ['position' => 'CB', 'x' => 58, 'y' => 78],
            ['position' => 'CB', 'x' => 42, 'y' => 78],
            ['position' => 'LB', 'x' => 22, 'y' => 75],
            ['position' => 'CM', 'x' => 50, 'y' => 60],
            ['position' => 'CM', 'x' => 35, 'y' => 55],
            ['position' => 'CM', 'x' => 65, 'y' => 55],
            ['position' => 'RW', 'x' => 80, 'y' => 30],
            ['position' => 'ST', 'x' => 50, 'y' => 25],
            ['position' => 'LW', 'x' => 20, 'y' => 30],
        ];
        $formation4231 = [
            ['position' => 'GK', 'x' => 50, 'y' => 92],
            ['position' => 'RB', 'x' => 78, 'y' => 75],
            ['position' => 'CB', 'x' => 58, 'y' => 78],
            ['position' => 'CB', 'x' => 42, 'y' => 78],
            ['position' => 'LB', 'x' => 22, 'y' => 75],
            ['position' => 'CDM', 'x' => 38, 'y' => 62],
            ['position' => 'CDM', 'x' => 62, 'y' => 62],
            ['position' => 'RAM', 'x' => 75, 'y' => 45],
            ['position' => 'CAM', 'x' => 50, 'y' => 45],
            ['position' => 'LAM', 'x' => 25, 'y' => 45],
            ['position' => 'ST', 'x' => 50, 'y' => 25],
        ];

        foreach ($teams as $idx => $team) {
            Formation::create([
                'team_id' => $team->id, 'name' => '4-4-2 أساسي',
                'sport_type' => 'football', 'formation_code' => '4-4-2',
                'positions_data' => $formation442,
                'description' => 'التشكيل الأساسي - 4-4-2 متوازن',
                'is_default' => true, 'is_active' => true,
            ]);

            $alt = $idx % 2 === 0 ? $formation433 : $formation4231;
            $code = $idx % 2 === 0 ? '4-3-3' : '4-2-3-1';
            $name = $idx % 2 === 0 ? '4-3-3 هجومي' : '4-2-3-1 مرن';
            Formation::create([
                'team_id' => $team->id, 'name' => $name,
                'sport_type' => 'football', 'formation_code' => $code,
                'positions_data' => $alt,
                'description' => 'تشكيل بديل',
                'is_default' => false, 'is_active' => true,
            ]);
        }
    }

    private function createTeamStaff(array $teams): void
    {
        $this->command->info('Creating team staff...');
        $roles = ['head_coach', 'assistant_coach', 'fitness_coach'];
        foreach ($teams as $tIdx => $team) {
            $key = $this->teamDefs[$tIdx]['key'];
            foreach ($roles as $s => $role) {
                $u = User::firstOrCreate(
                    ['email' => "staff_{$key}_{$s}@test.com"],
                    [
                        'name' => match ($role) {
                            'head_coach' => "مدرب {$team->name}",
                            'assistant_coach' => "مساعد مدرب {$team->name}",
                            'fitness_coach' => "مدرب لياقة {$team->name}",
                        },
                        'username' => "staff_{$key}_{$s}",
                        'password' => bcrypt('password'), 'role' => 'user',
                        'is_verified' => true, 'email_verified_at' => now(),
                    ]
                );
                TeamStaff::create([
                    'team_id' => $team->id, 'user_id' => $u->id,
                    'staff_role' => $role,
                    'start_date' => now()->subMonths(rand(3, 24)),
                    'is_active' => true,
                ]);
            }
        }
    }

    private function createTeamTactics(array $teams): void
    {
        $this->command->info('Creating team tactics...');
        $pressing = ['high', 'medium', 'low', 'mixed'];
        $buildUp = ['from_back', 'quick_counter', 'long_ball', 'mixed'];
        $defense = ['zone', 'man_to_man', 'mixed'];
        $attack = ['wing_play', 'central', 'balanced', 'counter_attack'];
        foreach ($teams as $team) {
            TeamTactic::create([
                'team_id' => $team->id, 'name' => "تكتيك {$team->name}",
                'pressing_style' => $pressing[array_rand($pressing)],
                'build_up_style' => $buildUp[array_rand($buildUp)],
                'defense_style' => $defense[array_rand($defense)],
                'attack_style' => $attack[array_rand($attack)],
                'formation_used' => '4-4-2',
                'notes' => 'تكتيك أساسي للموسم الحالي',
                'is_default' => true,
            ]);
        }
    }

    private function createMedicalRecords(array $teams, array $allPlayers): void
    {
        $this->command->info('Creating medical records...');
        $injuries = [
            ['name' => 'تمزق في العضلة الساقية', 'severity' => 'minor', 'days' => 14],
            ['name' => 'التواء في الكاحل', 'severity' => 'moderate', 'days' => 30],
            ['name' => 'إصابة في الرباط الصليبي', 'severity' => 'severe', 'days' => 180],
            ['name' => 'كسر في عظم مشط القدم', 'severity' => 'moderate', 'days' => 60],
            ['name' => 'كدمة في الركبة', 'severity' => 'minor', 'days' => 10],
            ['name' => 'تمزق في الغضروف', 'severity' => 'moderate', 'days' => 45],
            ['name' => 'إصابة في الكتف', 'severity' => 'minor', 'days' => 21],
            ['name' => 'شد عضلي في الفخذ', 'severity' => 'minor', 'days' => 7],
        ];
        $statuses = ['active', 'recovering', 'returned', 'long_term'];

        foreach ($teams as $team) {
            $pool = array_values(array_filter($allPlayers, fn($p) => $p->team_id === $team->id));
            $count = rand(3, 6);
            for ($r = 0; $r < $count; $r++) {
                $inj = $injuries[array_rand($injuries)];
                $player = $pool[array_rand($pool)];
                $date = now()->subDays(rand(5, 90));
                TeamMedicalRecord::create([
                    'team_id' => $team->id, 'player_id' => $player->id,
                    'record_type' => 'injury', 'injury_name' => $inj['name'],
                    'severity' => $inj['severity'], 'status' => $statuses[array_rand($statuses)],
                    'injury_date' => $date->format('Y-m-d'),
                    'expected_return' => (clone $date)->addDays($inj['days'])->format('Y-m-d'),
                    'treatment' => 'علاج طبيعي وتأهيل',
                    'notes' => 'متابعة أسبوعية',
                    'reported_by' => User::where('role', 'admin')->first()?->id ?? 1,
                ]);
            }
        }
    }

    private function createSeasonStats(array $competitions, array $teams, array $allPlayers): void
    {
        $this->command->info('Creating season stats...');
        foreach ($competitions as $comp) {
            foreach ($teams as $team) {
                $mp = rand(4, 14);
                $w = rand(0, $mp); $d = rand(0, $mp - $w); $l = $mp - $w - $d;
                $gf = $w * rand(1, 3) + $d * rand(0, 1); $ga = $l * rand(1, 2) + $d * rand(0, 1);
                TeamSeasonStat::create([
                    'team_id' => $team->id, 'competition_id' => $comp->id,
                    'season_year' => 2026, 'matches_played' => $mp,
                    'wins' => $w, 'draws' => $d, 'losses' => $l,
                    'goals_for' => $gf, 'goals_against' => $ga,
                    'clean_sheets' => rand(0, (int)ceil($mp / 3)), 'points' => $w * 3 + $d,
                    'yellow_cards' => rand(5, 35), 'red_cards' => rand(0, 3),
                    'possession_avg' => round(rand(30, 65) / 10, 1),
                    'shots_per_match' => round(rand(8, 18) / 10, 1),
                ]);

                $pool = array_values(array_filter($allPlayers, fn($p) => $p->team_id === $team->id));
                foreach ($pool as $player) {
                    $pmp = rand(2, min($mp, 12));
                    $isAtt = in_array($player->position_id, [8,9,10,11]);
                    PlayerSeasonStat::create([
                        'player_id' => $player->id, 'competition_id' => $comp->id,
                        'season_year' => 2026, 'matches_played' => $pmp,
                        'matches_started' => rand(1, $pmp),
                        'minutes_played' => $pmp * rand(40, 90),
                        'goals' => $isAtt ? rand(1, 12) : rand(0, 3),
                        'assists' => $isAtt ? rand(1, 8) : rand(0, 3),
                        'yellow_cards' => rand(0, 6), 'red_cards' => rand(0, 1),
                        'saves' => $player->position_id === 1 ? rand(15, 70) : 0,
                        'clean_sheets' => $player->position_id === 1 ? rand(0, 6) : 0,
                        'tackles' => $isAtt ? rand(2, 12) : rand(8, 45),
                        'interceptions' => rand(5, 30), 'key_passes' => $isAtt ? rand(5, 25) : rand(2, 12),
                        'dribbles' => $isAtt ? rand(5, 35) : rand(1, 10),
                    ]);
                }
            }
        }
    }

    private function createNews(): void
    {
        $this->command->info('Creating news articles...');
        $articles = [
            ['title' => 'انطلاق الموسم الجديد لدوري المحترفين', 'content' => 'تنطلق منافسات الموسم الجديد لدوري المحترفين بمشاركة 16 نادياً. تشهد البطولة هذا الموسم تنافساً قوياً بين الأندية الكبرى.', 'type' => 'announcement'],
            ['title' => 'الهلال يتصدر ترتيب الدوري بعد الجولة العاشرة', 'content' => 'حقق نادي الهلال فوزاً كبيراً على منافسه في الجولة العاشرة ليعزز صدارته لترتيب الدوري.', 'type' => 'match'],
            ['title' => 'الاتحاد يتأهل إلى نصف نهائي الكأس', 'content' => 'تأهل نادي الاتحاد إلى نصف نهائي كأس الأبطال بعد فوزه في مباراة مثيرة.', 'type' => 'match'],
            ['title' => 'انتقالات شتوية قياسية في الدوري', 'content' => 'شهدت فترة الانتقالات الشتوية تحركات كبيرة للأندية لتعزيز صفوفها بلاعبين مميزين.', 'type' => 'general'],
            ['title' => 'تكريم نجوم الجولة الثانية عشرة', 'content' => 'تم اختيار أفضل لاعب وأفضل حارس وأفضل مدرب في الجولة الثانية عشرة.', 'type' => 'general'],
            ['title' => 'افتتاح أكاديمية جديدة للناشئين', 'content' => 'أعلن الاتحاد السعودي لكرة القدم عن افتتاح أكاديمية جديدة لتطوير مواهب الناشئين.', 'type' => 'announcement'],
        ];
        $admin = User::where('role', 'admin')->first();
        foreach ($articles as $a) {
            News::create([
                'title' => $a['title'], 'content' => $a['content'],
                'type' => $a['type'], 'is_active' => true,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    private function createPlansAndSubscriptions(User $admin): void
    {
        $this->command->info('Creating plans & subscriptions...');
        $plans = [
            ['name' => 'الباقة المجانية', 'price' => 0, 'duration_days' => 30, 'trial_days' => 0],
            ['name' => 'الباقة الأساسية', 'price' => 99, 'duration_days' => 30, 'trial_days' => 7],
            ['name' => 'الباقة المتقدمة', 'price' => 199, 'duration_days' => 30, 'trial_days' => 0],
            ['name' => 'الباقة السنوية', 'price' => 1999, 'duration_days' => 365, 'trial_days' => 14],
        ];
        foreach ($plans as $p) {
            Plan::create($p);
        }
        Subscription::create([
            'user_id' => $admin->id,
            'plan_id' => Plan::where('name', 'الباقة السنوية')->first()->id,
            'start_date' => now()->subMonths(2)->format('Y-m-d'),
            'end_date' => now()->addMonths(10)->format('Y-m-d'),
            'status' => 'active',
        ]);
    }

    private function createUserProfiles(User $admin, User $organizer, User $coach): void
    {
        Profile::firstOrCreate(['user_id' => $admin->id],    ['full_name' => 'المدير العام للنظام',  'profile_date_birth' => '1985-06-15']);
        Profile::firstOrCreate(['user_id' => $organizer->id],['full_name' => 'أحمد المنظم',          'profile_date_birth' => '1990-03-22']);
        Profile::firstOrCreate(['user_id' => $coach->id],    ['full_name' => 'حسن المدرب',           'profile_date_birth' => '1988-11-10']);
    }

    private function createUserPreferences(User $admin, User $organizer, User $coach): void
    {
        UserPreference::firstOrCreate(['user_id' => $admin->id],    ['locale' => 'ar', 'theme' => 'light',  'timezone' => 'Asia/Riyadh', 'density' => 'comfortable']);
        UserPreference::firstOrCreate(['user_id' => $organizer->id],['locale' => 'ar', 'theme' => 'dark',   'timezone' => 'Asia/Riyadh', 'density' => 'compact']);
        UserPreference::firstOrCreate(['user_id' => $coach->id],    ['locale' => 'en', 'theme' => 'system', 'timezone' => 'Asia/Riyadh', 'density' => 'comfortable']);
    }

    private function createSecuritySettings(User $admin, User $organizer, User $coach): void
    {
        SecuritySetting::firstOrCreate(['user_id' => $admin->id],    ['twofa_email' => true, 'twofa_app' => true,  'notify_on_login' => true]);
        SecuritySetting::firstOrCreate(['user_id' => $organizer->id],['twofa_email' => false,'twofa_app' => false, 'notify_on_login' => true]);
        SecuritySetting::firstOrCreate(['user_id' => $coach->id],    ['twofa_email' => false,'twofa_app' => false, 'notify_on_login' => false]);
    }

    private function createActivities(User $admin): void
    {
        $this->command->info('Creating activities...');
        $events = [
            ['type' => 'auth',        'event' => 'login',     'description' => 'تسجيل دخول إلى النظام'],
            ['type' => 'team',        'event' => 'created',   'description' => 'إضافة فريق جديد'],
            ['type' => 'match',       'event' => 'created',   'description' => 'إضافة مباراة جديدة'],
            ['type' => 'player',      'event' => 'created',   'description' => 'إضافة لاعب جديد'],
            ['type' => 'competition', 'event' => 'created',   'description' => 'إضافة بطولة جديدة'],
            ['type' => 'lineup',      'event' => 'updated',   'description' => 'تحديث تشكيلة الفريق'],
            ['type' => 'settings',    'event' => 'updated',   'description' => 'تحديث إعدادات النظام'],
        ];
        foreach ($events as $e) {
            Activity::create([
                'user_id' => $admin->id, 'type' => $e['type'], 'event' => $e['event'],
                'description' => $e['description'], 'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 Test Seeder',
            ]);
        }
    }
}
