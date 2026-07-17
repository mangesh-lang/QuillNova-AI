<?php

namespace App\Modules\Templates\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\Category;
use App\Models\GeneratedContent;
use App\Models\ActivityLog;
use App\Services\AIEngineService;
use App\Services\DailyLimitService;
use App\Services\SettingService;
use App\Modules\Templates\Repositories\TemplateRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TemplateController extends Controller
{
    protected TemplateRepository $templates;
    protected AIEngineService $aiEngine;
    protected DailyLimitService $limitService;
    protected SettingService $settings;

    public function __construct(
        TemplateRepository $templates,
        AIEngineService $aiEngine,
        DailyLimitService $limitService,
        SettingService $settings
    ) {
        $this->templates = $templates;
        $this->aiEngine = $aiEngine;
        $this->limitService = $limitService;
        $this->settings = $settings;
    }

    /**
     * Browse templates index.
     */
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->get();
        $selectedCategory = $request->query('category');
        $search = $request->query('search');

        if ($search) {
            $templatesList = $this->templates->searchTemplates($search);
        } elseif ($selectedCategory) {
            $templatesList = $this->templates->getByCategory($selectedCategory);
        } else {
            $templatesList = Template::where('is_active', true)->with('category')->get();
        }

        return view('templates.index', compact('categories', 'templatesList', 'selectedCategory', 'search'));
    }

    /**
     * Show form for a specific template.
     */
    public function show(string $slug)
    {
        $template = Template::where('slug', $slug)->where('is_active', true)->with('category')->firstOrFail();
        return view('templates.show', compact('template'));
    }

    /**
     * Generate content via AJAX request.
     */
    public function generate(Request $request, string $slug)
    {
        $user = Auth::user();
        $template = Template::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // 1. Check daily usage limits
        if ($this->limitService->hasExceededLimits($user)) {
            return response()->json([
                'success' => false,
                'error' => "You have reached today's free limit. Please come back tomorrow.",
            ], 429);
        }

        // 2. Validate input fields dynamically based on template field specifications
        $rules = [];
        foreach ($template->fields as $field) {
            if ($field['required'] ?? false) {
                $rules[$field['name']] = 'required|string';
            } else {
                $rules[$field['name']] = 'nullable|string';
            }
        }
        $request->validate($rules);

        // 3. Compile prompt template with user inputs
        $prompt = $template->prompt_template;
        foreach ($template->fields as $field) {
            $fieldName = $field['name'];
            $userValue = $request->input($fieldName, '');
            $prompt = str_replace('{' . $fieldName . '}', $userValue, $prompt);
        }

        // 4. Determine AI engine provider (optionally overridden by user if allowed, else default)
        $provider = $request->input('provider', $this->settings->get('default_ai_provider', 'openai'));

        // 5. Query AI engine
        $aiResult = $this->aiEngine->generate($prompt, $provider);

        if (!$aiResult['success']) {
            return response()->json([
                'success' => false,
                'error' => $aiResult['error'],
            ], 500);
        }

        $generatedText = $aiResult['text'];
        $wordsCount = $aiResult['word_count'];

        // 6. Record daily limit usage
        $this->limitService->recordUsage($user, $wordsCount);

        // 7. Save to generated history
        $titleField = $template->fields[0]['name'] ?? 'topic';
        $titleInput = $request->input($titleField, 'AI Content');
        $title = $template->name . ' - ' . Str::limit($titleInput, 30);

        $savedContent = GeneratedContent::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => $title,
            'prompt_text' => $prompt,
            'result_text' => $generatedText,
            'word_count' => $wordsCount,
            'tool_type' => 'template',
            'is_favorite' => false,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'generate_content',
            'details' => 'Generated content using template: ' . $template->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $savedContent->id,
            'title' => $savedContent->title,
            'result' => $generatedText,
            'word_count' => $wordsCount,
            'message' => 'Content generated and saved successfully!',
        ]);
    }
}
