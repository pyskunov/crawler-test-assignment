<?php

namespace App\Domains\Crawler\DTO;

use Throwable;

/**
 * I like objects
 * I don't like PHP has no generics as I could create less objects :)
 */
class CrawlerOutput
{
    private array $results;
    private array $errors;

    /**
     * @param CrawlerTask[] $results
     * @param Throwable[] $errors
     */
    public static function make(
        array $results,
        array $errors
    ): CrawlerOutput {
        return (new CrawlerOutput())
            ->setResults($results)
            ->setErrors($errors);
    }

    public function setResults(array $results): CrawlerOutput
    {
        $this->results = $results;
        return $this;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function setErrors(array $errors): CrawlerOutput
    {
        $this->errors = $errors;
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
