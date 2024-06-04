<?php

namespace DV\Assistant\Facades;

use DV\Assistant\Contracts\Provider;
use DV\Assistant\Providers\Responses\AssistantResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @see \DV\Assistant\Assistant
 *
 * @method static AssistantResponse execute()
 * @method static Provider using($service)
 * @method static Provider messages($message)
 * @method static Provider system($system)
 * @method static Provider model($model)
 * @method static Provider metadata($metadata)
 * @method static Provider temperature($temperature)
 * @method static Provider tools($tools)
 * @method static Provider topK($topK)
 * @method static Provider topP($topP)
 * @method static Provider functionCallbacks(string $function, callable $callback)
 * @method static Provider additionalInstructions(string $instructions)
 */
class Assistant extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'completion_assistant';
    }
}
