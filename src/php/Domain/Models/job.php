<?php

declare(strict_types=1);

namespace App\Domain\Models;

use App\Domain\Models\ValueObjects\ExperienceRange;

class Job
{
    /**
     * @param array<string> $requiredSkills
     * @param array<string> $preferredSkills
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $company,
        public readonly array $requiredSkills,
        public readonly array $preferredSkills,
        public readonly ExperienceRange $experienceRange,
        public readonly bool $isRemote,
        public readonly \DateTimeImmutable $postedAt
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->requiredSkills)) {
            throw new \DomainException('Job must have at least one required skill');
        }
    }

    public function matchesLocation(bool $applicantPrefersRemote): bool
    {
        return $this->isRemote === $applicantPrefersRemote;
    }
}