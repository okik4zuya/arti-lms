<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Yaml\Yaml;

class DashboardController extends Controller
{
    protected function flattenChapters(array $manifest): array
    {
        return collect($manifest['sections'] ?? [])
            ->flatMap(fn ($section) => $section['chapters'] ?? [])
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
                $manifestPath = resource_path("content/{$course->slug}/manifest.yaml");
                $chapters = [];

                if (is_file($manifestPath)) {
                    $manifest = Yaml::parseFile($manifestPath);
                    $chapters = $this->flattenChapters($manifest);
                }

                $course->firstChapterSlug = $chapters[0]['slug'] ?? null;

                $completedCount = $request->user()
                    ->progress()
                    ->where('course_id', $course->id)
                    ->whereNotNull('completed_at')
                    ->whereIn('chapter_slug', array_column($chapters, 'slug'))
                    ->count();

                $course->totalChapters = count($chapters);
                $course->completedChapters = $completedCount;
                $course->progressPercent = $course->totalChapters > 0
                    ? (int) round($completedCount / $course->totalChapters * 100)
                    : 0;

                return $course;
            });

        return view('dashboard', [
            'courses' => $courses,
        ]);
    }
}
