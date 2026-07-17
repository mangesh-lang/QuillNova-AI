@extends('layouts.app')
@section('title', 'AI Professional Email Writer')

@section('content')
<div class="page-header d-flex align-items-center gap-3">
    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background-color: rgba(99, 102, 241, 0.1);">
        <i class="bi bi-envelope-at-fill fs-3 text-primary"></i>
    </div>
    <div>
        <h1 class="page-title">AI Professional Email Writer</h1>
        <p class="page-subtitle mb-0">Compose professional, clear, and high-impact emails in any language for any context.</p>
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

        let interval = setInterval(() => {
            if (this.progress < 90) {
                this.progress += Math.floor(Math.random() * 12) + 6;
            } else {
                clearInterval(interval);
            }
        }, 200);

        let formData = new FormData(document.getElementById('email-form'));
        
        fetch('{{ route('email.generate') }}', {
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
                this.resultTitle = res.body.title;
                this.resultText = res.body.result;
                this.wordsCount = res.body.word_count;
                this.generated = true;
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Email written successfully!',
                    showConfirmButton: false,
                    timer: 2500
                });
            } else {
                this.error = res.body.error || 'Failed to compose email.';
                Swal.fire({ icon: 'error', title: 'Generation Failed', text: this.error });
            }
        })
        .catch(err => {
            clearInterval(interval);
            this.loading = false;
            this.error = 'Network connection issue.';
            Swal.fire({ icon: 'error', title: 'Connection Error', text: this.error });
        });
    },

    copyEmail() {
        navigator.clipboard.writeText(this.resultText);
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Email copied to clipboard!', showConfirmButton: false, timer: 2000 });
    }
}">
    <!-- Inputs Column -->
    <div class="col-12 col-lg-5">
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-3">Email Parameters</h5>
            <hr class="border-custom mb-4">

            <form id="email-form" @submit.prevent="startGeneration()">
                @csrf
                <div class="mb-3">
                    <label for="recipient" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">Recipient / Who is this to?</label>
                    <input id="recipient" type="text" name="recipient" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" placeholder="e.g. My Manager, Hiring Director, Client" required>
                </div>

                <div class="mb-3">
                    <label for="purpose" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">What is the purpose of this email?</label>
                    <input id="purpose" type="text" name="purpose" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" placeholder="e.g. Requesting a salary review, following up on proposal" required>
                </div>

                <div class="mb-3">
                    <label for="context" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">Context & Key Points to cover</label>
                    <textarea id="context" name="context" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" rows="4" placeholder="e.g. Mention that I completed the Q2 goals early, list that we generated 20% more revenue than last quarter, request meeting next Tuesday." required></textarea>
                </div>

                <div class="mb-3">
                    <label for="tone" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">Email Tone</label>
                    <select id="tone" name="tone" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                        <option value="Professional & Formal">Professional & Formal</option>
                        <option value="Warm & Friendly">Warm & Friendly</option>
                        <option value="Direct & Polite">Direct & Polite</option>
                        <option value="Urgent">Urgent</option>
                        <option value="Informative">Informative</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="language" class="form-label fw-semibold text-primary" style="font-size: 0.9rem;">Language</label>
                    <select id="language" name="language" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                        <option value="English">English</option>
                        <option value="Spanish">Spanish</option>
                        <option value="French">French</option>
                        <option value="German">German</option>
                        <option value="Hindi">Hindi</option>
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
                            <span><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Drafting Email...</span>
                        </template>
                        <template x-if="!loading">
                            <span>Write Email <i class="bi bi-send-fill"></i></span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Outputs Column -->
    <div class="col-12 col-lg-7">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between" style="min-height: 480px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Composed Email Output</h5>
                
                <div class="d-flex gap-2" x-show="generated" style="display: none;">
                    <button class="btn btn-sm btn-outline-secondary border-custom text-primary" @click="copyEmail()" title="Copy to Clipboard"><i class="bi bi-clipboard"></i> Copy</button>
                    <a :href="'/history/' + resultId + '/download/pdf'" class="btn btn-sm btn-outline-danger" title="Download PDF"><i class="bi bi-file-pdf"></i> PDF</a>
                    <a :href="'/history/' + resultId + '/download/txt'" class="btn btn-sm btn-outline-secondary border-custom text-primary" title="Download TXT"><i class="bi bi-filetype-txt"></i> TXT</a>
                </div>
            </div>

            <hr class="border-custom mb-3 mt-0">

            <div class="flex-grow-1 position-relative p-2" style="overflow-y: auto; max-height: 450px;">
                <!-- Empty State -->
                <div class="text-center py-5 text-muted-custom" x-show="!loading && !generated && !error">
                    <i class="bi bi-envelope-open fs-1 d-block mb-3 text-secondary opacity-50"></i>
                    <h6 class="fw-bold">Email Composer sandbox</h6>
                    <p class="mb-0 small">Email copies with subject lines will populate here after details are compiled.</p>
                </div>

                <!-- Loading Skeletons -->
                <div x-show="loading" style="display: none;">
                    <div class="progress rounded-pill mb-4" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" :style="'width: ' + progress + '%; background-color: var(--accent-primary);'" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex flex-column gap-2.5">
                        <div class="skeleton rounded" style="height: 24px; width: 60%; margin-bottom: 20px;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 100%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 95%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 85%;"></div>
                        <div class="skeleton rounded" style="height: 16px; width: 30%;"></div>
                    </div>
                </div>

                <!-- Error Display -->
                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 p-3" x-show="error" style="display: none;">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i> <span x-text="error"></span>
                </div>

                <!-- Output Content -->
                <div class="output-textbox text-primary" x-show="generated" style="display: none; white-space: pre-wrap; font-size: 0.95rem; line-height: 1.7;" x-text="resultText"></div>
            </div>

            <div class="pt-3 border-top border-custom mt-3 d-flex justify-content-between align-items-center text-muted-custom" style="font-size: 0.8rem;" x-show="generated" style="display: none;">
                <span>Length: <strong class="text-primary" x-text="wordsCount"></strong> words</span>
                <span>Auto-saved in History</span>
            </div>
        </div>
    </div>
</div>
@endsection
