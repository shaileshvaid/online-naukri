<?php 

namespace Lib\Support;

/**
 * Lib\Support\Count
 * Countable php 7.2.
 *
 * @package Lib\Support\Count
 * @author  Anthony Pillos <dev.anthonypillos@gmail.com>
 * @version v1
**/
use Countable;

class Counter implements Countable {

	protected $count = 0;

    public function count() 
    { 
        return $this->count; 
    } 
}

