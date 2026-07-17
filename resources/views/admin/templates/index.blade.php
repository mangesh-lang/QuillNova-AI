@extends('layouts.app')
@section('title', 'Manage Templates & Categories')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">Templates & Categories</h1>
        <p class="page-subtitle mb-0">Create, edit, and manage categories and dynamic prompt templates.</p>
    </div>
</div>

<!-- Tabs Bar -->
<ul class="nav nav-pills mb-4 gap-2" id="admin-tabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active px-4 py-2.5 fw-semibold rounded-3 transition" id="templates-tab" data-bs-toggle="tab" data-bs-target="#templates-pane" type="button" role="tab" aria-controls="templates-pane" aria-selected="true"><i class="bi bi-lightning-charge-fill me-1.5"></i> AI Prompt Templates</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link px-4 py-2.5 fw-semibold rounded-3 transition border border-custom text-primary" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-pane" type="button" role="tab" aria-controls="categories-pane" aria-selected="false" style="background: transparent;"><i class="bi bi-grid-fill me-1.5"></i> Categories</button>
    </li>
</ul>

<div class="tab-content" id="admin-tabs-content">
    <!-- AI PROMPT TEMPLATES PANE -->
    <div class="tab-pane fade show active" id="templates-pane" role="tabpanel" aria-labelledby="templates-tab" tabindex="0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-primary">All AI Prompt Templates</h5>
            <button class="btn btn-primary d-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold" style="background-color: var(--accent-primary); border: none;" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                <i class="bi bi-plus-circle-fill"></i> Add Template
            </button>
        </div>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0 text-primary border-custom" style="background: transparent;">
                    <thead>
                        <tr class="text-muted-custom" style="border-bottom: 1.5px solid var(--border-color); font-size: 0.85rem;">
                            <th scope="col">Template Details</th>
                            <th scope="col">Category</th>
                            <th scope="col">Prompt Placeholder</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $temp)
                            <tr style="border-bottom: 1px solid var(--border-color); font-size: 0.9rem;">
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="bg-secondary text-primary rounded-3 d-flex align-items-center justify-content-center border border-custom" style="width: 38px; height: 38px;">
                                            <i class="bi {{ $temp->icon ?: 'bi-lightning' }} fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-primary">{{ $temp->name }}</div>
                                            <small class="text-muted-custom">{{ Str::limit($temp->description, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $temp->category->name }}</td>
                                <td class="small text-muted-custom text-truncate" style="max-width: 240px;" title="{{ $temp->prompt_template }}">
                                    {{ $temp->prompt_template }}
                                </td>
                                <td>
                                    @if($temp->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-3">Active</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-3">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <!-- Edit Button -->
                                        <button class="btn btn-sm btn-outline-secondary text-primary border-custom" data-bs-toggle="modal" data-bs-target="#editTemplateModal-{{ $temp->id }}" title="Edit Template"><i class="bi bi-pencil"></i></button>

                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.templates.destroy', $temp->id) }}" method="POST" onsubmit="return confirm('Delete this prompt template?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Template"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Template Modal -->
                            <div class="modal fade" id="editTemplateModal-{{ $temp->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content glass-card border-custom text-primary" style="background: var(--bg-secondary);">
                                        <form action="{{ route('admin.templates.update', $temp->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-custom">
                                                <h5 class="modal-title fw-bold">Edit Prompt Template</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body d-flex flex-column gap-3">
                                                <div class="row g-3">
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label fw-semibold text-primary">Template Name</label>
                                                        <input type="text" name="name" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" value="{{ $temp->name }}" required>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label fw-semibold text-primary">Bootstrap Icon Class</label>
                                                        <input type="text" name="icon" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" value="{{ $temp->icon }}" placeholder="bi-lightning" required>
                                                    </div>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label fw-semibold text-primary">Category</label>
                                                        <select name="category_id" class="form-select text-primary border-custom" style="background: var(--bg-secondary);">
                                                            @foreach($categories as $cat)
                                                                <option value="{{ $cat->id }}" {{ $temp->category_id === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label fw-semibold text-primary">Is Active?</label>
                                                        <select name="is_active" class="form-select text-primary border-custom" style="background: var(--bg-secondary);">
                                                            <option value="1" {{ $temp->is_active ? 'selected' : '' }}>Yes</option>
                                                            <option value="0" {{ !$temp->is_active ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="form-label fw-semibold text-primary">Description</label>
                                                    <input type="text" name="description" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" value="{{ $temp->description }}" required>
                                                </div>
                                                <div>
                                                    <label class="form-label fw-semibold text-primary">Prompt Template (Wrap input names in brackets e.g. {topic})</label>
                                                    <textarea name="prompt_template" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" rows="4" required>{{ $temp->prompt_template }}</textarea>
                                                </div>
                                                <div>
                                                    <label class="form-label fw-semibold text-primary">Fields Config JSON</label>
                                                    <textarea name="fields_json" class="form-control text-primary border-custom" style="background: var(--bg-secondary); font-family: monospace; font-size: 0.85rem;" rows="6" required>{{ json_encode($temp->fields, JSON_PRETTY_PRINT) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-custom">
                                                <button type="button" class="btn btn-secondary border-custom text-primary bg-transparent" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary" style="background-color: var(--accent-primary); border: none;">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted-custom">No templates available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                {{ $templates->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- CATEGORIES PANE -->
    <div class="tab-pane fade" id="categories-pane" role="tabpanel" aria-labelledby="categories-tab" tabindex="0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-primary">AI Template Categories</h5>
            <button class="btn btn-primary d-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold" style="background-color: var(--accent-primary); border: none;" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-circle-fill"></i> Add Category
            </button>
        </div>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0 text-primary border-custom" style="background: transparent;">
                    <thead>
                        <tr class="text-muted-custom" style="border-bottom: 1.5px solid var(--border-color); font-size: 0.85rem;">
                            <th scope="col">Icon</th>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Templates Count</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                            <tr style="border-bottom: 1px solid var(--border-color); font-size: 0.9rem;">
                                <td>
                                    <div class="bg-secondary text-primary rounded-3 d-flex align-items-center justify-content-center border border-custom" style="width: 36px; height: 36px;">
                                        <i class="bi {{ $cat->icon }} fs-5"></i>
                                    </div>
                                </td>
                                <td class="fw-bold">{{ $cat->name }}</td>
                                <td class="text-muted-custom small">{{ $cat->description }}</td>
                                <td>{{ $cat->templates_count }}</td>
                                <td>
                                    @if($cat->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-3">Active</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-3">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <!-- Edit Category Toggle -->
                                        <button class="btn btn-sm btn-outline-secondary text-primary border-custom" data-bs-toggle="modal" data-bs-target="#editCategoryModal-{{ $cat->id }}"><i class="bi bi-pencil"></i></button>

                                        <!-- Delete Category Form -->
                                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete this category? Related templates will also be deleted.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Category Modal -->
                            <div class="modal fade" id="editCategoryModal-{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content glass-card border-custom text-primary" style="background: var(--bg-secondary);">
                                        <form action="{{ route('admin.categories.update', $cat->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-custom">
                                                <h5 class="modal-title fw-bold">Edit Category</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body d-flex flex-column gap-3">
                                                <div>
                                                    <label class="form-label fw-semibold text-primary">Category Name</label>
                                                    <input type="text" name="name" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" value="{{ $cat->name }}" required>
                                                </div>
                                                <div>
                                                    <label class="form-label fw-semibold text-primary">Bootstrap Icon Class</label>
                                                    <input type="text" name="icon" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" value="{{ $cat->icon }}" placeholder="bi-hash" required>
                                                </div>
                                                <div>
                                                    <label class="form-label fw-semibold text-primary">Is Active?</label>
                                                    <select name="is_active" class="form-select text-primary border-custom" style="background: var(--bg-secondary);">
                                                        <option value="1" {{ $cat->is_active ? 'selected' : '' }}>Yes</option>
                                                        <option value="0" {{ !$cat->is_active ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="form-label fw-semibold text-primary">Description</label>
                                                    <textarea name="description" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" rows="3">{{ $cat->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-custom">
                                                <button type="button" class="btn btn-secondary border-custom text-primary bg-transparent" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary" style="background-color: var(--accent-primary); border: none;">Save Details</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                {{ $categories->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-custom text-primary" style="background: var(--bg-secondary);">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-custom">
                    <h5 class="modal-title fw-bold">Create Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div>
                        <label class="form-label fw-semibold text-primary">Category Name</label>
                        <input type="text" name="name" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" placeholder="e.g. Graphic Copywriting" required>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-primary">Bootstrap Icon Class</label>
                        <input type="text" name="icon" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" placeholder="bi-hash" required>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-primary">Description</label>
                        <textarea name="description" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" placeholder="Short summary..." rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-custom">
                    <button type="button" class="btn btn-secondary border-custom text-primary bg-transparent" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background-color: var(--accent-primary); border: none;">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Template Modal -->
<div class="modal fade" id="createTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card border-custom text-primary" style="background: var(--bg-secondary);">
            <form action="{{ route('admin.templates.store') }}" method="POST">
                @csrf
                <div class="modal-header border-custom">
                    <h5 class="modal-title fw-bold">Create AI Prompt Template</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-primary">Template Name</label>
                            <input type="text" name="name" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" placeholder="e.g. Email Composer" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-primary">Bootstrap Icon Class</label>
                            <input type="text" name="icon" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" placeholder="bi-lightning" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-primary">Category</label>
                            <select name="category_id" class="form-select text-primary border-custom" style="background: var(--bg-secondary);">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-primary">Description</label>
                        <input type="text" name="description" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" placeholder="e.g. Write professional corporate emails in seconds." required>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-primary">Prompt Template (Use brackets to denote placeholders e.g. {topic})</label>
                        <textarea name="prompt_template" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" placeholder="Write email to {recipient} with context {context}." rows="4" required></textarea>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-primary">Fields Config JSON</label>
                        <textarea name="fields_json" class="form-control text-primary border-custom" style="background: var(--bg-secondary); font-family: monospace; font-size: 0.85rem;" rows="6" placeholder='[
  {"name": "recipient", "type": "text", "label": "Recipient", "required": true},
  {"name": "context", "type": "textarea", "label": "Context details", "required": false}
]' required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-custom">
                    <button type="button" class="btn btn-secondary border-custom text-primary bg-transparent" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background-color: var(--accent-primary); border: none;">Create Template</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
