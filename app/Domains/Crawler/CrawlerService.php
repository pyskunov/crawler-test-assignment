<?php

namespace App\Domains\Crawler;

use App\Domains\Crawler\DTO\CrawlerOutput;
use App\Domains\Crawler\DTO\CrawlerTask;
use App\Domains\Crawler\HTML\HTMLParserPipeline;
use App\Domains\Crawler\HTTP\HttpClient;
use Illuminate\Http\Response;
use Throwable;
use RuntimeException;

class CrawlerService
{
    /**
     * For storring runtime errors
     */
    private array $errors = [];

    /**
     * Tasks pool
     */
    private array $urlPool = [];

    /**
     * Results box
     */
    private array $results = [];

    /**
     * Configs goes here. @see config/crawler.php
     */
    private array $config;

    public function __construct(
        // PHP readonly implementation actually something out of my understanding.
        // That's one of the redundant features from my point of sight
        // However, I am waiting 8.2 to have consts in traits :)
        private readonly HttpClient         $httpClient,
        private readonly HTMLParserPipeline $HTMLParser,
    )
    {
        // Let's make it configurable
        $this->config = config('crawler');
    }

    /**
     * Expected process handled here
     */
    public function main(
        string $targetURL
    ): CrawlerOutput {
        /**
         * I thought a lot about websockets here.
         * That was a kind of nice to have thing.
         *
         * I will say that assignment says "Backend challenge", not "Full stack".
         * But I like signals.
         *
         * Socket.io / Pusher can do a great job here.
         * We can also connect Queues in order to make it scalable.
         *
         * The plan was:
         * - Assign ID to the process (Str::uuid())
         * - Queue tasks (chain of tasks here, but we can do 1st + (max - 1) refactoring in order to make it perfect
         * - Whenever something happens - send it to WS
         * - Catch WS on the Frontend with Laravel Echo
         * - Update the table in a live mode.
         * - It can be also Queue Job dispatching Queue Job as a chain of responsibilities
         *
         * I estimate efforts for this as 13h (Fibonacci) and see it as extremely big for test assignments.
         * But I wish I could return back to it one day in the future when I will have time to do so.
         */

        // The very first task obviously will be our target
        $this->urlPool[] = $targetURL;

        // While pool has tasks and we have not too much errors + not enough results
        while (!empty($this->urlPool) && $this->needsMoreResults() && $this->isTooMuchErrors()) {
            try {
                // Pull next URL from the pool
                $nextURL = $this->pullInternalURL();

                // Init task
                $nextTask = CrawlerTask::make($nextURL);

                // Load the HTML
                $this->httpClient->loadDOMDocument($nextTask);

                // Parse it
                $this->HTMLParser->parse($nextTask);

                // Store results
                $this->results[] = $nextTask;

                // Take some new links to the pool
                $this->urlPool = array_merge(
                    $nextTask->getInternalURLs(),
                    $this->urlPool
                );
            } catch (Throwable $e) {
                // When something went wrong - let's store it.
                // I decided to go without Logging as I see no big profit of it during the test assignment
                $this->errors[] = $e;

                // Let's also say that we failed as far as no more tasks so it will be obvoious
                if (empty($this->urlPool)) {
                    $this->errors[] = new RuntimeException(
                        'Empty tasks pool',
                        Response::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            }
        }

        // Let's return results
        return CrawlerOutput::make(
            $this->results,
            $this->errors
        );
    }

    /**
     * Recursive pool of an URL that we still did not visit
     */
    public function pullInternalURL(): ?string
    {
        // pop from empty array = fatal. That's OK as far as we are handling it in the catch
        $url = array_pop(
            $this->urlPool
        );

        if (in_array($url, $this->getCurrentUsedURLs())) {
            return $this->pullInternalURL();
        }

        return $url;
    }

    /**
     * In results we store where we were
     */
    public function getCurrentUsedURLs(): array
    {
        return collect($this->results)
            ->map(fn(CrawlerTask $task) => $task->getURL())
            ->toArray();
    }

    private function needsMoreResults(): bool
    {
        return count($this->results) < $this->config['max_results'];
    }

    private function isTooMuchErrors(): bool
    {
        return count($this->errors) < $this->config['max_errors'];
    }
}
