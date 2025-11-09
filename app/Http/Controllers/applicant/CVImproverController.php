<?php

namespace App\Http\Controllers\applicant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CVImproverController extends Controller
{
    /**
     * Generate PDF from HTML content
     */
    public function generatePDF(Request $request)
    {
        $request->validate([
            'html' => 'required|string'
        ]);

        try {
            $html = $request->input('html');

            // Create a complete HTML document with proper styling
            $fullHtml = $this->wrapHTMLForPDF($html);

            // Generate unique filename
            $filename = 'improved_cv_' . Str::random(10) . '.pdf';
            $path = storage_path('app/public/' . $filename);

            // Generate PDF using Browsershot
            Browsershot::html($fullHtml)
                ->format('A4')
                ->margins(15, 15, 15, 15)
                ->showBackground()
                ->waitUntilNetworkIdle()
                ->save($path);

            // Return the PDF file
            return response()->download($path)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Wrap HTML content in a complete document with styling for PDF
     */
    private function wrapHTMLForPDF($content)
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Improved CV</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
        }

        .cv-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }

        h1 {
            font-size: 28px;
            color: #1e293b;
            margin-bottom: 10px;
            font-weight: 700;
        }

        h2 {
            font-size: 20px;
            color: #3464b0;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3464b0;
            font-weight: 600;
            page-break-after: avoid;
        }

        h3 {
            font-size: 16px;
            color: #1e293b;
            margin-top: 15px;
            margin-bottom: 8px;
            font-weight: 600;
            page-break-after: avoid;
        }

        p {
            margin-bottom: 12px;
            color: #475569;
            font-size: 14px;
        }

        ul {
            margin-left: 25px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        li {
            margin-bottom: 8px;
            color: #475569;
            font-size: 14px;
        }

        /* Contact info styling */
        .cv-content > p:nth-of-type(-n+4) {
            margin-bottom: 5px;
            font-size: 13px;
        }

        /* Prevent page breaks inside important elements */
        .cv-content > div,
        .cv-content > section {
            page-break-inside: avoid;
        }

        /* Ensure proper page breaks */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            h2 {
                page-break-after: avoid;
            }

            h3 {
                page-break-after: avoid;
            }

            ul, ol {
                page-break-inside: avoid;
            }

            p {
                orphans: 3;
                widows: 3;
            }
        }
    </style>
</head>
<body>
    {$content}
</body>
</html>
HTML;
    }
}
