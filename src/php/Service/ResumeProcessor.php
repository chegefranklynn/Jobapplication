<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Applicant;
use App\Repositories\ResumeRepository;
use App\Resume\AtsOptimizer;
use App\Resume\ResumeParser;
use App\Resume\SkillMatcher;

class ResumeProcessor {
    public function __construct(
        private ResumeParser $parser,
        private AtsOptimizer $optimizer,
        private SkillMatcher $matcher,
        private ResumeRepository $repository
    ) {}
    
    public function processUpload(
        string $filePath,
        Applicant $applicant,
        array $jobData
    ): array {
        // Step 1: Parse resume
        $parsed = $this->parser->parsePdf($filePath);
        
        // Step 2: Calculate ATS score
        $score = $this->optimizer->calculateScore(
            $parsed['keywords'],
            $jobData['keywords']
        );
        
        // Step 3: Save to database
        $applicantId = $this->repository->saveApplicant($applicant);
        $resumeId = $this->repository->saveResume(
            $applicantId,
            $filePath,
            $parsed,
            $score
        );
        
        // Step 4: Match skills to jobs
        $matches = [];
        foreach ($jobData['jobs'] as $job) {
            $result = $this->matcher->matchSkills(
                $parsed['skills'],
                $job['required_skills']
            );
            
            $this->repository->saveJobMatch(
                $resumeId,
                $job['id'],
                $result['match_rate'],
                $result['matches']
            );
            
            $matches[] = $result;
        }
        
        return [
            'applicant_id' => $applicantId,
            'resume_id' => $resumeId,
            'ats_score' => $score,
            'matches' => $matches
        ];
    }
}