<?php

namespace App\Domains\Crawler\HTML\Pipes;

use App\Domains\Crawler\DTO\CrawlerTask;
use Closure;

class TakeTitlePipe
{
    public function handle(CrawlerTask $task, Closure $next)
    {
        $task->setTitle(
            $task->getDOMDocument()->getElementsByTagName('title')->item(0)->textContent
        );

        return $next($task);
    }
}
