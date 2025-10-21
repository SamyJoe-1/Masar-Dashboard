<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory;

/**
 * Text Extraction Helper
 *
 * Extracts text content from PDF and DOCX files
 *
 * Installation required:
 * composer require smalot/pdfparser
 * composer require phpoffice/phpword
 */
class TextExtractionHelper
{
    /**
     * Extract text from file based on type
     */
    public static function extractFromFile($filePath, $mimeType)
    {
        $fullPath = Storage::disk('public')->path($filePath);

        if (!file_exists($fullPath)) {
            throw new \Exception('File not found: ' . $filePath);
        }

        // Determine extraction method based on MIME type
        if (str_contains($mimeType, 'pdf')) {
            return self::extractFromPDF($fullPath);
        } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
            return self::extractFromDOCX($fullPath);
        }

        throw new \Exception('Unsupported file type: ' . $mimeType);
    }

    /**
     * Extract text from PDF
     */
    public static function extractFromPDF($filePath)
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filePath);

            $fullText = $pdf->getText();

            // Extract metadata
            $details = $pdf->getDetails();

            // Try to identify sections
            $sections = self::identifySections($fullText);

            return [
                'full_text' => $fullText,
                'sections' => $sections,
                'metadata' => [
                    'pages' => $details['Pages'] ?? null,
                    'author' => $details['Author'] ?? null,
                    'title' => $details['Title'] ?? null,
                ],
                'success' => true
            ];

        } catch (\Exception $e) {
            return [
                'full_text' => '',
                'sections' => [],
                'error' => 'Failed to extract PDF: ' . $e->getMessage(),
                'success' => false
            ];
        }
    }

    /**
     * Extract text from DOCX
     */
    public static function extractFromDOCX($filePath)
    {
        try {
            $phpWord = IOFactory::load($filePath);
            $fullText = '';

            // Loop through sections
            foreach ($phpWord->getSections() as $section) {
                // Loop through elements in section
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $fullText .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        // Handle nested elements (like in tables)
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $fullText .= $childElement->getText() . "\n";
                            }
                        }
                    }
                }
            }

            // Identify sections
            $sections = self::identifySections($fullText);

            return [
                'full_text' => $fullText,
                'sections' => $sections,
                'metadata' => [
                    'sections_count' => count($phpWord->getSections()),
                ],
                'success' => true
            ];

        } catch (\Exception $e) {
            return [
                'full_text' => '',
                'sections' => [],
                'error' => 'Failed to extract DOCX: ' . $e->getMessage(),
                'success' => false
            ];
        }
    }

    /**
     * Identify CV sections using regex patterns
     */
    private static function identifySections($text)
    {
        $sections = [
            'summary' => '',
            'experience' => '',
            'education' => '',
            'skills' => '',
            'projects' => '',
            'certifications' => '',
            'contact' => ''
        ];

        // Section patterns (both English and Arabic)
        $patterns = [
            'summary' => '/(professional\s+summary|summary|objective|profile|about\s+me|ملخص|نبذة)/i',
            'experience' => '/(work\s+experience|experience|employment|work\s+history|الخبرة|الخبرات)/i',
            'education' => '/(education|academic|qualifications|التعليم|المؤهلات)/i',
            'skills' => '/(skills|technical\s+skills|competencies|abilities|المهارات|القدرات)/i',
            'projects' => '/(projects|portfolio|المشاريع|الأعمال)/i',
            'certifications' => '/(certifications|certificates|licenses|الشهادات)/i',
            'contact' => '/(contact|reach\s+me|get\s+in\s+touch|التواصل|الاتصال)/i'
        ];

        // Split text into lines
        $lines = explode("\n", $text);
        $currentSection = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) continue;

            // Check if line is a section header
            $foundSection = false;
            foreach ($patterns as $sectionName => $pattern) {
                if (preg_match($pattern, $line)) {
                    $currentSection = $sectionName;
                    $foundSection = true;
                    break;
                }
            }

            // Add line to current section
            if (!$foundSection && $currentSection) {
                $sections[$currentSection] .= $line . "\n";
            }
        }

        return $sections;
    }

    /**
     * Extract contact information
     */
    public static function extractContactInfo($text)
    {
        $contact = [
            'email' => null,
            'phone' => null,
            'linkedin' => null,
            'github' => null,
            'website' => null
        ];

        // Email pattern
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches)) {
            $contact['email'] = $matches[0];
        }

        // Phone pattern (international and local formats)
        if (preg_match('/(\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}/', $text, $matches)) {
            $contact['phone'] = $matches[0];
        }

        // LinkedIn
        if (preg_match('/linkedin\.com\/in\/[\w-]+/', $text, $matches)) {
            $contact['linkedin'] = 'https://' . $matches[0];
        }

        // GitHub
        if (preg_match('/github\.com\/[\w-]+/', $text, $matches)) {
            $contact['github'] = 'https://' . $matches[0];
        }

        // Website
        if (preg_match('/https?:\/\/[\w.-]+\.[a-z]{2,}/', $text, $matches)) {
            $contact['website'] = $matches[0];
        }

        return $contact;
    }

    /**
     * Clean and normalize text
     */
    public static function cleanText($text)
    {
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Remove special characters but keep basic punctuation
        $text = preg_replace('/[^\w\s\.\,\-\@\(\)]/u', '', $text);

        // Trim
        $text = trim($text);

        return $text;
    }

    /**
     * Count words in text
     */
    public static function countWords($text)
    {
        return str_word_count($text);
    }

    /**
     * Estimate reading time (words per minute)
     */
    public static function estimateReadingTime($text, $wpm = 200)
    {
        $words = self::countWords($text);
        return ceil($words / $wpm);
    }

    /**
     * Extract all dates from text
     */
    public static function extractDates($text)
    {
        $dates = [];

        // Pattern for common date formats
        $patterns = [
            '/\b\d{4}\b/',                           // 2024
            '/\b\d{1,2}\/\d{4}\b/',                 // 01/2024
            '/\b\d{1,2}-\d{4}\b/',                  // 01-2024
            '/\b(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\s+\d{4}\b/i',  // Jan 2024
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                $dates = array_merge($dates, $matches[0]);
            }
        }

        return array_unique($dates);
    }

    /**
     * Check if CV has specific keywords
     */
    public static function hasKeywords($text, $keywords)
    {
        $found = [];
        $missing = [];

        foreach ($keywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $found[] = $keyword;
            } else {
                $missing[] = $keyword;
            }
        }

        return [
            'found' => $found,
            'missing' => $missing,
            'match_percentage' => count($found) > 0 ? round((count($found) / count($keywords)) * 100, 2) : 0
        ];
    }

    /**
     * Extract skills using common patterns
     */
    public static function extractSkills($text)
    {
        // Common technical skills patterns
        $techSkills = [
            // Programming Languages
            'PHP', 'JavaScript', 'Python', 'Java', 'C\+\+', 'C#', 'Ruby', 'Go', 'Swift', 'Kotlin',
            'TypeScript', 'Rust', 'Scala', 'R', 'MATLAB',

            // Frameworks
            'Laravel', 'Vue\.js', 'React', 'Angular', 'Node\.js', 'Express', 'Django', 'Flask',
            'Spring', 'ASP\.NET', 'Rails', 'Symfony',

            // Databases
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'Oracle', 'SQL Server',
            'MariaDB', 'Cassandra', 'DynamoDB',

            // DevOps & Cloud
            'Docker', 'Kubernetes', 'AWS', 'Azure', 'GCP', 'CI/CD', 'Jenkins', 'GitLab CI',
            'Terraform', 'Ansible', 'Linux', 'Nginx', 'Apache',

            // Tools
            'Git', 'GitHub', 'GitLab', 'Bitbucket', 'JIRA', 'Confluence', 'Slack',
            'Figma', 'Photoshop', 'Illustrator',

            // Other
            'REST API', 'GraphQL', 'Microservices', 'Agile', 'Scrum', 'TDD', 'Unit Testing'
        ];

        $foundSkills = [];

        foreach ($techSkills as $skill) {
            if (preg_match('/\b' . preg_quote($skill, '/') . '\b/i', $text)) {
                $foundSkills[] = str_replace('\.', '.', $skill);
            }
        }

        return array_unique($foundSkills);
    }
}
