@props(['title' => 'No Data Found', 'message' => 'There are no records matching your criteria.', 'icon' => 'bi-inbox'])

<div class="text-center py-5 my-3">
    <i class="bi {{ $icon }} empty-state-icon text-muted d-block mb-3"></i>
    <h5 class="fw-semibold text-dark mb-1">{{ $title }}</h5>
    <p class="text-muted small mb-0">{{ $message }}</p>
</div>
