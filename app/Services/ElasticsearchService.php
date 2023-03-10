<?php

namespace App\Services;

use Exception;
use Illuminate\Foundation\Auth\User;
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
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function deleteDocument(string $index, int $id): bool
    {
        $response = Http::delete($this->concatIndexToUrl($index) . '/_doc/' . $id);

        $this->throwIfResponseIsNotOk($response);

        return true;
    }

    /**
     * @param string $index
     * @param int $id
     * @param array $body
     * @return true
     * @throws Exception
     */
    public function updateDocument(string $index, int $id, array $body): bool
    {
        $response = Http::post($this->concatIndexToUrl($index) . '/_update/' . $id,  $body);

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
     * @param array $body
     * @return array
     * @throws Exception
     */
    public function search(string $index, array $body): array
    {
        $response = Http::post($this->concatIndexToUrl($index) . '/_search' , $body);

        $this->throwIfResponseIsNotOk($response);

        return $response->json('hits.hits');
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
