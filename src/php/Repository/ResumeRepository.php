<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Applicant;
use PDO;

class ResumeRepository {
    public function __construct(
        private PDO $db
    ) {}
    
    public function saveApplicant(Applicant $applicant): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO applicants (name, email) 
             VALUES (:name, :email)"
        );
        
        $stmt->execute([
            ':name' => $applicant->name,
            ':email' => $applicant->email
        ]);
        
        return (int)$this->db->lastInsertId();
    }
    
    public function saveResume(
        int $applicantId,
        string $filePath,
        array $parsedData,
        float $atsScore
    ): int {
        $stmt = $this->db->prepare(
            "INSERT INTO resumes 
             (applicant_id, file_path, parsed_data, ats_score)
             VALUES (:applicant_id, :file_path, :parsed_data, :ats_score)"
        );
        
        $stmt->execute([
            ':applicant_id' => $applicantId,
            ':file_path' => $filePath,
            ':parsed_data' => json_encode($parsedData),
            ':ats_score' => $atsScore
        ]);
        
        return (int)$this->db->lastInsertId();
    }
    
    public function saveJobMatch(
        int $resumeId,
        int $jobId,
        float $score,
        array $matchedSkills
    ): void {
        $stmt = $this->db->prepare(
            "INSERT INTO job_matches 
             (resume_id, job_id, match_score, matched_skills)
             VALUES (:resume_id, :job_id, :score, :skills)"
        );
        
        $stmt->execute([
            ':resume_id' => $resumeId,
            ':job_id' => $jobId,
            ':score' => $score,
            ':skills' => json_encode($matchedSkills)
        ]);
    }
}
