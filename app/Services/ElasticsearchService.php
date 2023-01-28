<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class ElasticsearchService
{
    /**
     * @param string $index
     * @param array $body
     * @return bool
     * @throws Exception
     */
    public function createIndex(string $index, array $body): bool
    {
        $response = Http::put($this->concatIndexToUrl($index), $body);

        $this->throwIfResponseIsNotOk($response);

        return true;
    }

    /**
     * @param string $index
     * @param int $id
     * @param array $body
     * @return bool
     * @throws Exception
     */
    public function indexDocument(string $index, int $id, array $body): bool
    {
        $response = Http::post($this->concatIndexToUrl($index) . '/_doc/' . $id, $body);

        $this->throwIfResponseIsNotOk($response);

        return true;
    }

    /**
     * @param string $index
     * @return bool
     * @throws Exception
     */
    public function deleteIndex(string $index): bool
    {
        $response = Http::delete($this->concatIndexToUrl($index));

        $this->throwIfResponseIsNotOk($response);

        return true;
    }

    /**
     * @param string $index
     * @return bool
     */
    public function doesIndexExist(string $index): bool
    {
        return Http::head($this->concatIndexToUrl($index))->status() === 200;
    }

    /**
     * @param string $index
     * @return string
     */
    private function concatIndexToUrl(string $index): string
    {
        return config('elasticsearch.url') . '/' . $index;
    }

    /**
     * @throws Exception
     */
    private function throwIfResponseIsNotOk(Response $response): void
    {
        if ($response->status() !== 200 && $response->status() !== 201) {
            throw new Exception($response->body());
        }
    }
}
