<?php

declare(strict_types=1);

namespace App\Resume;

class SkillMatcher {
    public function matchSkills(
        array $applicantSkills,
        array $jobSkills,
        float $threshold = 0.6
    ): array {
        $matches = [];
        
        foreach ($jobSkills as $jobSkill) {
            foreach ($applicantSkills as $applicantSkill) {
                $similarity = $this->calculateSimilarity(
                    $jobSkill,
                    $applicantSkill
                );
                
                if ($similarity >= $threshold) {
                    $matches[$jobSkill] = $applicantSkill;
                }
            }
        }
        
        return [
            'match_rate' => count($matches) / count($jobSkills),
            'matches' => $matches
        ];
    }

    private function calculateSimilarity(
        string $a,
        string $b
    ): float {
        // Levenshtein-based similarity
        $maxLen = max(strlen($a), strlen($b));
        return 1 - (levenshtein($a, $b) / $maxLen);
    }
}