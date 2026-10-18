<?php

namespace App\Http\Controllers;

use App\Support\CourseContent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected function flattenSubchapters(array $manifest): array
    {
        return collect($manifest['sections'] ?? [])
            ->flatMap(fn ($section) => $section['chapters'] ?? [])
            ->flatMap(fn ($chapter) => $chapter['subchapters'] ?? [])
            ->all();
    }

    public function index(Request $request)
    {
        $courses = $request->user()
            ->courseAccess()
            ->with('course')
            ->get()
            ->pluck('course')
            ->map(function ($course) use ($request) {
                $subchapters = $this->flattenSubchapters(app(CourseContent::class)->manifest($course->slug, false));

                $course->firstSubchapterSlug = $subchapters[0]['slug'] ?? null;

                $completedSlugs = $request->user()
                    ->progress()
                    ->where('course_id', $course->id)
                    ->whereNotNull('completed_at')
                    ->whereIn('subchapter_slug', array_column($subchapters, 'slug'))
                    ->pluck('subchapter_slug');
                $completedCount = $completedSlugs->count();

                // "Lanjutkan belajar": the first subchapter, in course order, that is not marked complete yet.
                $next = collect($subchapters)->first(fn ($subchapter) => ! $completedSlugs->contains($subchapter['slug']));
                $course->resumeSlug = $next['slug'] ?? $course->firstSubchapterSlug;
                $course->resumeTitle = $next['title'] ?? null;
                $course->resumeState = match (true) {
                    $subchapters === [] => 'empty',
                    $next === null => 'done',
                    $completedCount === 0 => 'start',
                    default => 'continue',
                };

                $course->totalSubchapters = count($subchapters);
                $course->completedSubchapters = $completedCount;
                $course->progressPercent = $course->totalSubchapters > 0
                    ? (int) round($completedCount / $course->totalSubchapters * 100)
                    : 0;

                return $course;
            });

        return view('dashboard', [
            'courses' => $courses,
        ]);
    }
}
