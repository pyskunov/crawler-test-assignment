<?php

namespace App\Domains\Crawler\HTML\Pipes;

use App\Domains\Crawler\DTO\CrawlerTask;
use Closure;
use DOMElement;
use Illuminate\Support\Str;

class TakeExternalURLsPipe
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

                if ($this->isExternalURL($task, $href)) {
                    $collector->push($href);
                }
            }
        }

        $task->setExternalURLs(
            $collector->toArray()
        );

        return $next($task);
    }

    private function isExternalURL(CrawlerTask $task, string $href): bool
    {
        $stringable = Str::of($href);

        return !$stringable->startsWith('/')
            && !$stringable->startsWith('#')
            && !$stringable->startsWith($task->getURL())
            && !$stringable->startsWith(preg_replace('/https?:/', '', $task->getURL()));
    }
}
