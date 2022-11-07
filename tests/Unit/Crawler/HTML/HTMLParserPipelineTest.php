<?php

namespace Tests\Unit\Crawler\HTML;

use App\Domains\Crawler\DTO\CrawlerTask;
use App\Domains\Crawler\HTML\HTMLParserException;
use App\Domains\Crawler\HTML\HTMLParserPipeline;
use Tests\TestCase;
use DOMDocument;

class HTMLParserPipelineTest extends TestCase
{
    /**
     * @test
     */
    public function canParseSuccessfully(): void
    {
        /**
         * @var $parser HTMLParserPipeline
         */
        $parser = app(HTMLParserPipeline::class);

        $task = $this->getSuccessTask();
        $parser->parse($task);

        $this->assertSame(
            strlen('Foo bar'),
            strlen($task->getTitle())
        );

        $this->assertSame(
            3,
            $task->getNumberOfExternalLinks()
        );

        $this->assertSame(
            2,
            $task->getNumberOfInternalLinks()
        );

        $this->assertSame(
            2,
            $task->getNumberOfUniqueImages()
        );

        $this->assertSame(
            6,
            $task->getWordsCount()
        );
    }

    /**
     * @test
     */
    public function canHandleErrors(): void
    {
        $this->expectException(HTMLParserException::class);
        $this->expectExceptionCode(417);
        $this->expectExceptionMessage('Failed to parse the HTML');

        /**
         * @var $parser HTMLParserPipeline
         */
        $parser = app(HTMLParserPipeline::class);

        $parser->parse(
            $this->getFailureTask()
        );
    }

    private function getSuccessTask(): CrawlerTask
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML(
            <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Foo bar</title>
</head>
<body>

<img src="/src1">
<img src="/src1">
<img data-src="/src2">
<img data-src="/src2">

<a href="/foo"></a>
<a href="#foo"></a>
<a href="/bar"></a>
<a href="https://notafoo.bar/foo"></a>
<a href="https://notafoo.bar#foo"></a>
<a href="https://notafoo.bar/bar"></a>

<p>Words goes here</p>
<strong>And some here</strong>

</body>
</html>
HTML
        );

        return CrawlerTask::make('https://foo.bar')->setDOMDocument($dom);
    }

    private function getFailureTask(): CrawlerTask
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML(
            <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>

<img src="/src1">
<img src="/src1">
<img data-src="/src2">
<img data-src="/src2">

<a href="/foo"></a>
<a href="#foo"></a>
<a href="/bar"></a>
<a href="https://notafoo.bar/foo"></a>
<a href="https://notafoo.bar#foo"></a>
<a href="https://notafoo.bar/bar"></a>

<p>Words goes here</p>
<strong>And some here</strong>

</body>
</html>
HTML
        );

        return CrawlerTask::make('https://foo.bar')->setDOMDocument($dom);
    }
}
