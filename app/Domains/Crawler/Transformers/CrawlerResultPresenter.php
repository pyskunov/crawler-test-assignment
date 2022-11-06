<?php

namespace App\Domains\Crawler\Transformers;

use App\Domains\Crawler\Transformers\Adapters\BladeAdapter;
use App\Domains\Crawler\Transformers\Adapters\CLIAdapter;

class CrawlerResultPresenter
{
    /**
     * When we want to transform data for blade
     */
    public static function blade(): BladeAdapter
    {
        return new BladeAdapter();
    }

    /**
     * When we want to transform data for CLI
     */
    public static function cli(): CLIAdapter
    {
        return new CLIAdapter();
    }
}
