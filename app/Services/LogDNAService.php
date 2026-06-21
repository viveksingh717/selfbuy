<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LogDNAService
{
    protected string $ingestionKey;
    protected string $host;
    protected string $url;

    public function __construct()
    {
        $this->ingestionKey = env('LOGDNA_INGESTION_KEY');
        $this->host = gethostname();
        $this->url = 'https://logs.logdna.com/logs/ingest';
    }

    /**
     * Send log to LogDNA
     */
    public function send(string $message, array $meta = [], string $level = 'info'): void
    {
        try {
            Http::post($this->url, [
                'lines' => [
                    [
                        'timestamp' => time() * 1000,
                        'line'      => $message,
                        'app'       => config('app.name'),
                        'level'     => $level,
                        'meta'      => $meta
                    ]
                ],
                'hostname' => $this->host,
                'now'      => time() * 1000
            ], [
                'Content-Type' => 'application/json',
                'apikey'       => $this->ingestionKey,
            ]);
        } catch (Exception $e) {
            // fallback to Laravel log if LogDNA fails
            Log::error('LogDNA failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log request data
     */
    public function logRequest($request, string $tag = 'REQUEST'): void
    {
        $this->send($tag, [
            'method' => $request->method(),
            'url'    => $request->fullUrl(),
            'ip'     => $request->ip(),
            'body'   => $request->all()
        ]);
    }

    /**
     * Log response data
     */
    public function logResponse($response, string $tag = 'RESPONSE'): void
    {
        $this->send($tag, [
            'response' => $response
        ]);
    }

    /**
     * Log exception
     */
    public function logException(\Throwable $e, string $tag = 'EXCEPTION'): void
    {
        $this->send($tag, [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ], 'error');
    }

    /**
     * Custom log
     */
    public function info(string $message, array $meta = []): void
    {
        $this->send($message, $meta, 'info');
    }

    public function error(string $message, array $meta = []): void
    {
        $this->send($message, $meta, 'error');
    }
}