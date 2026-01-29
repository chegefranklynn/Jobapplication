<?php

declare(strict_types=1);

namespace App\Domain\Models;

use DateTimeImmutable;

/**
 * @phpstan-immutable
 */
class Applicant
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly DateTimeImmutable $createdAt,
        public readonly int $experienceYears = 0,
        /** @var array<string> */
        public readonly array $skills = [],
        /** @var array{min: int, max: int} */
        public readonly array $salaryExpectations = ['min' => 0, 'max' => 0]
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) { 
            throw new \DomainException("Invalid email: {$this->email}");
        }

        if ($this->experienceYears < 0) {
            throw new \DomainException('Experience cannot be negative');
        }
    }

    public static function create(
        string $name,
        string $email,
        int $experienceYears,
        array $skills
    ): self {
        return new self(
            null,
            $name,
            $email,
            new DateTimeImmutable(),
            $experienceYears,
            $skills
        );
    }
} 