<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Shared CSV / PDF download helpers for admin exports (orders, transactions, dashboard report).
 */
class ExportService
{
    /** PDFs are for reading/printing - past this many rows, use CSV. */
    public const PDF_ROW_LIMIT = 1000;

    /**
     * Stream a CSV. $writer receives a callable $put(array $row) and writes rows with it
     * (so callers can chunk through big queries without loading everything).
     */
    public function csv(string $filename, Closure $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($writer) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel shows ₹ and names correctly
            $writer(fn (array $row) => fputcsv($out, $row));
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Render an admin export view (extends admin.exports.layout) as an A4 PDF download. */
    public function pdf(string $view, array $data, string $filename, string $orientation = 'landscape'): Response
    {
        return Pdf::loadView($view, $data + ['generatedAt' => now()])
            ->setOption('isFontSubsettingEnabled', true) // embed only the glyphs used -> small file
            ->setPaper('a4', $orientation)
            ->download($filename);
    }

    /** "Status: Pending · From: 01 Sep 2026" line describing the filters used. */
    public static function describeFilters(array $filters, array $labels = []): string
    {
        $parts = [];
        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $label = $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
            $shown = in_array($key, ['date_from', 'date_to'], true) ? date('d M Y', strtotime($value)) : ucfirst((string) $value);
            $parts[] = "{$label}: {$shown}";
        }

        return $parts ? implode(' · ', $parts) : 'All records';
    }
}
