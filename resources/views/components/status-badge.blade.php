@props(['status' => null, 'type' => null])

@php
    $val = strtolower($status ?? $type ?? 'default');
    $badgeClass = 'bg-secondary';
    $label = ucfirst($val);

    switch ($val) {
        case 'applied':
            $badgeClass = 'bg-info text-dark';
            break;
        case 'shortlisted':
            $badgeClass = 'bg-warning text-dark';
            break;
        case 'interview':
            $badgeClass = 'bg-primary';
            break;
        case 'selected':
        case 'approved':
        case 'cleared':
            $badgeClass = 'bg-success';
            break;
        case 'rejected':
            $badgeClass = 'bg-danger';
            break;
        case 'pending':
            $badgeClass = 'bg-warning text-dark';
            break;
        case 'internship':
            $badgeClass = 'bg-indigo text-white';
            break;
        case 'full-time':
            $badgeClass = 'bg-dark';
            break;
        case 'research':
        case 'fellowship':
            $badgeClass = 'bg-purple text-white';
            break;
        case 'government':
            $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
            break;
    }
@endphp

<span class="badge {{ $badgeClass }} px-2 py-1">{{ $label }}</span>
