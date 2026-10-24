<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Free tools hub. Every tool runs in the visitor's browser; the server only serves the page and the Gem texts.
 */
class ToolController extends Controller
{
    public function index()
    {
        return view('site.tools.index', ['tools' => config('tools.tools')]);
    }

    public function show(string $tool)
    {
        $config = config('tools.tools.'.$tool);
        abort_unless($config, 404);

        return view('site.tools.show', [
            'slug' => $tool,
            'tool' => $config,
            'tools' => config('tools.tools'),
            'instruction' => $this->gemFile($config['instruction']),
            'knowledgeUrl' => route('tools.knowledge', $tool),
        ]);
    }

    /** The knowledge file of a Gem, as a plain Markdown download to upload into the Gem. */
    public function knowledge(string $tool)
    {
        $config = config('tools.tools.'.$tool);
        abort_unless($config, 404);

        return response($this->gemFile($config['knowledge']), 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="arti-'.$tool.'-pengetahuan.md"',
        ]);
    }

    protected function gemFile(string $name): string
    {
        $path = resource_path('gems/'.$name);
        abort_unless(File::exists($path), 404);

        return trim(File::get($path))."\n";
    }
}
