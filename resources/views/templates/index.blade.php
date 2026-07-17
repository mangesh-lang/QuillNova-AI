@extends('layouts.app')
@section('title', 'AI Templates')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">AI Templates</h1>
        <p class="page-subtitle mb-0">Browse through our specialized AI copywriters and productivity generators.</p>
    </div>

    <!-- Search Form -->
    <form action="{{ route('templates.index') }}" method="GET" class="w-100 w-md-auto d-flex gap-2">
        <input type="text" name="search" class="form-control border-custom text-primary" style="background: var(--bg-secondary); border-radius: 10px; width: 280px;" placeholder="Search AI tools..." value="{{ $search }}">
        @if($selectedCategory)
            <input type="hidden" name="category" value="{{ $selectedCategory }}">
        @endif
        <button type="submit" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold" style="background-color: var(--accent-primary); border: none;"><i class="bi bi-search"></i></button>
    </form>
</div>

<!-- Category Badges Slider -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('templates.index') }}" class="btn btn-sm rounded-pill px-3 py-2 transition {{ !$selectedCategory ? 'btn-primary' : 'btn-outline-secondary border-custom text-primary' }}" style="{{ !$selectedCategory ? 'background-color: var(--accent-primary); border: none;' : '' }}">
        <i class="bi bi-grid-fill me-1.5"></i> All Categories
    </a>
    @foreach($categories as $cat)
        <a href="{{ route('templates.index', ['category' => $cat->slug]) }}" class="btn btn-sm rounded-pill px-3 py-2 transition {{ $selectedCategory === $cat->slug ? 'btn-primary' : 'btn-outline-secondary border-custom text-primary' }}" style="{{ $selectedCategory === $cat->slug ? 'background-color: var(--accent-primary); border: none;' : '' }}">
            <i class="bi {{ $cat->icon }} me-1.5"></i> {{ $cat->name }}
        </a>
    @endforeach
</div>

<!-- Templates Cards Grid -->
<div class="row g-4">
    @forelse($templatesList as $temp)
        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
            <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <!-- Icon / Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background-color: rgba(99, 102, 241, 0.1);">
                            <i class="bi {{ $temp->icon ?: 'bi-lightning' }} fs-4 text-primary"></i>
                        </div>
                        <span class="badge bg-secondary text-primary border border-custom px-2 py-1 rounded-3" style="font-size: 0.725rem;">
                            {{ $temp->category->name }}
                        </span>
                    </div>

                    <!-- Details -->
                    <h5 class="fw-bold mb-2 text-primary">{{ $temp->name }}</h5>
                    <p class="text-muted-custom mb-3" style="font-size: 0.85rem; line-height: 1.5; height: 4.5rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                        {{ $temp->description }}
                    </p>
                </div>

                <!-- Launch Button -->
                <div class="d-grid pt-2">
                    <a href="{{ route('templates.show', $temp->slug) }}" class="btn btn-outline-primary rounded-3 d-flex align-items-center justify-content-center gap-2 py-2 fw-semibold text-primary border-custom" style="border-color: var(--accent-primary);">
                        Use Tool <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted-custom">
            <i class="bi bi-search fs-1 d-block mb-3"></i>
            <h5 class="fw-bold">No AI tools matching search criteria found.</h5>
            <p class="mb-0">Try checking spelling or choosing a different category filter.</p>
        </div>
    @endforelse
</div>
@endsection
