<?php

return [

    /*
    | Where flat-file articles live: <path>/<slug>/index.md (+ optional images/).
    | Kept outside resources/content, which belongs to the LMS courses.
    */
    'path' => env('ARTICLES_PATH', resource_path('articles')),

];
