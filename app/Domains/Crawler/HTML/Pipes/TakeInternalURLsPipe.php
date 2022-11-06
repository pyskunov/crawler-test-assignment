<?php

namespace App\Domains\Crawler\HTML\Pipes;

use App\Domains\Crawler\DTO\CrawlerTask;
use Closure;
use DOMElement;
use Illuminate\Support\Str;

class TakeInternalURLsPipe
{
    public function handle(CrawlerTask $task, Closure $next)
    {
        $anchors = $task->getDOMDocument()->getElementsByTagName('a');

        $collector = collect();

        /**
         * @var $anchor DOMElement
         */
        foreach ($anchors->getIterator() as $anchor) {
            if ($anchor->hasAttribute('href')) {
                $href = $anchor->getAttribute('href');

                if ($this->isInternalURL($task, $href)) {
                    $collector->push($href);
                }
            }
        }

        $task->setInternalURLs(
            $collector
                ->unique()
                ->map(
                    function (string $uri) use ($task) {
                        $baseURL = parse_url($task->getURL(), PHP_URL_SCHEME)
                            . '://'
                            . parse_url($task->getURL(), PHP_URL_HOST);

                        return preg_match('/^#/', $uri)
                            ? "{$baseURL}{$uri}"
                            : $baseURL . '/' . ltrim($uri, '/');
                    }
                )
                ->toArray()
        );

        return $next($task);
    }

    private function isInternalURL(CrawlerTask $task, string $href): bool
    {
        /**
         * Self-reference cannot be new link
         */
        if ($href === '/') {
            return false;
        }

        /**
         * Self-section loop anchor can be an internal link.
         */
        if (preg_match('/^\/?#.*/', $href)) {
            return true;
        }

        if (Str::of($href)->startsWith('/')) {
            return true;
        }

        /**
         * Full links starts with the base target domain are internal
         */
        $baseURL = parse_url($task->getURL());

        return Str::of($href)->startsWith([
            "{$baseURL['scheme']}://{$baseURL['host']}",
            "//{$baseURL['host']}",
        ]);
    }
}
