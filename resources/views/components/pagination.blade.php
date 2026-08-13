@props(['paginator'])

@if($paginator && $paginator->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $paginator->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
@endif
