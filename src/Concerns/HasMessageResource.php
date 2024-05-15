<?php

namespace DV\Assistant\Concerns;

use DV\Assistant\Exceptions\InvalidArgumentException;

trait HasMessageResource
{
    private string $model;

    private int $maxTokens = 1000;

    private array $messages;

    private ?string $system = null;

    private ?string $additionalInstructions = null;

    private ?array $metadata = null;

    private ?float $temperature = null;

    private ?int $topK = null;

    private ?int $topP = null;

    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function maxTokens(int $maxTokens): static
    {
        if ($maxTokens <= 0) {
            throw new InvalidArgumentException('Max tokens must be a positive integer.');
        }

        $this->maxTokens = $maxTokens;

        return $this;
    }

    public function messages(array $messages): static
    {
        foreach ($messages as $message) {

            if (!isset($message['role']) || !isset($message['content'])) {
                throw new InvalidArgumentException('Each message must have a "role" and "content" key.');
            }

            if (!is_string($message['role']) || !is_string($message['content'])) {
                throw new InvalidArgumentException('Message "role" and "content" must be strings.');
            }
        }

        $this->messages = $messages;

        return $this;
    }

    public function system(?string $system): static
    {
        $this->system = $system;

        return $this;
    }

    public function additionalInstructions(?string $instructions): static
    {
        $this->additionalInstructions = $instructions;

        return $this;
    }

    public function metadata(?array $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function temperature(?float $temperature): static
    {
        if ($temperature < 0.0 || $temperature > 1.0) {
            throw new InvalidArgumentException('Temperature must be between 0.0 and 1.0.');
        }

        $this->temperature = $temperature;

        return $this;
    }

    public function topK(?int $topK): static
    {
        if ($topK < 0.0 || $topK > 2.0) {
            throw new InvalidArgumentException('TopP must be between 0.0 and 2.0.');
        }

        $this->topK = $topK;

        return $this;
    }

    public function topP(?int $topP): static
    {

        if ($topP < 0.0 || $topP > 2.0) {
            throw new InvalidArgumentException('TopP must be between 0.0 and 2.0.');
        }

        $this->topP = $topP;

        return $this;
    }
}
