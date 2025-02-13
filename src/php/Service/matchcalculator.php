<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Models\Applicant;
use App\Domain\Models\Job;

class MatchCalculator
{
    public function __construct(
        private float $skillWeight = 0.6,
        private float $experienceWeight = 0.4
    ) {}

    /**
     * @return array{
     *     score: float<0,100>,
     *     skillsMatch: float<0,100>,
     *     experienceMatch: float<0,100>
     * }
     */
    public function calculateMatch(Applicant $applicant, Job $job): array
    {
        $skillsScore = $this->calculateSkillsMatch(
            $applicant->skills,
            $job->requiredSkills,
            $job->preferredSkills
        );

        $experienceScore = $this->calculateExperienceMatch(
            $applicant->experienceYears,
            $job->experienceRange
        );

        $totalScore = ($skillsScore * $this->skillWeight) 
                    + ($experienceScore * $this->experienceWeight);

        return [
            'score' => round($totalScore, 2),
            'skillsMatch' => $skillsScore,
            'experienceMatch' => $experienceScore
        ];
    }

    public static function createWithWeights(
        float $skillWeight,
        float $experienceWeight
    ): self {
        return new self($skillWeight, $experienceWeight);
    }
}