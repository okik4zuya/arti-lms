<?php

return [

    /*
    | Cache store for rendered course pages and manifests (see App\Support\CourseContent).
    | null = the application's default store; set CONTENT_CACHE_STORE=redis to keep them in Redis.
    | A cache that is down never breaks a page: it is rendered fresh and a warning is logged.
    */
    'cache_store' => env('CONTENT_CACHE_STORE'),

    /* Days a rendered page stays cached. The key carries the file's modification time, so an edit shows at once. */
    'cache_days' => (int) env('CONTENT_CACHE_DAYS', 7),

];
