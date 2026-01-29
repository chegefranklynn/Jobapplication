<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Resume;

use App\Domain\Resume\AtsOptimizer;
use PHPUnit\Framework\TestCase;

class AtsOptimizerTest extends TestCase
{
    private AtsOptimizer $optimizer;

    protected function setUp(): void
    {
        $this->optimizer = new AtsOptimizer();
    }

    public function testScoreCalculation(): void
    {
        $score = $this->optimizer->calculateScore(
            ['PHP', 'AWS'], 
            ['php', 'aws', 'docker']
        );
        
        $this->assertEquals(66.67, $score, 0.01);
    }

    public function testSuggestionLogic(): void
    {
        $suggestions = $this->optimizer->suggestImprovements(
            ['Experienced in PHP development'],
            ['Looking for PHP and Docker skills']
        );
        
        $this->assertContains('docker', $suggestions['missing_keywords']);
        $this->assertStringContainsString(
            'Add 2 missing keywords',
            $suggestions['recommended_actions'][0]
        );
    }

    public function testKeywordExtraction(): void
    {
        $keywords = $this->invokeMethod(
            $this->optimizer,
            'extractKeywords',
            [['PHP PHP Symfony Docker']]
        );
        
        $this->assertEquals(['php', 'symfony', 'docker'], $keywords);
    }

    private function invokeMethod(object $obj, string $name, array $args)
    {
        $method = new \ReflectionMethod($obj, $name);
        return $method->invokeArgs($obj, $args);
    }
} 