<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Player;
use App\Models\User;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $team1 = Team::firstOrCreate(['name' => 'الهلال'], ['points' => 20]);
        $team2 = Team::firstOrCreate(['name' => 'الأهلي'], ['points' => 15]);

        $users = [
            ['name' => 'محمد صلاح', 'username' => 'salah', 'email' => 'salah@test.com', 'team_id' => 1, 'number' => 10, 'position' => 'جناح أيمن', 'position_id' => 9],
            ['name' => 'كريم بنزيما', 'username' => 'benzema', 'email' => 'benzema@test.com', 'team_id' => 1, 'number' => 9, 'position' => 'مهاجم', 'position_id' => 11],
            ['name' => 'مانويل نوير', 'username' => 'neuer', 'email' => 'neuer@test.com', 'team_id' => 1, 'number' => 1, 'position' => 'حارس المرمى', 'position_id' => 1],
            ['name' => 'أحمد رمضان', 'username' => 'ramadan', 'email' => 'ramadan@test.com', 'team_id' => 2, 'number' => 7, 'position' => 'جناح أيسر', 'position_id' => 10],
            ['name' => 'علي الحسن', 'username' => 'ali', 'email' => 'ali@test.com', 'team_id' => 2, 'number' => 5, 'position' => 'مدافع مركزي', 'position_id' => 3],
            ['name' => 'ياسر القحطاني', 'username' => 'yasser', 'email' => 'yasser@test.com', 'team_id' => 1, 'number' => 8, 'position' => 'وسط مركزي', 'position_id' => 6],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'username' => $u['username'],
                    'password' => bcrypt('password'),
                    'role' => 'player',
                ]
            );
            Player::firstOrCreate(
                ['user_id' => $user->id, 'team_id' => $u['team_id']],
                [
                    'number' => $u['number'],
                    'position' => $u['position'],
                    'position_id' => $u['position_id'],
                    'sport_type' => 'football',
                ]
            );
        }

        $this->command->info('Test data seeded successfully!');
    }
}
