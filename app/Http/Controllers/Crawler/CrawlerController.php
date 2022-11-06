<?php

namespace App\Http\Controllers\Crawler;

use App\Domains\Crawler\CrawlerService;
use App\Domains\Crawler\Transformers\CrawlerResultPresenter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crawler\InitRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('v1')]
class CrawlerController extends Controller
{
    /**
     * I like the concept of thin controller & composer service
     */
    public function __construct(
        private CrawlerService $crawlerService
    )
    {
    }

    /**
     * I like Spatie very much
     * The miracle of 8.1 is attributes.
     * I very like the fact we can do the same as Symfony doing so long
     * NestJS is doing this
     * Spring boot is doing this
     * .Net is doing this
     *
     * GOD BLESS SPATIE :)
     */
    #[Post(uri: 'crawler', name: 'crawler')]
    public function crawler(InitRequest $request): View | RedirectResponse
    {
        // Let's expect view will be returned to display our results
        return view(
            'domains.crawler.results',
            [
                'data' => CrawlerResultPresenter::blade()->main(
                    $this->crawlerService->main(
                        $request->validated('target')
                    )
                ),
            ]
        );
    }
}
