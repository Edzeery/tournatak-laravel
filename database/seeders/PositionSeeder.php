<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $footballPositions = [
            ['name' => 'حارس المرمى', 'name_en' => 'Goalkeeper', 'category' => 'goalkeeper', 'abbreviation' => 'GK', 'sort_order' => 1],
            ['name' => 'ظهير أيمن', 'name_en' => 'Right Back', 'category' => 'defender', 'abbreviation' => 'RB', 'sort_order' => 2],
            ['name' => 'مدافع مركزي', 'name_en' => 'Center Back', 'category' => 'defender', 'abbreviation' => 'CB', 'sort_order' => 3],
            ['name' => 'ظهير أيسر', 'name_en' => 'Left Back', 'category' => 'defender', 'abbreviation' => 'LB', 'sort_order' => 4],
            ['name' => 'وسط دفاعي', 'name_en' => 'Defensive Midfielder', 'category' => 'midfielder', 'abbreviation' => 'CDM', 'sort_order' => 5],
            ['name' => 'وسط مركزي', 'name_en' => 'Central Midfielder', 'category' => 'midfielder', 'abbreviation' => 'CM', 'sort_order' => 6],
            ['name' => 'وسط هجومي', 'name_en' => 'Attacking Midfielder', 'category' => 'midfielder', 'abbreviation' => 'CAM', 'sort_order' => 7],
            ['name' => 'جناح أيمن', 'name_en' => 'Right Winger', 'category' => 'forward', 'abbreviation' => 'RW', 'sort_order' => 8],
            ['name' => 'جناح أيسر', 'name_en' => 'Left Winger', 'category' => 'forward', 'abbreviation' => 'LW', 'sort_order' => 9],
            ['name' => 'مهاجم مركزي', 'name_en' => 'Center Forward', 'category' => 'forward', 'abbreviation' => 'CF', 'sort_order' => 10],
            ['name' => 'مهاجم صريح', 'name_en' => 'Striker', 'category' => 'forward', 'abbreviation' => 'ST', 'sort_order' => 11],
        ];

        $futsalPositions = [
            ['name' => 'حارس صالة', 'name_en' => 'Futsal Goalkeeper', 'category' => 'goalkeeper', 'abbreviation' => 'FGK', 'sort_order' => 1],
            ['name' => 'بيفوت', 'name_en' => 'Pivot', 'category' => 'forward', 'abbreviation' => 'PIV', 'sort_order' => 2],
            ['name' => 'فيكس', 'name_en' => 'Fixer', 'category' => 'midfielder', 'abbreviation' => 'FIX', 'sort_order' => 3],
            ['name' => 'جناح صالة', 'name_en' => 'Wing', 'category' => 'midfielder', 'abbreviation' => 'WNG', 'sort_order' => 4],
            ['name' => 'دفاع صالة', 'name_en' => 'Defender', 'category' => 'defender', 'abbreviation' => 'FDE', 'sort_order' => 5],
        ];

        foreach ($footballPositions as $position) {
            Position::create(array_merge($position, ['sport_type' => 'football']));
        }

        foreach ($futsalPositions as $position) {
            Position::create(array_merge($position, ['sport_type' => 'futsal']));
        }
    }
}
