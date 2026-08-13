<?php

namespace App\Services\Interviews;

use App\Models\Application;
use App\Models\Interview;
use App\Models\User;
use App\Services\Applications\ApplicationService;
use Illuminate\Support\Facades\DB;
use Exception;

class InterviewService
{
    public function __construct(
        protected ApplicationService $applicationService
    ) {}

    /**
     * Schedule a new interview round for an application.
     */
    public function scheduleRound(Application $application, array $data, User $creatorUser): Interview
    {
        return DB::transaction(function () use ($application, $data, $creatorUser) {
            $nextRoundNumber = ($application->interviews()->max('round_number') ?? 0) + 1;

            $interview = Interview::create([
                'application_id' => $application->id,
                'round_number' => $data['round_number'] ?? $nextRoundNumber,
                'round_name' => $data['round_name'] ?? "Round {$nextRoundNumber}",
                'interview_type' => $data['interview_type'] ?? 'online',
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'interviewer' => $data['interviewer'] ?? null,
                'location_meeting_link' => $data['location_meeting_link'] ?? null,
                'notes' => $data['notes'] ?? null,
                'result' => 'pending',
            ]);

            // If application was not already in 'interview' status, update it
            if ($application->status !== 'interview') {
                $this->applicationService->updateStatus(
                    $application,
                    'interview',
                    $creatorUser,
                    "Interview round {$interview->round_number} scheduled."
                );
            }

            return $interview;
        });
    }

    /**
     * Update an interview round result. If this is marked as the final round, update application outcome.
     */
    public function updateResult(Interview $interview, string $result, User $updaterUser, bool $isFinalRound = false, ?string $notes = null): Interview
    {
        return DB::transaction(function () use ($interview, $result, $updaterUser, $isFinalRound, $notes) {
            $interview->update([
                'result' => $result,
                'notes' => $notes ?? $interview->notes,
            ]);

            $application = $interview->application;

            // Business Rule: If this is the final round result, determine final application status
            if ($isFinalRound || $result === 'rejected') {
                if ($result === 'cleared') {
                    $this->applicationService->updateStatus(
                        $application,
                        'selected',
                        $updaterUser,
                        "Application selected after clearing final interview round."
                    );
                } elseif ($result === 'rejected') {
                    $this->applicationService->updateStatus(
                        $application,
                        'rejected',
                        $updaterUser,
                        "Application rejected following interview round outcome."
                    );
                }
            }

            return $interview;
        });
    }
}
