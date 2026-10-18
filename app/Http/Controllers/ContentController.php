<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Support\CourseContent;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContentController extends Controller
{
    public function show(Request $request, CourseContent $content, Course $course, string $slug)
    {
        $page = $content->page($course, $slug);

        abort_if($page === null, 404);

        $manifest = $content->manifest($course->slug);

        $completedSlugs = $request->user()
            ->progress()
            ->where('course_id', $course->id)
            ->whereNotNull('completed_at')
            ->pluck('subchapter_slug');

        $flat = collect($manifest['sections'] ?? [])
            ->flatMap(fn (array $section) => $section['chapters'] ?? [])
            ->flatMap(fn (array $chapter) => $chapter['subchapters'] ?? [])
            ->values();
        $position = $flat->search(fn (array $subchapter) => ($subchapter['slug'] ?? null) === $slug);

        return view('content.show', [
            'previous' => $position === false ? null : $content->neighbour($course->slug, $flat->all(), $position, -1),
            'next' => $position === false ? null : $content->neighbour($course->slug, $flat->all(), $position, 1),
            'course' => $course,
            'slug' => $slug,
            'title' => $page['title'],
            'html' => $page['html'],
            'manifest' => $manifest,
            'completedSlugs' => $completedSlugs,
            'isCompleted' => $completedSlugs->contains($slug),
        ]);
    }

    public function image(Course $course, string $slug, string $file): BinaryFileResponse
    {
        $path = resource_path("content/{$course->slug}/{$slug}/images/{$file}");

        abort_unless(is_file($path) && ! str_contains($file, '..'), 404);

        return response()->file($path);
    }
}
