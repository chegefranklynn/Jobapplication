<?php

declare(strict_types=1);

namespace App\Resume;

use PhpOffice\PhpWord\IOFactory;

class DocxParser {
    public function parseDocx(string $filePath): array
    {
        $phpWord = IOFactory::load($filePath);
        $text = '';
        
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $text .= $element->getText() . "\n";
            }
        }
        
        return [
            'text' => $this->cleanText($text),
            'metadata' => [
                'pages' => count($phpWord->getSections())
            ]
        ];
    }
}