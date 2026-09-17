<?php

namespace App\Http\Middleware;

use App\Support\PageTitle;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyPageMetadata
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->isHtmlResponse($response)) {
            return $response;
        }

        $content = $response->getContent();
        if (! is_string($content) || stripos($content, '</head>') === false) {
            return $response;
        }

        $metadata = PageTitle::forRequest($request);
        $content = $this->applyTitle($content, $metadata['full']);
        $content = $this->applyIcons($content);
        $response->setContent($content);

        return $response;
    }

    private function isHtmlResponse(Response $response): bool
    {
        if (! method_exists($response, 'getContent')) {
            return false;
        }

        $contentType = strtolower((string) $response->headers->get('Content-Type'));

        return $contentType === '' || str_contains($contentType, 'text/html');
    }

    private function applyTitle(string $content, string $resolvedTitle): string
    {
        $safeTitle = e($resolvedTitle);
        if (! preg_match('/<title\b[^>]*>(.*?)<\/title>/is', $content, $matches)) {
            return preg_replace('/<\/head>/i', "    <title>{$safeTitle}</title>\n</head>", $content, 1) ?? $content;
        }

        if (! PageTitle::isGenericExistingTitle($matches[1])) {
            return $content;
        }

        return preg_replace(
            '/<title\b[^>]*>.*?<\/title>/is',
            "<title>{$safeTitle}</title>",
            $content,
            1
        ) ?? $content;
    }

    private function applyIcons(string $content): string
    {
        $content = preg_replace(
            '/<link\b(?=[^>]*\brel\s*=\s*["\'][^"\']*(?:shortcut\s+icon|icon|apple-touch-icon)[^"\']*["\'])[^>]*>\s*/i',
            '',
            $content
        ) ?? $content;

        $iconUrl = e(asset('images/UNDIPOfficial.png'));
        $icons = implode("\n", [
            "    <link rel=\"icon\" type=\"image/png\" href=\"{$iconUrl}\">",
            "    <link rel=\"shortcut icon\" type=\"image/png\" href=\"{$iconUrl}\">",
            "    <link rel=\"apple-touch-icon\" href=\"{$iconUrl}\">",
        ]);

        return preg_replace('/<\/head>/i', "{$icons}\n</head>", $content, 1) ?? $content;
    }
}
