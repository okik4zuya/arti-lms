<?php

namespace App\Http\Middleware;

use App\Models\Course;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCourseAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Course $course */
        $course = $request->route('course');

        $hasAccess = $request->user()
            ->courseAccess()
            ->where('course_id', $course->id)
            ->exists();

        abort_unless($hasAccess, 403);

        return $next($request);
    }
}
