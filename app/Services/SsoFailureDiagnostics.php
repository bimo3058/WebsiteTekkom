<?php

namespace App\Services;

use GuzzleHttp\Exception\RequestException;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class SsoFailureDiagnostics
{
    /** Never log exception messages, request headers, URLs, tokens or raw response bodies. */
    public function context(Throwable $exception): array
    {
        $context = ['exception' => get_class($exception)];
        if (! $exception instanceof RequestException) return $context;

        $context['http_status'] = $exception->getResponse()?->getStatusCode();
        $host = strtolower($exception->getRequest()->getUri()->getHost());
        $context['provider'] = match ($host) {
            'login.microsoftonline.com' => 'microsoft_identity',
            'graph.microsoft.com' => 'microsoft_graph',
            default => 'other',
        };
        $body = $exception->getResponse()?->getBody();
        if (! $body || ! $body->isSeekable()) return $context;
        $position = $body->tell();
        try {
            $body->rewind();
            $data = json_decode($body->read(65536), true);
        } finally {
            $body->seek($position);
        }
        if (! is_array($data)) return $context;
        $error = $data['error'] ?? null;
        $code = is_array($error) ? ($error['code'] ?? null) : $error;
        if (in_array($code, ['invalid_client', 'invalid_grant', 'invalid_request', 'unauthorized_client', 'access_denied', 'temporarily_unavailable', 'InvalidAuthenticationToken', 'Authorization_RequestDenied', 'ErrorAccessDenied'], true)) {
            $context['provider_error'] = $code;
        }
        $codes = $data['error_codes'] ?? [];
        if (is_array($codes)) {
            $context['aad_codes'] = array_values(array_slice(array_filter($codes, fn ($value) => is_int($value) && $value > 0 && $value < 1000000000), 0, 8));
        }

        return $context;
    }

    public function message(Throwable $exception, string $reference): string
    {
        $message = $exception instanceof InvalidStateException
            ? 'Sesi login SSO tidak valid atau telah berakhir. Buka kembali login melalui alamat situs yang sama, lalu coba lagi.'
            : 'Login SSO belum berhasil diproses oleh SITKOM. Jika berulang, sampaikan kode referensi berikut kepada pengelola.';

        return $message.' Referensi: '.$reference;
    }
}
