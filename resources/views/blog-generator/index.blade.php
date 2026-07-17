@extends('layouts.app')
@section('title', 'Advanced Blog Generator')

@section('content')
<div class="page-header d-flex align-items-center gap-3">
    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: rgba(99, 102, 241, 0.1);">
        <i class="bi bi-journal-richtext fs-3 text-primary"></i>
    </div>
    <div>
        <h1 class="page-title">Advanced Blog Generator</h1>
        <p class="page-subtitle mb-0">Craft long-form blogs in two steps: first build a logical outline, then compile the full post.</p>
    </div>
</div>

<div class="row g-4" x-data="{
    step: 1, // 1: Outline Builder, 2: Post Compiler
    loading: false,
    outlineText: '',
    postText: '',
    postTitle: '',
    resultId: null,
    wordsCount: 0,
    progress: 0,
    error: '',

    generateOutline() {
        this.loading = true;
        this.error = '';
        this.progress = 0;
        this.outlineText = '';

        let interval = setInterval(() => {
            if (this.progress < 90) this.progress += 10;
        }, 200);

        let formData = new FormData(document.getElementById('outline-form'));

        fetch('{{ route('blog.outline') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            }
        })
        .then(response => {
            clearInterval(interval);
            this.progress = 100;
            return response.json().then(data => ({ status: response.status, body: data }));
        })
        .then(res => {
            this.loading = false;
            if (res.status === 200) {
                this.outlineText = res.body.outline;
                this.step = 2; // Transition to Step 2
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Blog outline created!',
                    showConfirmButton: false,
                    timer: 2500
                });
            } else {
                this.error = res.body.error || 'Failed to generate outline.';
                Swal.fire({ icon: 'error', title: 'Outline Failed', text: this.error });
            }
        })
        .catch(err => {
            clearInterval(interval);
            this.loading = false;
            this.error = 'Network error occurred.';
            Swal.fire({ icon: 'error', title: 'Connection Error', text: this.error });
        });
    },

    generatePost() {
        this.loading = true;
        this.error = '';
        this.progress = 0;
        this.postText = '';

        let interval = setInterval(() => {
            if (this.progress < 95) this.progress += 5;
        }, 400);

        let formData = new FormData(document.getElementById('post-form'));
        // Append topic and tone from first form
        formData.append('topic', document.getElementById('topic').value);
        formData.append('tone', document.getElementById('tone').value);

        fetch('{{ route('blog.post') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            }
        })
        .then(response => {
            clearInterval(interval);
            this.progress = 100;
            return response.json().then(data => ({ status: response.status, body: data }));
        })
        .then(res => {
            this.loading = false;
            if (res.status === 200) {
                this.resultId = res.body.id;
                this.postTitle = res.body.title;
                this.postText = res.body.result;
                this.wordsCount = res.body.word_count;
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Full blog post generated successfully!',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                this.error = res.body.error || 'Failed to generate post.';
                Swal.fire({ icon: 'error', title: 'Generation Failed', text: this.error });
            }
        })
        .catch(err => {
            clearInterval(interval);
            this.loading = false;
            this.error = 'Network error occurred.';
            Swal.fire({ icon: 'error', title: 'Connection Error', text: this.error });
        });
    },

    copyPost() {
        navigator.clipboard.writeText(this.postText);
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Copied to clipboard!', showConfirmButton: false, timer: 2000 });
    }
}">
    <!-- Step 1 Layout Panel -->
    <div class="col-12 col-lg-5" x-show="step === 1">
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-3">Step 1: Build Blog Outline</h5>
            <hr class="border-custom mb-4">

            <form id="outline-form" @submit.prevent="generateOutline()">
                @csrf
                <div class="mb-3">
                    <label for="topic" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">What is your blog topic?</label>
                    <input id="topic" type="text" name="topic" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" placeholder="e.g. 10 Remote Work Productivity Tips" required>
                </div>

                <div class="mb-3">
                    <label for="keywords" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">Target Keywords (comma separated)</label>
                    <input id="keywords" type="text" name="keywords" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" placeholder="e.g. remote productivity, workspace setup">
                </div>

                <div class="mb-3">
                    <label for="tone" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">Tone of voice</label>
                    <select id="tone" name="tone" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                        <option value="Professional">Professional</option>
                        <option value="Informative">Informative</option>
                        <option value="Friendly">Friendly</option>
                        <option value="Casual">Casual</option>
                        <option value="Witty">Witty</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="provider" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">AI Model Engine</label>
                    <select id="provider" name="provider" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                        <option value="openai">OpenAI (GPT-4o-mini)</option>
                        <option value="gemini">Google Gemini (Gemini 2.5 Flash)</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-custom py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2" :disabled="loading">
                        <template x-if="loading">
                            <span><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Architecting Outline...</span>
                        </template>
                        <template x-if="!loading">
                            <span>Next: Build Outline <i class="bi bi-arrow-right"></i></span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Step 2 Edit & Compile Panel -->
    <div class="col-12 col-lg-5" x-show="step === 2" style="display: none;">
        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Step 2: Review & Write Post</h5>
                <button class="btn btn-sm btn-outline-secondary border-custom text-primary" @click="step = 1"><i class="bi bi-arrow-left"></i> Edit Topic</button>
            </div>
            <hr class="border-custom mb-4">

            <form id="post-form" @submit.prevent="generatePost()">
                @csrf
                <div class="mb-3">
                    <label for="outline" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">Refine Generated Outline</label>
                    <textarea id="outline" name="outline" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" rows="8" x-model="outlineText" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="language" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">Output Language</label>
                    <select id="language" name="language" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                        <option value="English">English</option>
                        <option value="Spanish">Spanish</option>
                        <option value="French">French</option>
                        <option value="German">German</option>
                        <option value="Hindi">Hindi</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="provider-post" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">AI Model Engine</label>
                    <select id="provider-post" name="provider" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                        <option value="openai">OpenAI (GPT-4o-mini)</option>
                        <option value="gemini">Google Gemini (Gemini 2.5 Flash)</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2" :disabled="loading" style="background-color: #10b981; border: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                        <template x-if="loading">
                            <span><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Compiling Full Blog...</span>
                        </template>
                        <template x-if="!loading">
                            <span>Generate Full Post <i class="bi bi-cpu"></i></span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Output Display Panel -->
    <div class="col-12 col-lg-7">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between" style="min-height: 480px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Generation Output Workspace</h5>
                
                <div class="d-flex gap-2" x-show="postText" style="display: none;">
                    <button class="btn btn-sm btn-outline-secondary border-custom text-primary" @click="copyPost()" title="Copy to Clipboard"><i class="bi bi-clipboard"></i> Copy</button>
                    <a :href="'/history/' + resultId + '/download/pdf'" class="btn btn-sm btn-outline-danger" title="Download PDF"><i class="bi bi-file-pdf"></i> PDF</a>
                    <a :href="'/history/' + resultId + '/download/txt'" class="btn btn-sm btn-outline-secondary border-custom text-primary" title="Download TXT"><i class="bi bi-filetype-txt"></i> TXT</a>
                </div>
            </div>

            <hr class="border-custom mb-3 mt-0">

            <div class="flex-grow-1 position-relative p-2" style="overflow-y: auto; max-height: 450px;">
                <!-- Empty State -->
                <div class="text-center py-5 text-muted-custom" x-show="!loading && !outlineText && !postText && !error">
                    <i class="bi bi-journal-plus fs-1 d-block mb-3 text-secondary opacity-50"></i>
                    <h6 class="fw-bold">Blog Generation Output</h6>
                    <p class="mb-0 small">Step 1 outline and Step 2 full blog articles will render in this sandbox.</p>
                </div>

                <!-- Outline Preview State -->
                <div x-show="!loading && outlineText && !postText" style="display: none;">
                    <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info rounded-4 mb-3 small">
                        <i class="bi bi-info-circle me-1.5"></i> Outline built successfully! Review the items on the left form, tweak headers, and press generate post.
                    </div>
                    <div class="text-primary" style="white-space: pre-wrap; font-size: 0.95rem;" x-text="outlineText"></div>
                </div>

                <!-- Loading State / Skeletons -->
                <div x-show="loading" style="display: none;">
                    <div class="progress rounded-pill mb-4" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" :style="'width: ' + progress + '%; background-color: var(--accent-primary);'" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex flex-column gap-2.5">
                        <div class="skeleton rounded" style="height: 24px; width: 60%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 100%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 95%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 85%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 40%;"></div>
                    </div>
                </div>

                <!-- Error Display -->
                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 p-3" x-show="error" style="display: none;">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i> <span x-text="error"></span>
                </div>

                <!-- Final Full Post Output -->
                <div class="output-textbox text-primary" x-show="postText" style="display: none; white-space: pre-wrap; font-size: 0.95rem; line-height: 1.7;" x-text="postText"></div>
            </div>

            <div class="pt-3 border-top border-custom mt-3 d-flex justify-content-between align-items-center text-muted-custom" style="font-size: 0.8rem;" x-show="postText" style="display: none;">
                <span>Length: <strong class="text-primary" x-text="wordsCount"></strong> words</span>
                <span>Auto-saved in History</span>
            </div>
        </div>
    </div>
</div>
@endsection
