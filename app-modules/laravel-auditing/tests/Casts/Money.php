<?php

namespace Assist\LaravelAuditing\Tests\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Assist\LaravelAuditing\Tests\Models\Money as MoneyValueObject;

class Money implements CastsAttributes
{
    /**
     * {@inheritdoc}
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return new MoneyValueObject($value, 'USD');
    }

    /**
     * {@inheritdoc}
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}
