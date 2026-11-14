@props(['status' => null, 'type' => null])

@php
    $val = strtolower($status ?? $type ?? 'default');
    $badgeClass = 'badge-subtle-neutral';
    $label = ucfirst(str_replace('_', ' ', $val));

    switch ($val) {
        case 'applied':
            $badgeClass = 'badge-subtle-info';
            break;
        case 'shortlisted':
            $badgeClass = 'badge-subtle-warning';
            break;
        case 'interview':
            $badgeClass = 'badge-subtle-primary';
            break;
        case 'selected':
        case 'approved':
        case 'cleared':
        case 'active':
            $badgeClass = 'badge-subtle-success';
            break;
        case 'rejected':
        case 'suspended':
        case 'inactive':
            $badgeClass = 'badge-subtle-danger';
            break;
        case 'pending':
            $badgeClass = 'badge-subtle-warning';
            break;
        case 'internship':
            $badgeClass = 'badge-subtle-info';
            break;
        case 'full-time':
            $badgeClass = 'badge-subtle-neutral';
            break;
        case 'part-time':
            $badgeClass = 'badge-subtle-neutral';
            break;
        case 'research':
        case 'fellowship':
            $badgeClass = 'badge-subtle-primary';
            break;
        case 'government':
            $badgeClass = 'badge-subtle-warning';
            break;
        default:
            $badgeClass = 'badge-subtle-neutral';
            break;
    }
@endphp

<span class="badge {{ $badgeClass }}">{{ $label }}</span>