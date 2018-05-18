<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    /*
     * All fields are available for mass assignment.
     */
    protected $guarded = [];

    const PENDING = 0;
    const ACCEPTED = 1;
    const DENIED = 2;
    const BLOCKED = 3;
}
