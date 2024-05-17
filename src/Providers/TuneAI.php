<?php

namespace DV\Assistant\Providers;

use DV\Assistant\Concerns\HasMessageResource;
use DV\Assistant\Contracts\HasFunction;
use DV\Assistant\Contracts\Provider;
use DV\Assistant\Providers\Responses\AssistantResponse;
use Illuminate\Support\Facades\Http;

class TuneAI implements Provider
{
    use HasMessageResource;

    private array $config;

    public function __construct()
    {
        $this->config = config('assistant.providers.tuneai');
    }

    public function execute(): AssistantResponse
    {
        $request = $this->request();

        $response = Http::withHeaders([
            'Authorization' => $this->config['api_key'],
            'X-Org-Id' => $this->config['org_id'],
            'content-type' => 'application/json',
        ])->post($this->config['url'], $request);

        $response = $response->json();

        return new AssistantResponse($response['id'], head($response['choices'])['message']['content'], $response['usage']['prompt_tokens'], $response['usage']['completion_tokens']);
    }

    private function request(): array
    {
        $optional = array_filter([
            'metadata' => $this->metadata,
            'temperature' => $this->temperature,
        ]);

        $messages = $this->messages;

        if ($this->system) {
            array_unshift($messages, ['role' => 'system', 'content' => $this->system]);
        }

        return [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'messages' => $messages,
            ...$optional,
        ];
    }
}
