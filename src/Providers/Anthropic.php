<?php

namespace DV\Assistant\Providers;

use DV\Assistant\Concerns\HasMessageResource;
use DV\Assistant\Contracts\HasFunction;
use DV\Assistant\Contracts\Provider;
use DV\Assistant\Providers\Responses\AssistantResponse;
use Illuminate\Support\Facades\Http;

class Anthropic implements HasFunction, Provider
{
    use HasMessageResource;

    private array $config;

    private ?array $tools = [];

    private ?array $functions = [];

    public function __construct()
    {
        $this->config = config('assistant.providers.anthropic');
    }

    public function execute(): AssistantResponse
    {
        $request = $this->request();

        $response = Http::withHeaders([
            'x-api-key' => $this->config['api_key'],
            'content-type' => 'application/json',
            'anthropic-version' => $this->config['version'],
            'anthropic-beta' => 'tools-2024-04-04',
        ])->post($this->config['url'], $request);

        $response = $response->json();

        $response = $this->handleCallbacks($response);

        return $this->parseResponse($response);
    }

    private function request(): array
    {
        $optional = array_filter([
            'tools' => $this->tools,
            'system' => $this->system . PHP_EOL . $this->additionalInstructions,
            'metadata' => $this->metadata,
            'temperature' => $this->temperature,
        ]);

        return [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'messages' => $this->messages,
            ...$optional,
        ];
    }

    public function tools(?array $tools): static
    {
        $this->tools = $tools;

        return $this;
    }

    public function functionCallbacks(string $function, callable $callback): static
    {
        $this->functions[$function] = $callback;

        return $this;
    }

    private function handleCallbacks(array $response): AssistantResponse|array
    {
        $function_executed = false;

        if (!empty($response['stop_reason']) && $response['stop_reason'] === 'tool_use') {

            foreach ($response['content'] as $content) {

                if ($content['type'] === 'tool_use') {

                    if (!isset($this->functions[$content['name']])) {

                        throw new \Exception("Function callback for {$content['name']} not found.");
                    }

                    $this->messages[] = [
                        'role' => 'assistant',
                        'content' => $response['content'],
                    ];

                    $callbackResponse = $this->functions[$content['name']](...($content['input'] ?? []));

                    $this->messages[] = [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'tool_result',
                                'tool_use_id' => $content['id'],
                                'content' => $callbackResponse,
                            ],
                        ],
                    ];

                    $function_executed = true;
                }

            }
        }

        if ($function_executed) {

            return $this->execute();
        }

        return $response;
    }

    private function parseResponse($response): AssistantResponse
    {

        if ($response instanceof AssistantResponse) {
            return $response;
        }

        foreach ($response['content'] as $key => $content) {

            if (preg_match('/<thinking>(?:.|\n)+?<\/thinking>((.|\\n)*)/', $content['text'], $matches)) {
                $response['content'][$key]['text'] = (trim($matches[1]));
            }
        }

        return new AssistantResponse($response['id'], head($response['content'])['text'], $response['usage']['input_tokens'], $response['usage']['output_tokens']);
    }
}
