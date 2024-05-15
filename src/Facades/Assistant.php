<?php

namespace DV\Assistant\Facades;

use DV\Assistant\Contracts\MessageResource;
use DV\Assistant\Contracts\Provider;
use Illuminate\Support\Facades\Facade;

/**
 * @see \DV\Assistant\Assistant
 *
 * @method static Provider execute($provider)
 * @method static MessageResource message($message)
 * @method static MessageResource system($system)
 * @method static MessageResource model($model)
 * @method static MessageResource metadata($metadata)
 * @method static MessageResource temperature($temperature)
 * @method static MessageResource tools($tools)
 * @method static MessageResource topK($topK)
 * @method static MessageResource topP($topP)
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
        return 'assistant';
    }
}
