<?php

namespace DV\Assistant\Providers;

use DV\Assistant\Contracts\HasFunction;
use DV\Assistant\Facades\Assistant;
use DV\Assistant\Concerns\HasMessageResource;
use DV\Assistant\Contracts\MessageResource;
use DV\Assistant\Contracts\Provider;
use Illuminate\Support\Facades\Http;

class TuneAI implements Provider, MessageResource, HasFunction
{
    use HasMessageResource;

    private array $config;

    public function __construct()
    {
        $this->config = config('assistant.providers.tuneai');
    }

    public function execute()
    {
        $request = $this->request();

//        dd($request);
        $response = Http::withHeaders([
            'Authorization' => $this->config['api_key'],
            'X-Org-Id' => $this->config['org_id'],
            'content-type' => 'application/json',
        ])->post($this->config['url'], $request);

        $response = $response->json();


        if ($this instanceof HasFunction) {

            $response = $this->handleCallbacks($response);
        }

        return $response;
    }

    private function request(): array
    {
        $optional = array_filter([
            'metadata' => $this->metadata,
            'temperature' => $this->temperature,
            'tools' => $this->tools,
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

    private function handleCallbacks(array $response)
    {
        $function_executed = false;

        if (!empty($response['stop_reason']) && $response['stop_reason'] === 'tool_use') {

            foreach($response['content'] as $content) {

                if ($content['type'] === 'tool_use') {

                    if (!$this->functions[$content['name']]) {

                        throw new \Exception("Function callback for {$content['name']} not found.");
                    }

                    $this->messages[] = [
                        'role' => 'assistant',
                        'content' => $response['content']
                    ];


                    $callbackResponse = $this->functions[$content['name']](...($content['input']??[]));

                    $this->messages[] = [
                        'role' => 'user',
                        'content' => [
                            [
                                "type" => "tool_result",
                                "tool_use_id" => $content['id'],
                                "content" => $callbackResponse
                            ]
                        ]
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

    private function parseResponse($response)
    {

        if ($response['type'] === 'error') {
            dd($this->messages, $response);
        }

        foreach ($response['content'] as $key => $content) {

            if (preg_match('/<thinking>(?:.|\n)+?<\/thinking>((.|\\n)*)/', $content['text'], $matches)) {
                $response['content'][$key]['text'] = (trim($matches[1]));
            }
        }


        return $response;
    }
}