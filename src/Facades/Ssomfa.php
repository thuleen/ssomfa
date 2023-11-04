<?php

namespace Thuleen\Ssomfa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Thuleen\Ssomfa\Ssomfa
 */
class Ssomfa extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Thuleen\Ssomfa\Ssomfa::class;
    }
}
