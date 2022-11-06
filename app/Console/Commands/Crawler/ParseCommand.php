<?php

namespace App\Console\Commands\Crawler;

use App\Domains\Crawler\CrawlerService;
use App\Domains\Crawler\Transformers\CrawlerResultPresenter;
use Illuminate\Console\Command;

class ParseCommand extends Command
{
    protected $signature = 'crawler:parse {target}';
    protected $description = 'CLI representation of the code';

    /**
     * Interesting fact about Laravel.
     * Sometimes Laravel is designed to inject things in both __constructor & handle() methods.
     * CLI is very interesting as if you inject into __contructor - the dependency will be resolved on each
     * call to the server. And it does not depend on the web/cli load. Just will be loaded as resolved dependency :)
     */
    public function handle(CrawlerService $crawlerService): int
    {
        $this->info('Init crawler parsing');

        $data = CrawlerResultPresenter::cli()->main(
            $crawlerService->main(
                $this->argument('target')
            )
        );

        $this->info('Parsing complete, presenting results...');

        // Total Stats
        $this->info('Total stats');
        $this->table([
            'Pages crawled',
            'Unique images',
            'Unique internal links',
            'Unique external links',
            'Avg page load in seconds',
            'Avg word count',
            'Avg title length',
        ], [
            [
                $data['total']['pages'],
                $data['total']['unique_images'],
                $data['total']['unique_internal_urls'],
                $data['total']['unique_external_urls'],
                $data['total']['avg_page_load_speed'] . ' ms',
                $data['total']['avg_words_count'],
                $data['total']['avg_title_length'],
            ]
        ]);

        // Per Task Stats
        $this->info('Per task stats:');
        $iteration = 0;
        $this->table(
            [
                '#',
                'URL',
                'HTTP Code',
                'Page Load Speed',
                'Unique Images',
                'Unique Internal Links',
                'Unique External Links',
                'Words count',
                'Title Length',
            ],
            array_map(function (array $task) use (&$iteration) {
                $iteration++;

                return [
                    $iteration,
                    $task['url'],
                    $task['http_code'],
                    $task['page_load_speed'] . ' ms',
                    $task['unique_images'],
                    $task['unique_internal_links'],
                    $task['unique_external_links'],
                    $task['words_count'],
                    $task['title_length'],
                ];
            }, $data['per_task'])
        );

        return Command::SUCCESS;
    }
}
