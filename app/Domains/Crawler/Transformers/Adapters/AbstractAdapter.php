<?php

namespace App\Domains\Crawler\Transformers\Adapters;

use App\Domains\Crawler\DTO\CrawlerOutput;
use App\Domains\Crawler\DTO\CrawlerTask;
use Throwable;

/**
 * In this example it is too big
 * But generally a lot of things can be different in adapters, so I find this one useful enough
 */
abstract class AbstractAdapter
{
    protected const COUNTER_IMAGES_KEY = 'img';
    protected const COUNTER_INTERNAL_URLS_KEY = 'inl';
    protected const COUNTER_EXTERNAL_URLS_KEY = 'exl';

    protected array $controlSums;

    public abstract function main(CrawlerOutput $crawlerOutput): array;

    protected function resetControlSums(): void
    {
        $this->controlSums = [
            self::COUNTER_IMAGES_KEY => [],
            self::COUNTER_INTERNAL_URLS_KEY => [],
            self::COUNTER_EXTERNAL_URLS_KEY => [],
        ];
    }

    protected function crawlerTaskToArray(CrawlerTask $crawlerTask): array
    {
        return [
            'url' => $crawlerTask->getURL(),
            'http_code' => $crawlerTask->getHttpCode(),
            'page_load_speed' => ceil($crawlerTask->getProcessingTime() * 1000),
            'unique_images' => $crawlerTask->getNumberOfUniqueImages(),
            'unique_internal_links' => $crawlerTask->getNumberOfInternalLinks(),
            'unique_external_links' => $crawlerTask->getNumberOfExternalLinks(),
            'words_count' => $crawlerTask->getWordsCount(),
            'title_length' => strlen($crawlerTask->getTitle()),
        ];
    }

    /**
     * @param Throwable[] $errors
     */
    protected function errors(array $errors): array
    {
        return array_map(
            fn (Throwable $error) => ['message' => $error->getMessage(), 'code' => $error->getCode() ?? 500],
            $errors
        );
    }

    protected function calculateAvgPageLoadSpeed(array &$stats): void
    {
        $wordsCount = collect($stats['per_task'])
            ->reduce(function (int $carry, array $data) {
                $carry += $data['page_load_speed'];

                return $carry;
            }, 0);

        $stats['total']['avg_page_load_speed'] = floor(
            $wordsCount / count($stats['per_task'])
        );
    }

    protected function calculateAvgWordsCount(array &$stats): void
    {
        $wordsCount = collect($stats['per_task'])
            ->reduce(function (int $carry, array $data) {
                $carry += $data['words_count'];

                return $carry;
            }, 0);

        $stats['total']['avg_words_count'] = floor(
            $wordsCount / count($stats['per_task'])
        );
    }

    protected function calculateAvgTitleLength(array &$stats): void
    {
        $wordsCount = collect($stats['per_task'])
            ->reduce(function (int $carry, array $data) {
                $carry += $data['title_length'];

                return $carry;
            }, 0);

        $stats['total']['avg_title_length'] = floor(
            $wordsCount / count($stats['per_task'])
        );
    }

    protected function processCrawlerTask(CrawlerTask $crawlerTask, array &$stats): void
    {
        $this->controlSums[self::COUNTER_IMAGES_KEY] = array_merge(
            $this->controlSums[self::COUNTER_IMAGES_KEY],
            $crawlerTask->getImages()
        );

        $this->controlSums[self::COUNTER_INTERNAL_URLS_KEY] = array_merge(
            $this->controlSums[self::COUNTER_INTERNAL_URLS_KEY],
            $crawlerTask->getInternalURLs()
        );

        $this->controlSums[self::COUNTER_EXTERNAL_URLS_KEY] = array_merge(
            $this->controlSums[self::COUNTER_EXTERNAL_URLS_KEY],
            $crawlerTask->getExternalURLs()
        );

        $stats['per_task'][] = $this->crawlerTaskToArray($crawlerTask);
    }

    protected function calculateStats(array &$stats): void
    {
        $stats['total']['unique_images'] = count(
            array_unique($this->controlSums[self::COUNTER_IMAGES_KEY])
        );

        $stats['total']['unique_internal_urls'] = count(
            array_unique($this->controlSums[self::COUNTER_INTERNAL_URLS_KEY])
        );

        $stats['total']['unique_external_urls'] = count(
            array_unique($this->controlSums[self::COUNTER_EXTERNAL_URLS_KEY])
        );

        $this->calculateAvgPageLoadSpeed($stats);
        $this->calculateAvgWordsCount($stats);
        $this->calculateAvgTitleLength($stats);
    }
}
