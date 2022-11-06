<?php

namespace App\Domains\Crawler\Transformers\Adapters;

use App\Domains\Crawler\DTO\CrawlerOutput;

class CLIAdapter extends AbstractAdapter
{
    public function main(CrawlerOutput $crawlerOutput): array
    {
        // Reset counters
        $this->resetControlSums();

        $stats = [
            'total' => [
                'pages' => count($crawlerOutput->getResults()),
                'unique_images' => 0,
                'unique_internal_urls' => 0,
                'unique_external_urls' => 0,
                'avg_page_load_speed' => 0,
                'avg_words_count' => 0,
                'avg_title_length' => 0,
            ],
            'per_task' => [],
            'errors' => $this->errors(
                $crawlerOutput->getErrors()
            ),
        ];

        foreach ($crawlerOutput->getResults() as $crawlerTask) {
            $this->processCrawlerTask($crawlerTask, $stats);
        }

        $this->calculateStats($stats);

        // Release memory from garbage
        $this->resetControlSums();

        return $stats;
    }
}
