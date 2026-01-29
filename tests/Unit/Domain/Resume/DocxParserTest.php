<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Resume;

use App\Domain\Resume\DocxParser;
use PHPUnit\Framework\TestCase;

class DocxParserTest extends TestCase
{
    private const TEST_DOCX = __DIR__.'/../../../../tests/samples/sample_resume.docx';

    public function testParsesValidDocx(): void
    {
        $parser = new DocxParser();
        $result = $parser->parseDocx(self::TEST_DOCX);
        
        $this->assertStringContainsString('Work Experience', $result['text']);
        $this->assertGreaterThan(0, $result['metadata']['pages']);
    }

    public function testInvalidFileThrowsException(): void
    {
        $this->expectException(\RuntimeException::class);
        
        $parser = new DocxParser();
        $parser->parseDocx('/path/to/nonexistent.docx');
    }

    public function testTextCleaning(): void
    {
        $parser = new DocxParser();
        $dirtyText = "  Hello\tWorld\n\r";
        $cleanText = $this->invokeMethod($parser, 'cleanText', [$dirtyText]);
        
        $this->assertEquals('Hello World', $cleanText);
    }

    public function testDifferentWordVersions(): void
    {
        $parser = new DocxParser();
        $versions = ['2003', '2007', '2013', '2019'];
        foreach ($versions as $version) {
            $result = $parser->parseDocx("sample_{$version}.docx");
            $this->assertNotEmpty($result['text']);
        }
    }

    public function testImageOnlyResume(): void
    {
        $this->expectException(\RuntimeException::class);
        $parser = new DocxParser();
        $parser->parseDocx('image_only.docx');
    }

    private function invokeMethod(object $obj, string $name, array $args)
    {
        $method = new \ReflectionMethod($obj, $name);
        return $method->invokeArgs($obj, $args);
    }
} 