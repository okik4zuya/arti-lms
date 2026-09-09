<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Yaml\Yaml;

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
                $manifestPath = resource_path("content/{$course->slug}/manifest.yaml");
                $subchapters = [];

                if (is_file($manifestPath)) {
                    $manifest = Yaml::parseFile($manifestPath);
                    $subchapters = $this->flattenSubchapters($manifest);
                }

                $course->firstSubchapterSlug = $subchapters[0]['slug'] ?? null;

                $completedCount = $request->user()
                    ->progress()
                    ->where('course_id', $course->id)
                    ->whereNotNull('completed_at')
                    ->whereIn('subchapter_slug', array_column($subchapters, 'slug'))
                    ->count();

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
