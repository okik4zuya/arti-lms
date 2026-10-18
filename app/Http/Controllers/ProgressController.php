<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Progress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function store(Request $request, Course $course, string $slug): RedirectResponse|JsonResponse
    {
        Progress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
                'subchapter_slug' => $slug,
            ],
            [
                'completed_at' => now(),
            ]
        );

        // The page marks the subchapter as done on its own (resources/js/progress-instant.js) and only needs to know it was saved.
        if ($request->expectsJson()) {
            return response()->json(['completed' => true, 'slug' => $slug]);
        }

        return redirect()->route('content.show', ['course' => $course, 'slug' => $slug]);
    }
}
