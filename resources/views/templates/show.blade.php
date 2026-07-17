@extends('layouts.app')
@section('title', $template->name)

@section('content')
<div class="page-header d-flex align-items-center gap-3">
    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: rgba(99, 102, 241, 0.1);">
        <i class="bi {{ $template->icon ?: 'bi-lightning' }} fs-3 text-primary"></i>
    </div>
    <div>
        <h1 class="page-title">{{ $template->name }}</h1>
        <p class="page-subtitle mb-0">{{ $template->description }}</p>
    </div>
</div>

<div class="row g-4" x-data="{
    loading: false,
    generated: false,
    resultId: null,
    resultText: '',
    resultTitle: '',
    wordsCount: 0,
    progress: 0,
    error: '',
    
    startGeneration() {
        this.loading = true;
        this.generated = false;
        this.error = '';
        this.progress = 0;
        this.resultText = '';

        // Simulate progress bar increase
        let interval = setInterval(() => {
            if (this.progress < 90) {
                this.progress += Math.floor(Math.random() * 15) + 5;
            } else {
                clearInterval(interval);
            }
        }, 300);

        // Submit form via AJAX
        let formData = new FormData(document.getElementById('generation-form'));
        
        fetch('{{ route('templates.generate', $template->slug) }}', {
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
            if (res.status === 200) {
                this.resultId = res.body.id;
                this.resultTitle = res.body.title;
                this.resultText = res.body.result;
                this.wordsCount = res.body.word_count;
                this.generated = true;
                this.loading = false;
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: res.body.message || 'Generation Complete!',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                this.error = res.body.error || 'Something went wrong. Please try again.';
                this.loading = false;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Generation Failed',
                    text: this.error
                });
            }
        })
        .catch(err => {
            clearInterval(interval);
            this.loading = false;
            this.error = 'A network error occurred. Please check your connection.';
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: this.error
            });
        });
    },

    copyToClipboard() {
        navigator.clipboard.writeText(this.resultText);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Copied to clipboard!',
            showConfirmButton: false,
            timer: 2000
        });
    },

    shareContent() {
        if (navigator.share) {
            navigator.share({
                title: this.resultTitle,
                text: this.resultText
            }).catch(console.error);
        } else {
            this.copyToClipboard();
            Swal.fire({
                icon: 'info',
                title: 'Share System',
                text: 'Copied text to clipboard. You can paste and share it anywhere!'
            });
        }
    }
}">
    <!-- Form Box Column -->
    <div class="col-12 col-lg-5">
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-3">Generation Parameters</h5>
            <hr class="border-custom mb-4">

            <form id="generation-form" @submit.prevent="startGeneration()">
                @csrf

                <!-- Dynamic Form Fields Loop -->
                @foreach($template->fields as $field)
                    <div class="mb-3">
                        <label for="{{ $field['name'] }}" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">
                            {{ $field['label'] }} @if($field['required'] ?? false)<span class="text-danger">*</span>@endif
                        </label>
                        
                        @if($field['type'] === 'textarea')
                            <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" rows="4" placeholder="Enter context here..." @if($field['required'] ?? false) required @endif></textarea>
                        @elseif($field['type'] === 'select')
                            <select id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" @if($field['required'] ?? false) required @endif>
                                @foreach($field['options'] as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="{{ $field['name'] }}" type="text" name="{{ $field['name'] }}" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" placeholder="Type here..." @if($field['required'] ?? false) required @endif>
                        @endif
                    </div>
                @endforeach

                <!-- AI Provider Switcher -->
                <div class="mb-4">
                    <label for="provider" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">Select AI Engine</label>
                    <select id="provider" name="provider" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                        <option value="openai">OpenAI (GPT-4o-mini)</option>
                        <option value="gemini">Google Gemini (Gemini 2.5 Flash)</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-custom py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2" :disabled="loading">
                        <template x-if="loading">
                            <span><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...</span>
                        </template>
                        <template x-if="!loading">
                            <span><i class="bi bi-cpu"></i> Generate Content</span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Output Box Column -->
    <div class="col-12 col-lg-7">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between" style="min-height: 480px;">
            <!-- Output Header Actions -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">AI Generation Output</h5>
                
                <div class="d-flex gap-2" x-show="generated" style="display: none;">
                    <button class="btn btn-sm btn-outline-secondary border-custom text-primary" @click="copyToClipboard()" title="Copy to Clipboard"><i class="bi bi-clipboard"></i> Copy</button>
                    <button class="btn btn-sm btn-outline-secondary border-custom text-primary" @click="shareContent()" title="Share"><i class="bi bi-share"></i> Share</button>
                    <a :href="'/history/' + resultId + '/download/pdf'" class="btn btn-sm btn-outline-danger" title="Download PDF"><i class="bi bi-file-pdf"></i> PDF</a>
                    <a :href="'/history/' + resultId + '/download/txt'" class="btn btn-sm btn-outline-secondary border-custom text-primary" title="Download TXT"><i class="bi bi-filetype-txt"></i> TXT</a>
                </div>
            </div>

            <hr class="border-custom mb-3 mt-0">

            <!-- Core Output Display -->
            <div class="flex-grow-1 position-relative p-2" style="overflow-y: auto; max-height: 450px;">
                <!-- 1. Empty State -->
                <div class="text-center py-5 text-muted-custom" x-show="!loading && !generated && !error">
                    <i class="bi bi-magic fs-1 d-block mb-3 text-secondary opacity-50"></i>
                    <h6 class="fw-bold">Output Sandbox Ready</h6>
                    <p class="mb-0 small">Fill out the parameters on the left and hit generate to draft copywriting.</p>
                </div>

                <!-- 2. Loading State / Skeleton & Progress -->
                <div x-show="loading" style="display: none;">
                    <div class="progress rounded-pill mb-4" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" :style="'width: ' + progress + '%; background-color: var(--accent-primary);'" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex flex-column gap-2.5">
                        <div class="skeleton rounded" style="height: 24px; width: 60%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 100%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 95%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 85%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 90%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 40%;"></div>
                    </div>
                </div>

                <!-- 3. Error Display -->
                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 p-3" x-show="error" style="display: none;">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i> <span x-text="error"></span>
                </div>

                <!-- 4. Realized Generated Content Output -->
                <div class="output-textbox text-primary" x-show="generated" style="display: none; white-space: pre-wrap; font-size: 0.95rem; line-height: 1.7;" x-text="resultText"></div>
            </div>

            <!-- Footer Stats -->
            <div class="pt-3 border-top border-custom mt-3 d-flex justify-content-between align-items-center text-muted-custom" style="font-size: 0.8rem;" x-show="generated" style="display: none;">
                <span>Length: <strong class="text-primary" x-text="wordsCount"></strong> words</span>
                <span>Auto-saved in History</span>
            </div>
        </div>
    </div>
</div>
@endsection
