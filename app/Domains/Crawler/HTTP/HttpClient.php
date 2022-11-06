<?php

namespace App\Domains\Crawler\HTTP;

use App\Domains\Crawler\DTO\CrawlerTask;
use DOMDocument;
use \Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;
use \Illuminate\Http\Response as ResponseCode;

class HttpClient
{
    private Response $response;

    /**
     * Accepts and edits DTO
     */
    public function loadDOMDocument(CrawlerTask $task): void
    {
        try {
            /**
             * When Http client was introduced - it was a breath of a fresh air for me.
             * The main feature for me is that it is mockable which I enjoy a lot
             */
            $this->response = Http::get($task->getURL());
        } catch (Throwable $e) {
            throw new HttpClientException(
                "GET request failed: `{$task->getURL()}`",
                ResponseCode::HTTP_BAD_REQUEST,
                $e
            );
        }

        $dom = new DOMDocument('1.0', 'UTF-8');

        $internalErrors = libxml_use_internal_errors(true);

        $res = $dom->loadHTML(
            $this->response->body()
        );

        libxml_use_internal_errors($internalErrors);

        if ($res === false) {
            throw new HttpClientException(
                "Load HTML failed: `{$task->getURL()}`",
                ResponseCode::HTTP_BAD_REQUEST
            );
        }

        $task->setDOMDocument($dom);
        $task->setHttpCode($this->response->status());
    }
}
