@props(['title' => 'No Records Found', 'message' => 'There are no records matching your criteria.', 'icon' => 'bi-inbox'])

<div class="empty-state-box">
    <div class="empty-state-icon">
        <i class="bi {{ $icon }}"></i>
    </div>
    <div class="empty-state-title">{{ $title }}</div>
    <p class="empty-state-desc">{{ $message }}</p>
    {{ $slot ?? '' }}
</div>
