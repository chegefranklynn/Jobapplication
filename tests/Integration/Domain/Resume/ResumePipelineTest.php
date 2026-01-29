<?php

namespace Tests\Integration\Domain\Resume;

use PHPUnit\Framework\TestCase;
use App\Domain\Resume\DocxParser;
use App\Domain\Resume\AtsOptimizer;

class ResumePipelineTest extends TestCase
{
    public function testEndToResumeProcessing(): void
    {
        // Sample job description keywords
        $jobKeywords = ['PHP', 'MySQL', 'REST APIs'];
        
        // Process resume
        $parser = new DocxParser();
        $content = $parser->parseDocx('sample_resume.docx');
        
        $optimizer = new AtsOptimizer();
        $score = $optimizer->calculateScore(
            $optimizer->extractKeywords([$content['text']]),
            $jobKeywords
        );
        
        $this->assertGreaterThan(70.0, $score);
    }
} 