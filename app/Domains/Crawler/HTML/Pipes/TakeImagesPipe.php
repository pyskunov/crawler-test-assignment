<?php

namespace App\Domains\Crawler\HTML\Pipes;

use App\Domains\Crawler\DTO\CrawlerTask;
use Closure;
use DOMElement;
use Illuminate\Support\Str;

class TakeImagesPipe
{
    public function handle(CrawlerTask $task, Closure $next)
    {
        $images = $task->getDOMDocument()->getElementsByTagName('img');

        $collector = collect();

        /**
         * @var $image DOMElement
         */
        foreach ($images->getIterator() as $image) {
            $src = $image->getAttribute('src')
                ? $image->getAttribute('src')
                : $image->getAttribute('data-src');

            if ($src) {
                $collector->push($src);
            }
        }

        $baseURL = parse_url($task->getURL());

        $task->setImages(
            $collector->map(function (string $imageURI) use ($baseURL) {
                if (Str::of($imageURI)->startsWith('/')) {
                    $imageURI = $baseURL['scheme']
                        . '://'
                        . $baseURL['host']
                        . '/'
                        . ltrim($imageURI, '/');
                }

                return $imageURI;
            })->toArray()
        );

        return $next($task);
    }
}
