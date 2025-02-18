<?php

namespace Jaulz\LaravelFactory\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public function employees()
    {
        return $this->belongsToMany(User::class, 'employees');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
