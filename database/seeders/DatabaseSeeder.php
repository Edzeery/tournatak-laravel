<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PositionSeeder::class,
            FormationSeeder::class,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@tournatak.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
        ]);
        $admin->assignRole('admin');

        // Create demo organizer
        $organizer = User::create([
            'name' => 'Ahmed Organizer',
            'username' => 'ahmed_org',
            'email' => 'ahmed@tournatak.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'is_verified' => true,
        ]);
        $organizer->assignRole('organizer');

        // Create demo captain
        $captain = User::create([
            'name' => 'Mohamed Captain',
            'username' => 'moh_captain',
            'email' => 'mohamed@tournatak.com',
            'password' => Hash::make('password'),
            'role' => 'captain',
            'is_verified' => true,
        ]);
        $captain->assignRole('captain');

        // Create demo viewer
        $viewer = User::create([
            'name' => 'Viewer User',
            'username' => 'viewer1',
            'email' => 'viewer@tournatak.com',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'is_verified' => true,
        ]);
        $viewer->assignRole('viewer');

        // Run comprehensive test data seeder
        $this->call(TestDataSeeder::class);

        // Create sample notifications for admin
        $this->createSampleNotifications($admin);

        $this->command->info('Database seeded successfully!');
    }

    private function createSampleNotifications(User $admin): void
    {
        $notifications = [
            [
                'title' => 'مرحباً بك في لوحة التحكم',
                'message' => 'تم تسجيل دخولك بنجاح. يمكنك إدارة البطولات والفرق من هنا.',
                'icon' => 'bi-check-circle-fill text-success',
                'link' => '/admin/dashboard',
            ],
            [
                'title' => 'بطولة جديدة قيد المراجعة',
                'message' => 'يوجد طلب بطولة جديد بانتظار الموافقة عليه.',
                'icon' => 'bi-exclamation-triangle-fill text-warning',
                'link' => '/admin/competitions',
            ],
            [
                'title' => 'تم إنشاء مباراة جديدة',
                'message' => 'تم إضافة مباراة جديدة في البطولة المحلية.',
                'icon' => 'bi-calendar-event-fill text-info',
                'link' => '/admin/matches',
            ],
            [
                'title' => 'تنبيه أمني',
                'message' => 'تم تسجيل محاولة تسجيل دخول ببيانات غير صحيحة.',
                'icon' => 'bi-shield-exclamation text-danger',
                'link' => '/admin/security-log',
            ],
            [
                'title' => 'تحديث النظام',
                'message' => 'تم تحديث إعدادات النظام بنجاح. يرجى مراجعة الأذونات.',
                'icon' => 'bi-gear-fill text-secondary',
                'link' => '/admin/dashboard',
            ],
            [
                'title' => 'فريق جديد مسجل',
                'message' => 'تم تسجيل فريق جديد ويحتاج إلى مراجعة البيانات.',
                'icon' => 'bi-people-fill text-primary',
                'link' => '/admin/teams',
            ],
            [
                'title' => 'إحصائيات المباراة',
                'message' => 'تم تحديث إحصائيات المباراة الأخيرة. يمكنك مراجعتها الآن.',
                'icon' => 'bi-bar-chart-fill text-info',
                'link' => '/admin/matches',
            ],
            [
                'title' => 'تذكير',
                'message' => 'يوجد مباراة قادمة خلال 3 أيام. تأكد من إعداد التشكيلة.',
                'icon' => 'bi-clock-fill text-warning',
                'link' => '/admin/matches',
            ],
        ];

        foreach ($notifications as $i => $data) {
            UserNotification::create(array_merge($data, [
                'user_id' => $admin->id,
                'is_read' => $i > 3,
                'created_at' => now()->subHours(rand(1, 72)),
            ]));
        }
    }
}
