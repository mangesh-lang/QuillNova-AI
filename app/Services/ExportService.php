<?php

namespace App\Services;

use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportService
{
    /**
     * Export content as a downloadable TXT file.
     */
    public function exportToTxt(string $title, string $content)
    {
        $filename = Str::slug($title) ?: 'generated-content';
        $filename .= '.txt';

        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export content as a downloadable PDF file.
     */
    public function exportToPdf(string $title, string $content)
    {
        $filename = Str::slug($title) ?: 'generated-content';
        $filename .= '.pdf';

        // Prepare a clean styled HTML layout for Dompdf rendering
        $html = view('pdf.content', [
            'title' => $title,
            'content' => $content,
        ])->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download($filename);
    }
}
