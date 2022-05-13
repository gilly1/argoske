<?php

namespace App\Repositories;

use Cache;

class MainRepository
{

    private $cacheKey,$model;

    function __construct($cacheKey,$model)
    {
        $this->model = $model;
        $this->cacheKey = $cacheKey;
    }

    public function getCacheKey()
    {
        return  $this->cacheKey;
    }

    public function reCache()
    {
        $model = $this->model;
        Cache::forget($this->getCacheKey());
        Cache::rememberForever($this->getCacheKey(), function () use ($model) {
            return $model;
        });
    }

    public function checkCache()
    {
        if (! Cache::has($this->getCacheKey())) {
            $this->reCache();
        }
    }

    public function all()
    {
        $this->checkCache();
        
        return Cache::get($this->getCacheKey());
    }

    public function where()
    {
        $this->checkCache();
        
        return Cache::where()->get($this->getCacheKey());
    }
}
