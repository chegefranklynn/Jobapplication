<?php

namespace App\Repositories;

use App\Domain\Models\Applicant;
use PDO;

class ResumeRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function saveApplicant(Applicant $applicant): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO applicants 
             (name, email, experience_years, skills) 
             VALUES (:name, :email, :exp, :skills)"
        );

        $stmt->execute([
            ':name' => $applicant->name,
            ':email' => $applicant->email,
            ':exp' => $applicant->experienceYears,
            ':skills' => json_encode($applicant->skills)
        ]);

        return (int)$this->db->lastInsertId();
    }
} 