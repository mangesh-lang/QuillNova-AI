@extends('layouts.app')
@section('title', 'Content History')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">Content History</h1>
        <p class="page-subtitle mb-0">Search, filter, view, and download all generated AI copywriters.</p>
    </div>
</div>

<!-- Filters Bar -->
<div class="glass-card p-3 mb-4">
    <form action="{{ route('history.index') }}" method="GET" class="row g-3 align-items-center">
        <!-- Search -->
        <div class="col-12 col-md-4">
            <div class="input-group">
                <span class="input-group-text border-custom text-muted-custom" style="background: var(--bg-secondary);"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" placeholder="Search document text..." value="{{ $search }}">
            </div>
        </div>

        <!-- Tool Type Filter -->
        <div class="col-12 col-md-3">
            <select name="tool_type" class="form-select text-primary border-custom" style="background: var(--bg-secondary);">
                <option value="">All Tools</option>
                @foreach($toolTypes as $type)
                    <option value="{{ $type }}" {{ $toolType === $type ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Favorites Filter -->
        <div class="col-12 col-md-3">
            <select name="favorite" class="form-select text-primary border-custom" style="background: var(--bg-secondary);">
                <option value="">All Records</option>
                <option value="true" {{ $isFavorite === true ? 'selected' : '' }}>Favorites Only</option>
                <option value="false" {{ $isFavorite === false ? 'selected' : '' }}>Normal Records Only</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-3 py-2" style="background-color: var(--accent-primary); border: none;">Filter</button>
            <a href="{{ route('history.index') }}" class="btn btn-outline-secondary border-custom text-primary px-3 rounded-3 py-2" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- History Log List Table -->
<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0 text-primary border-custom" style="background: transparent;" id="history-table">
            <thead>
                <tr class="text-muted-custom" style="border-bottom: 1.5px solid var(--border-color); font-size: 0.85rem;">
                    <th scope="col">Favorite</th>
                    <th scope="col">Document Title</th>
                    <th scope="col">Module / Type</th>
                    <th scope="col">Words count</th>
                    <th scope="col">Generated At</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $item)
                    <tr style="border-bottom: 1px solid var(--border-color); font-size: 0.9rem;" id="row-{{ $item->id }}">
                        <td>
                            <!-- Favorite Toggle Button -->
                            <button class="btn btn-link p-0 border-0 bg-transparent" 
                                    onclick="toggleFavorite({{ $item->id }}, this)">
                                <i class="bi {{ $item->is_favorite ? 'bi-star-fill text-warning' : 'bi-star text-muted-custom' }} fs-5"></i>
                            </button>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $item->title }}</div>
                            <small class="text-muted-custom text-truncate d-block" style="max-width: 320px; font-size: 0.775rem;">
                                {{ Str::limit(strip_tags($item->result_text), 80) }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-secondary text-primary border border-custom px-2 py-1 rounded-3" style="font-size: 0.75rem;">
                                {{ $item->template ? $item->template->name : ucfirst(str_replace('_', ' ', $item->tool_type)) }}
                            </span>
                        </td>
                        <td>{{ number_format($item->word_count) }}</td>
                        <td class="text-muted-custom" style="font-size: 0.85rem;">{{ $item->created_at->format('M d, Y h:i A') }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <!-- Export PDF -->
                                <a href="{{ route('history.download.pdf', $item->id) }}" class="btn btn-sm btn-outline-danger" title="Download PDF"><i class="bi bi-file-pdf"></i></a>
                                
                                <!-- Export TXT -->
                                <a href="{{ route('history.download.txt', $item->id) }}" class="btn btn-sm btn-outline-secondary text-primary border-custom" title="Download TXT"><i class="bi bi-filetype-txt"></i></a>
                                
                                <!-- View Content Modal Toggle -->
                                <button class="btn btn-sm btn-outline-secondary text-primary border-custom" title="View Document" 
                                    onclick="viewDocument(`{{ addslashes($item->title) }}`, `{{ addslashes($item->result_text) }}`)">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <!-- Delete -->
                                <form action="{{ route('history.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this record permanently?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete record"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted-custom">
                            <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                            <h6 class="fw-bold">No generated documents match search criteria.</h6>
                            <p class="mb-0 small text-muted-custom">Generate some text and it will log automatically here.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4 d-flex justify-content-end">
        {{ $history->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- View Document Modal (Bootstrap 5) -->
<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card border-custom text-primary" style="background: var(--bg-secondary);">
            <div class="modal-header border-custom">
                <h5 class="modal-title fw-bold" id="documentModalLabel">View Document</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 480px; overflow-y: auto;">
                <div id="modal-content-area" class="lh-lg" style="white-space: pre-wrap; font-size: 0.95rem;"></div>
            </div>
            <div class="modal-footer border-custom">
                <button type="button" class="btn btn-secondary rounded-3 border-custom text-primary bg-transparent" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary rounded-3" style="background-color: var(--accent-primary); border: none;" onclick="copyModalContent()">Copy Content</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle Favorite state via AJAX
    function toggleFavorite(id, btn) {
        fetch('/history/' + id + '/favorite', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const icon = btn.querySelector('i');
                if (data.is_favorite) {
                    icon.className = 'bi bi-star-fill text-warning fs-5';
                } else {
                    icon.className = 'bi bi-star text-muted-custom fs-5';
                }
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }

    let activeModalText = '';

    // View document content in modal
    function viewDocument(title, text) {
        document.getElementById('documentModalLabel').textContent = title;
        document.getElementById('modal-content-area').textContent = text;
        activeModalText = text;

        const myModal = new bootstrap.Modal(document.getElementById('documentModal'));
        myModal.show();
    }

    function copyModalContent() {
        navigator.clipboard.writeText(activeModalText);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Copied!',
            showConfirmButton: false,
            timer: 1500
        });
    }
</script>
@endsection
