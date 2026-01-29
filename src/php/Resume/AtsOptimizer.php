<?php

declare(strict_types=1);

namespace App\Resume;

class AtsOptimizer {
    public function calculateScore(
        array $resumeKeywords,
        array $jobKeywords
    ): float {
        $matches = array_intersect(
            array_map('strtolower', $resumeKeywords),
            array_map('strtolower', $jobKeywords)
        );
        
        return (count($matches) / count($jobKeywords)) * 100;
    }

    public function suggestImprovements(
        array $resumeContent,
        array $jobDescription
    ): array {
        $missing = array_diff(
            $this->extractKeywords($jobDescription),
            $this->extractKeywords($resumeContent)
        );
        
        return [
            'missing_keywords' => $missing,
            'recommended_actions' => $this->generateActions($missing)
        ];
    }
    
    private function extractKeywords(array $content): array
    {
        // Basic keyword extraction (TF-IDF would be better)
        $words = array_count_values(
            str_word_count(
                strtolower(implode(' ', $content)),
                1
            )
        );
        arsort($words);
        return array_slice(array_keys($words), 0, 20);
    }
}