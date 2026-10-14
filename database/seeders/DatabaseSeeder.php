<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Development accounts. Idempotent: running it again resets these two users, nothing else.
     * Never seeds in production, because the passwords are public.
     */
    public const ADMIN_EMAIL = 'admin@example.com';

    public const LEARNER_EMAIL = 'test@example.com';

    public const PASSWORD = 'password';

    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('Refusing to seed known passwords in production.');

            return;
        }

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'learner']);

        $admin = $this->user('Admin', self::ADMIN_EMAIL);
        $admin->syncRoles('admin');

        $learner = $this->user('Test User', self::LEARNER_EMAIL);
        $learner->syncRoles('learner');

        $course = Course::firstOrCreate(
            ['slug' => 'example-course'],
            ['title' => 'Example Course', 'published_at' => now()],
        );

        CourseAccess::firstOrCreate(
            ['user_id' => $learner->id, 'course_id' => $course->id],
            ['granted_at' => now()],
        );
    }

    protected function user(string $name, string $email): User
    {
        // Plain assignment: the model's `hashed` cast hashes the password.
        $user = User::firstOrNew(['email' => $email]);
        $user->name = $name;
        $user->password = self::PASSWORD;
        $user->email_verified_at ??= now();
        $user->save();

        return $user;
    }
}
