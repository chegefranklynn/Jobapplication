<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Resume;

use App\Domain\Resume\SkillMatcher;
use PHPUnit\Framework\TestCase;

class SkillMatcherTest extends TestCase
{
    private SkillMatcher $matcher;

    protected function setUp(): void
    {
        $this->matcher = new SkillMatcher();
    }

    public function similarityProvider(): array
    {
        return [
            ['PHP', 'PHP', 1.0],
            ['JavaScript', 'Javascript', 0.92], // levenshtein 1/12
            ['AWS', 'Amazon Web Services', 0.0], // No substring matching
        ];
    }

    /**
     * @dataProvider similarityProvider
     */
    public function testCalculateSimilarity(string $a, string $b, float $expected): void
    {
        $result = $this->matcher->matchSkills([$a], [$b], 0);
        $this->assertEqualsWithDelta($expected, $result['match_rate'], 0.01);
    }

    public function testThresholdBehavior(): void
    {
        $result = $this->matcher->matchSkills(
            ['React.js'], 
            ['React'],
            threshold: 0.8 // levenshtein distance 5/7 → 0.285 similarity
        );
        
        $this->assertEquals(0.0, $result['match_rate']);
    }

    public function testCaseInsensitivity(): void
    {
        $result = $this->matcher->matchSkills(
            ['aws'], 
            ['AWS']
        );
        
        $this->assertEquals(1.0, $result['match_rate']);
    }

    public function testDuplicateSkillsHandling(): void
    {
        $result = $this->matcher->matchSkills(
            ['PHP', 'PHP', 'AWS'],
            ['PHP', 'AWS']
        );
        
        $this->assertEquals(1.0, $result['match_rate']);
    }

    public function testSpecialCharactersInSkills(): void
    {
        $result = $this->matcher->matchSkills(
            ['C#'], 
            ['C Sharp'],
            threshold: 0.5
        );
        
        $this->assertGreaterThan(0.4, $result['match_rate']);
    }
} 