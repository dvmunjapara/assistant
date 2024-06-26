<?php

namespace DV\Assistant\Contracts;

use DV\Assistant\Providers\Responses\AssistantResponse;

interface Provider
{
    public function execute(): AssistantResponse;

    public function model(string $model): static;

    public function maxTokens(int $maxTokens): static;

    public function messages(array $messages): static;

    public function system(?string $system): static;

    public function additionalInstructions(?string $instructions): static;

    public function metadata(?array $metadata): static;

    public function temperature(?float $temperature): static;

    public function topK(?int $topK): static;

    public function topP(?int $topP): static;
}
