<?php

namespace App\Modules\Templates\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminTemplateController extends Controller
{
    /**
     * Display templates and categories administration lists.
     */
    public function index()
    {
        $templates = Template::with('category')->orderBy('created_at', 'desc')->paginate(15, ['*'], 'templates_page');
        $categories = Category::withCount('templates')->orderBy('name', 'asc')->paginate(10, ['*'], 'categories_page');
        
        return view('admin.templates.index', compact('templates', 'categories'));
    }

    /**
     * Store a new category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'icon' => 'required|string',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon ?: 'bi-hash',
            'is_active' => true,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_category_create',
            'details' => 'Admin created template category: ' . $category->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Category created successfully!');
    }

    /**
     * Update an existing category.
     */
    public function updateCategory(Request $request, int $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'required|string',
            'is_active' => 'required|boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon,
            'is_active' => $request->is_active,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_category_update',
            'details' => 'Admin updated template category #' . $category->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Category updated successfully!');
    }

    /**
     * Delete an existing category.
     */
    public function destroyCategory(Request $request, int $id)
    {
        $category = Category::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_category_delete',
            'details' => 'Admin deleted template category: ' . $category->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $category->delete();

        return back()->with('success', 'Category and its related templates deleted successfully.');
    }

    /**
     * Store a new prompt template.
     */
    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:templates',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'prompt_template' => 'required|string',
            'icon' => 'required|string',
            // fields JSON structure: array of objects containing name, type, label, options, required
            'fields_json' => 'required|json',
        ]);

        $fields = json_decode($request->fields_json, true);

        $template = Template::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'category_id' => $request->category_id,
            'prompt_template' => $request->prompt_template,
            'fields' => $fields,
            'icon' => $request->icon ?: 'bi-lightning',
            'is_active' => true,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_template_create',
            'details' => 'Admin created prompt template: ' . $template->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Prompt template created successfully!');
    }

    /**
     * Update an existing prompt template.
     */
    public function updateTemplate(Request $request, int $id)
    {
        $template = Template::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:templates,name,' . $template->id,
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'prompt_template' => 'required|string',
            'icon' => 'required|string',
            'fields_json' => 'required|json',
            'is_active' => 'required|boolean',
        ]);

        $fields = json_decode($request->fields_json, true);

        $template->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'category_id' => $request->category_id,
            'prompt_template' => $request->prompt_template,
            'fields' => $fields,
            'icon' => $request->icon,
            'is_active' => $request->is_active,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_template_update',
            'details' => 'Admin updated template #' . $template->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Prompt template updated successfully!');
    }

    /**
     * Delete an existing prompt template.
     */
    public function destroyTemplate(Request $request, int $id)
    {
        $template = Template::findOrFail($id);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_template_delete',
            'details' => 'Admin deleted template: ' . $template->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $template->delete();

        return back()->with('success', 'Template deleted successfully.');
    }
}
