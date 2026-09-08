<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Progress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function store(Request $request, Course $course, string $slug): RedirectResponse
    {
        Progress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
                'chapter_slug' => $slug,
            ],
            [
                'completed_at' => now(),
            ]
        );

        return redirect()->route('content.show', ['course' => $course, 'slug' => $slug]);
    }
}
