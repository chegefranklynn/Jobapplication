<?php

namespace App\Tests\Unit\Domain\Models;

use PHPUnit\Framework\TestCase;
use App\Domain\Models\Applicant;

class ApplicantTest extends TestCase
{
    public function test_invalid_email_format(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        new Applicant(
            id: 3,
            name: 'Bad Email',
            email: 'not-an-email',
            experienceYears: 2,
            skills: ['PHP'],
            experienceRange: ['min' => 1, 'max' => 3]
        );
    }

    public function test_experience_range_edge_cases(): void
    {
        // Min equals max
        $applicant = new Applicant(
            id: 4,
            name: 'Exact Experience',
            email: 'exact@test.com',
            experienceYears: 5,
            skills: [],
            experienceRange: ['min' => 5, 'max' => 5]
        );
        
        $this->assertEquals(5, $applicant->experienceRange['min']);
    }

    public function test_skill_deduplication(): void
    {
        $applicant = new Applicant(
            id: 5,
            name: 'Duplicate Skills',
            email: 'dupes@test.com',
            experienceYears: 3,
            skills: ['PHP', 'php', 'PHP'],
            experienceRange: ['min' => 1, 'max' => 5]
        );
        
        $this->assertCount(2, $applicant->getSkillSet());
    }

    public function test_empty_skills_allowed(): void
    {
        $applicant = new Applicant(
            id: 6,
            name: 'No Skills',
            email: 'noskills@test.com',
            experienceYears: 0,
            skills: [],
            experienceRange: ['min' => 0, 'max' => 0]
        );
        
        $this->assertCount(0, $applicant->getSkillSet());
    }

    public function test_max_experience_years(): void
    {
        $this->expectNotToPerformAssertions();
        
        new Applicant(
            id: 7,
            name: 'Veteran',
            email: 'veteran@test.com',
            experienceYears: PHP_INT_MAX,
            skills: ['Legacy'],
            experienceRange: ['min' => 0, 'max' => PHP_INT_MAX]
        );
    }
}
