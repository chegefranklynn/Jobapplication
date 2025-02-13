<?php

namespace App\Tests\Unit\Domain\Models;

use App\Domain\Models\Job;
use App\Domain\Models\ExperienceRange;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{
    public function test_duplicate_skills_are_filtered(): void
    {
        $job = new Job(
            id: 4,
            title: 'Backend Engineer',
            company: 'Startup',
            requiredSkills: ['PHP', 'PHP', 'MySQL'],
            preferredSkills: ['AWS', 'aws'],
            experienceRange: new ExperienceRange(2, 5),
            isRemote: false,
            postedAt: new \DateTimeImmutable()
        );
        
        $this->assertEqualsCanonicalizing(['PHP', 'MySQL'], $job->requiredSkills);
        $this->assertEqualsCanonicalizing(['AWS', 'aws'], $job->preferredSkills);
    }

    public function test_future_post_date_throws_exception(): void
    {
        $this->expectException(\DomainException::class);
        
        new Job(
            id: 5,
            title: 'Future Job',
            company: 'Time Travel Inc',
            requiredSkills: ['PHP'],
            preferredSkills: [],
            experienceRange: new ExperienceRange(0, 0),
            isRemote: true,
            postedAt: new \DateTimeImmutable('+1 day')
        );
    }

    public function test_experience_range_boundaries(): void
    {
        $this->expectNotToPerformAssertions();
        
        // Valid minimum range
        new Job(
            id: 6,
            title: 'Junior Role',
            company: 'Tech Corp',
            requiredSkills: ['PHP'],
            preferredSkills: [],
            experienceRange: new ExperienceRange(0, 0),
            isRemote: false,
            postedAt: new \DateTimeImmutable()
        );
        
        // Valid maximum range
        new Job(
            id: 7,
            title: 'CTO Position',
            company: 'Startup',
            requiredSkills: ['Leadership'],
            preferredSkills: [],
            experienceRange: new ExperienceRange(15, 30),
            isRemote: true,
            postedAt: new \DateTimeImmutable()
        );
    }

    public function invalidJobProvider(): array
    {
        return [
            'negative_id' => [-1, 'PHP Developer', ['PHP']],
            'empty_title' => [1, '', ['PHP']],
            'whitespace_title' => [1, '   ', ['PHP']],
            'numeric_company' => [1, 'Dev', ['PHP'], '123Corp']
        ];
    }

    /**
     * @dataProvider invalidJobProvider
     */
    public function test_invalid_constructor_values(
        int $id,
        string $title,
        array $skills,
        string $company = 'Test Corp'
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        
        new Job(
            id: $id,
            title: $title,
            company: $company,
            requiredSkills: $skills,
            preferredSkills: [],
            experienceRange: new ExperienceRange(0, 0),
            isRemote: true,
            postedAt: new \DateTimeImmutable()
        );
    }
}
