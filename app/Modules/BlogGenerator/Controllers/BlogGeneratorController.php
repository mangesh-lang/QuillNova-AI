<?php

namespace App\Modules\BlogGenerator\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\GeneratedContent;
use App\Models\ActivityLog;
use App\Services\AIEngineService;
use App\Services\DailyLimitService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogGeneratorController extends Controller
{
    protected AIEngineService $aiEngine;
    protected DailyLimitService $limitService;
    protected SettingService $settings;

    public function __construct(AIEngineService $aiEngine, DailyLimitService $limitService, SettingService $settings)
    {
        $this->aiEngine = $aiEngine;
        $this->limitService = $limitService;
        $this->settings = $settings;
    }

    /**
     * Show the dedicated Blog Generator form.
     */
    public function index()
    {
        return view('blog-generator.index');
    }

    /**
     * Step 1: Generate Blog Outlines/Ideas.
     */
    public function generateOutline(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'keywords' => 'nullable|string',
            'tone' => 'required|string',
        ]);

        $user = Auth::user();
        if ($this->limitService->hasExceededLimits($user)) {
            return response()->json([
                'success' => false,
                'error' => "You have reached today's free limit. Please come back tomorrow.",
            ], 429);
        }

        $prompt = "Create a detailed blog post outline for the topic: \"{$request->topic}\".\n";
        if ($request->keywords) {
            $prompt .= "Include the following key concepts/keywords in the sections: \"{$request->keywords}\".\n";
        }
        $prompt .= "Write in a {$request->tone} tone. Keep the outline logical with an Introduction, 3-5 subheadings, and a Conclusion.";

        $provider = $request->input('provider', $this->settings->get('default_ai_provider', 'openai'));
        $result = $this->aiEngine->generate($prompt, $provider);

        if (!$result['success']) {
            return response()->json(['success' => false, 'error' => $result['error']], 500);
        }

        // Record a single limit request (estimating outline size as ~150 words)
        $this->limitService->recordUsage($user, $result['word_count']);

        return response()->json([
            'success' => true,
            'outline' => $result['text'],
        ]);
    }

    /**
     * Step 2: Generate complete article based on custom outline.
     */
    public function generatePost(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'outline' => 'required|string',
            'tone' => 'required|string',
            'language' => 'required|string',
        ]);

        $user = Auth::user();
        if ($this->limitService->hasExceededLimits($user)) {
            return response()->json([
                'success' => false,
                'error' => "You have reached today's free limit. Please come back tomorrow.",
            ], 429);
        }

        $prompt = "Write a complete, structured blog post in {$request->language} based on the topic: \"{$request->topic}\".\n\n";
        $prompt .= "Follow this outline closely:\n{$request->outline}\n\n";
        $prompt .= "Style Guidelines: Write in a {$request->tone} tone, use HTML headers (<h2>, <h3>) for structure, make the paragraphs engaging, and add a conclusion. Do not output markdown code blocks, just direct HTML structure.";

        $provider = $request->input('provider', $this->settings->get('default_ai_provider', 'openai'));
        $result = $this->aiEngine->generate($prompt, $provider);

        if (!$result['success']) {
            return response()->json(['success' => false, 'error' => $result['error']], 500);
        }

        // Record limit counts
        $this->limitService->recordUsage($user, $result['word_count']);

        // Find or reference the standard Blog Generator template if it exists
        $template = Template::where('slug', 'blog-generator')->first();

        // Save generated blog content in history
        $savedContent = GeneratedContent::create([
            'user_id' => $user->id,
            'template_id' => $template ? $template->id : null,
            'title' => 'Blog Generator - ' . Str::limit($request->topic, 30),
            'prompt_text' => $prompt,
            'result_text' => $result['text'],
            'word_count' => $result['word_count'],
            'tool_type' => 'blog_generator',
            'is_favorite' => false,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'blog_generator',
            'details' => 'Generated full blog post.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $savedContent->id,
            'title' => $savedContent->title,
            'result' => $result['text'],
            'word_count' => $result['word_count'],
        ]);
    }
}
