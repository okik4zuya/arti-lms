<?php

namespace App\Actions\Learners;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/** Creates a learner account (role `learner`) and optionally grants access to courses. */
class CreateLearner
{
    /** @param  iterable<Course>  $courses */
    public function handle(string $name, string $email, string $password, iterable $courses = []): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
        $user->assignRole('learner');

        foreach ($courses as $course) {
            $user->courseAccess()->create(['course_id' => $course->id, 'granted_at' => now()]);
        }

        return $user;
    }
}
