<?php

namespace App\Domains\Crawler\DTO;

use DOMDocument;

/**
 * I like DTOs concept
 *
 * JS & PHP are both using referencing in objects so that we can use DTOs everywhere to achieve magic results
 */
class CrawlerTask
{
    private string $url;
    private DOMDocument $DOMDocument;
    private int $httpCode;

    private array $internalURLs = [];
    private array $externalURLs = [];
    private array $images = [];

    private float $createdAt;
    private float $doneAt;
    private int $wordsCount;
    private string $title;

    public static function make(string $url): CrawlerTask
    {
        return (new CrawlerTask())
            ->setURL($url)
            ->startTask();
    }

    public function startTask(): CrawlerTask
    {
        $this->createdAt = microtime(true);

        return $this;
    }

    public function finishTask(): CrawlerTask
    {
        $this->doneAt = microtime(true);

        return $this;
    }

    public function getProcessingTime(): float
    {
        if (!isset($this->createdAt) || !isset($this->doneAt)) {
            return 0;
        }

        // TO 0.01ms precizing
        return $this->doneAt - $this->createdAt;
    }

    public function getInternalURLs(): array
    {
        return $this->internalURLs;
    }

    public function setInternalURLs(array $urls): CrawlerTask
    {
        $this->internalURLs = $urls;

        return $this;
    }

    public function setExternalURLs(array $urls): CrawlerTask
    {
        $this->externalURLs = $urls;

        return $this;
    }

    public function getExternalURLs(): array
    {
        return $this->externalURLs;
    }

    public function setImages(array $images): CrawlerTask
    {
        $this->images = $images;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }


    public function getURL(): string
    {
        return $this->url;
    }

    public function setURL(string $url): CrawlerTask
    {
        $this->url = $url;

        return $this;
    }

    public function getDOMDocument(): DOMDocument
    {
        return $this->DOMDocument;
    }

    public function setDOMDocument(DOMDocument $DOMDocument): CrawlerTask
    {
        $this->DOMDocument = $DOMDocument;
        return $this;
    }

    public function getNumberOfUniqueImages(): int
    {
        return collect($this->images)
            ->unique()
            ->count();
    }

    public function getNumberOfInternalLinks(): int
    {
        return collect($this->internalURLs)
            ->unique()
            ->count();
    }

    public function getNumberOfExternalLinks(): int
    {
        return collect($this->externalURLs)
            ->unique()
            ->count();
    }

    public function getWordsCount(): int
    {
        return $this->wordsCount;
    }

    public function setWordsCount(int $wordsCount): CrawlerTask
    {
        $this->wordsCount = $wordsCount;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $titleLength): CrawlerTask
    {
        $this->title = $titleLength;
        return $this;
    }

    public function setHttpCode(int $code): CrawlerTask
    {
        $this->httpCode = $code;
        return $this;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
