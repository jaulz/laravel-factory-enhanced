<?php

namespace Jaulz\LaravelFactory\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public function imageable()
    {
        return $this->morphTo();
    }
}
