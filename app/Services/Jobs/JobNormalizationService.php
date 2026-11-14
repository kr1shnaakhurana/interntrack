<?php

namespace App\Services\Jobs;

use Carbon\Carbon;
use Exception;

class JobNormalizationService
{
    /**
     * Sanitize untrusted external HTML/text description.
     */
    public function sanitizeDescription(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        // Remove script and style tags completely along with their inner content
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        $content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content);
        $content = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $content);

        // Strip dangerous HTML tags but allow safe formatting
        $allowedTags = '<p><br><hr><ul><ol><li><strong><b><em><i><u><h3><h4><h5><h6><blockquote><a>';
        $cleaned = strip_tags($content, $allowedTags);

        // Remove inline javascript event attributes (onload, onclick, onmouseover, etc.)
        $cleaned = preg_replace('/\s*on\w+="[^"]*"/i', '', $cleaned);
        $cleaned = preg_replace('/\s*on\w+=\'[^\']*\'/i', '', $cleaned);

        return trim($cleaned);
    }

    /**
     * Normalize raw job type into an InternTrack standard type.
     */
    public function normalizeJobType(?string $rawType, ?string $title = null, ?string $description = null): string
    {
        $type = strtolower(trim($rawType ?? ''));
        $titleLower = strtolower(trim($title ?? ''));
        $descLower = strtolower(trim($description ?? ''));

        // Check title clues first
        if (str_contains($titleLower, 'intern') || str_contains($titleLower, 'trainee') || str_contains($titleLower, 'apprentice')) {
            return 'internship';
        }

        if (str_contains($titleLower, 'research fellow') || str_contains($titleLower, 'postdoc')) {
            return 'research';
        }

        if (str_contains($titleLower, 'fellowship') || str_contains($titleLower, 'fellow')) {
            return 'fellowship';
        }

        // Direct mapping
        switch ($type) {
            case 'internship':
            case 'intern':
            case 'trainee':
            case 'apprenticeship':
                return 'internship';

            case 'full-time':
            case 'full_time':
            case 'fulltime':
            case 'permanent':
            case 'regular':
                return 'full-time';

            case 'part-time':
            case 'part_time':
            case 'parttime':
                return 'part-time';

            case 'research':
                return 'research';

            case 'fellowship':
                return 'fellowship';

            case 'government':
            case 'govt':
            case 'public_sector':
            case 'sarkari':
                return 'government';

            default:
                if (str_contains($titleLower, 'government') || str_contains($descLower, 'sarkari') || str_contains($titleLower, 'recruitment')) {
                    return 'government';
                }
                return 'full-time';
        }
    }

    /**
     * Normalize remote flag.
     */
    public function normalizeRemote(mixed $remoteIndicator, ?string $location = null, ?string $title = null): bool
    {
        if (is_bool($remoteIndicator)) {
            return $remoteIndicator;
        }

        $remoteStr = strtolower(trim((string) $remoteIndicator));
        if (in_array($remoteStr, ['1', 'true', 'yes', 'remote', 'telecommute', 'wfh'])) {
            return true;
        }

        $locLower = strtolower(trim($location ?? ''));
        $titleLower = strtolower(trim($title ?? ''));

        if (str_contains($locLower, 'remote') || str_contains($locLower, 'anywhere') || str_contains($locLower, 'work from home')) {
            return true;
        }

        if (str_contains($titleLower, 'remote') || str_contains($titleLower, 'wfh')) {
            return true;
        }

        return false;
    }

    /**
     * Normalize location string.
     */
    public function normalizeLocation(?string $location, bool $isRemote = false): string
    {
        $clean = trim($location ?? '');
        if (empty($clean)) {
            return $isRemote ? 'Remote' : 'Location Not Specified';
        }

        // Remove redundant multiple spaces/commas
        $clean = preg_replace('/\s*,\s*/', ', ', $clean);
        return preg_replace('/\s+/', ' ', $clean);
    }

    /**
     * Format salary or stipend into readable compensation string.
     */
    public function normalizeSalary(mixed $min = null, mixed $max = null, ?string $currency = 'INR', ?string $raw = null, ?string $period = 'yr'): ?string
    {
        if (! empty($raw)) {
            return trim($raw);
        }

        $minVal = is_numeric($min) ? (float) $min : null;
        $maxVal = is_numeric($max) ? (float) $max : null;

        if ($minVal === null && $maxVal === null) {
            return null;
        }

        $symbol = match (strtoupper($currency ?? 'INR')) {
            'INR' => '₹',
            'USD' => '$',
            'GBP' => '£',
            'EUR' => '€',
            default => ($currency ? $currency . ' ' : ''),
        };

        $periodSuffix = match (strtolower(trim($period ?? 'yr'))) {
            'monthly', 'month', 'mo' => ' / mo',
            'hourly', 'hour', 'hr' => ' / hr',
            'weekly', 'week', 'wk' => ' / wk',
            default => ' / yr',
        };

        if ($minVal !== null && $maxVal !== null) {
            if ($minVal === $maxVal) {
                return $symbol . number_format($minVal) . $periodSuffix;
            }
            return $symbol . number_format($minVal) . ' - ' . $symbol . number_format($maxVal) . $periodSuffix;
        }

        if ($minVal !== null) {
            return 'From ' . $symbol . number_format($minVal) . $periodSuffix;
        }

        return 'Up to ' . $symbol . number_format($maxVal) . $periodSuffix;
    }

    /**
     * Normalize skills into comma-separated string.
     */
    public function normalizeSkills(mixed $skills): ?string
    {
        if (is_array($skills)) {
            $cleaned = array_filter(array_map('trim', $skills));
            return ! empty($cleaned) ? implode(', ', array_unique($cleaned)) : null;
        }

        if (is_string($skills)) {
            $trimmed = trim($skills);
            return ! empty($trimmed) ? $trimmed : null;
        }

        return null;
    }

    /**
     * Validate external URL.
     */
    public function validateUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $trimmed = trim($url);
        // Only allow HTTP/HTTPS schemes
        if (! preg_match('/^https?:\/\//i', $trimmed)) {
            return null;
        }

        if (filter_var($trimmed, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return $trimmed;
    }

    /**
     * Safely parse datetime string into Carbon instance or ISO string.
     */
    public function normalizeDate(mixed $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date)->toDateTimeString();
        } catch (Exception) {
            return null;
        }
    }
}

