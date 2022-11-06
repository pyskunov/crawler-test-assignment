<?php

namespace App\Domains\Crawler\HTML;

use App\Domains\Crawler\DTO\CrawlerTask;
use App\Domains\Crawler\HTML\Pipes\TakeExternalURLsPipe;
use App\Domains\Crawler\HTML\Pipes\TakeInternalURLsPipe;
use App\Domains\Crawler\HTML\Pipes\TakeTitlePipe;
use App\Domains\Crawler\HTML\Pipes\TakeImagesPipe;
use App\Domains\Crawler\HTML\Pipes\TakeWordsCountPipe;
use Illuminate\Http\Response;
use Illuminate\Pipeline\Pipeline;
use Throwable;

class HTMLParserPipeline
{
    /**
     * Accepts and Edits DTO
     */
    public function parse(CrawlerTask $task): void
    {
        try {
            /**
             * What I like in Pipelines is that they are here with us :)
             * Not always we can find a way of usage for it as they are complex enough
             * But I find this task a beautiful example of Chain of Responsibilities pattern.
             * If one fails - all the task is failed
             */
            app(Pipeline::class)
                ->send($task)
                ->through([
                    TakeInternalURLsPipe::class,
                    TakeExternalURLsPipe::class,
                    TakeImagesPipe::class,
                    TakeWordsCountPipe::class,
                    TakeTitlePipe::class,
                ])
                ->then(fn(CrawlerTask $task) => $task->finishTask());
        } catch (Throwable $e) {
            throw new HTMLParserException(
                'Failed to parse the HTML',
                Response::HTTP_EXPECTATION_FAILED,
                $e
            );
        }
    }
}
