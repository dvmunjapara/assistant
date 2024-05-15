<?php

namespace DV\Assistant;

use DV\Assistant\Contracts\Provider;
use DV\Assistant\Exceptions\InvalidArgumentException;

class Assistant
{
    private Provider $provider;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $this->provider = $this->resolveProvider(config('assistant.default_provider'));
    }

    public function __call($method, $parameters)
    {
        return $this->provider->$method(...$parameters);
    }

    public function using($provider)
    {
        $this->provider = $this->resolveProvider($provider);

        return $this;
    }

    private function resolveProvider($provider)
    {
        if (! app()->has($provider)) {
            throw new InvalidArgumentException("Provider {$provider} not found");
        }

        return app()->get($provider);
    }
}
