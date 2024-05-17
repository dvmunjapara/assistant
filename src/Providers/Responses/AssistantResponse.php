<?php

namespace DV\Assistant\Providers\Responses;

class AssistantResponse
{
    public function __construct(public string $id, public string $message, public string $input_tokens, public string $output_tokens)
    {
        //
    }
}
