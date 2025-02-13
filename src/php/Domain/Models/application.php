<?php

declare(strict_types=1);

namespace App\Domain\Models;

/**
 * @phpstan-type ExperienceRange array{min: int<0, max>, max: int<0, max>}
 */
class Applicant
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly int $experienceYears,
        /** @var array<string> */
        public readonly array $skills,
        /** @var ExperienceRange */
        public readonly array $experienceRange,
        public readonly \DateTimeImmutable $createdAt = new \DateTimeImmutable()
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->experienceYears < 0) {
            throw new \InvalidArgumentException('Experience cannot be negative');
        }

        if ($this->experienceRange['min'] > $this->experienceRange['max']) {
            throw new \LogicException('Invalid experience range');
        }
    }

    public function getSkillSet(): \Ds\Set
    {
        return new \Ds\Set($this->skills);
    }
}