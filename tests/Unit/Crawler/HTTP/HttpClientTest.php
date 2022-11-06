<?php

namespace Tests\Unit\Crawler\HTTP;

use App\Domains\Crawler\DTO\CrawlerTask;
use App\Domains\Crawler\HTTP\HttpClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use DOMDocument;

class HttpClientTest extends TestCase
{
    /**
     * @test
     */
    public function canGetSuccessfully(): void
    {
        Http::fake([
            'https://foo.bar' => Http::response($this->getHTML()),
        ]);

        $httpClient = new HttpClient();

        $task = CrawlerTask::make('https://foo.bar');
        $httpClient->loadDOMDocument($task);

        $this->assertInstanceOf(
            DOMDocument::class,
            $task->getDOMDocument()
        );

        $this->assertSame(
            'https://foo.bar',
            $task->getURL()
        );
    }

    private function getHTML(): string
    {
        return
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
HTML;
    }
}
