<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormationSeeder extends Seeder
{
    public function run(): void
    {
        $formations = [
            [
                'team_id' => null,
                'name' => '4-4-2',
                'sport_type' => 'football',
                'formation_code' => '4-4-2',
                'positions_data' => json_encode([
                    ['position' => 'GK', 'x' => 50, 'y' => 92],
                    ['position' => 'RB', 'x' => 75, 'y' => 75],
                    ['position' => 'CB', 'x' => 58, 'y' => 78],
                    ['position' => 'CB', 'x' => 42, 'y' => 78],
                    ['position' => 'LB', 'x' => 25, 'y' => 75],
                    ['position' => 'RM', 'x' => 78, 'y' => 55],
                    ['position' => 'CM', 'x' => 58, 'y' => 55],
                    ['position' => 'CM', 'x' => 42, 'y' => 55],
                    ['position' => 'LM', 'x' => 22, 'y' => 55],
                    ['position' => 'ST', 'x' => 58, 'y' => 30],
                    ['position' => 'ST', 'x' => 42, 'y' => 30],
                ]),
                'description' => 'التشكيلة الكلاسيكية 4-4-2 - توازن دفاعي وهجومي مع رباعي في الوسط وثنائي هجومي',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'team_id' => null,
                'name' => '4-3-3',
                'sport_type' => 'football',
                'formation_code' => '4-3-3',
                'positions_data' => json_encode([
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
                ]),
                'description' => 'التشكيلة الهجومية 4-3-3 - ثلاثي هجومي سريع مع ثلاثي وسط داعم',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'team_id' => null,
                'name' => '4-2-3-1',
                'sport_type' => 'football',
                'formation_code' => '4-2-3-1',
                'positions_data' => json_encode([
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
                ]),
                'description' => 'التشكيلة المرنة 4-2-3-1 - اثنان دفاعيان في الوسط مع ثلاثة لاعبين هجوميين خلف المهاجم',
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'team_id' => null,
                'name' => '3-5-2',
                'sport_type' => 'football',
                'formation_code' => '3-5-2',
                'positions_data' => json_encode([
                    ['position' => 'GK', 'x' => 50, 'y' => 92],
                    ['position' => 'CB', 'x' => 60, 'y' => 78],
                    ['position' => 'CB', 'x' => 50, 'y' => 80],
                    ['position' => 'CB', 'x' => 40, 'y' => 78],
                    ['position' => 'RWB', 'x' => 82, 'y' => 60],
                    ['position' => 'CM', 'x' => 60, 'y' => 55],
                    ['position' => 'CM', 'x' => 50, 'y' => 55],
                    ['position' => 'CM', 'x' => 40, 'y' => 55],
                    ['position' => 'LWB', 'x' => 18, 'y' => 60],
                    ['position' => 'ST', 'x' => 58, 'y' => 28],
                    ['position' => 'ST', 'x' => 42, 'y' => 28],
                ]),
                'description' => 'التشكيلة الدفاعية 3-5-2 - ثلاثة مدافعين مع خمسة لاعبي وسط وثنائي هجومي',
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($formations as $formation) {
            DB::table('formations')->insert($formation);
        }
    }
}
