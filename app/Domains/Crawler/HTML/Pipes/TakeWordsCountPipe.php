<?php

namespace App\Domains\Crawler\HTML\Pipes;

use App\Domains\Crawler\DTO\CrawlerTask;
use Closure;
use DOMElement;
use Illuminate\Support\Str;
use DOMNodeList;

class TakeWordsCountPipe
{
    public function handle(CrawlerTask $task, Closure $next)
    {
        $textTags = [
            'p',
            'a',
            'button',
            'strong',
            'em',
            'mark',
            'b',
            'i',
            's',
            'small',
            'sup',
            'sub',
            'pre',
            'dfn',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'q',
            'blockquote',
            'cite',
            'code',
            'samp',
            'kbd',
            'var',
            'time',
        ];

        $longString = "";
        /**
         * @var $textTag DOMNodeList
         */
        foreach ($textTags as $textTag) {
            /**
             * @var $tag DOMElement
             */
            foreach ($task->getDOMDocument()->getElementsByTagName($textTag)->getIterator() as $tag) {
                if ($tag->textContent) {
                    $longString .= " {$tag->textContent}";
                }
            }
        }

        $task->setWordsCount(
            Str::of($longString)
                ->trim()
                ->wordCount()
        );

        return $next($task);
    }
}
