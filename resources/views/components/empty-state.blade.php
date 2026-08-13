@props(['title' => 'No Data Found', 'message' => 'There are no records matching your criteria.'])

<div class="text-center py-5 my-3 bg-white rounded-3 border">
    <i class="bi bi-inbox text-muted display-4 mb-3 d-block"></i>
    <h5 class="fw-bold text-dark">{{ $title }}</h5>
    <p class="text-muted small mb-0">{{ $message }}</p>
</div>
