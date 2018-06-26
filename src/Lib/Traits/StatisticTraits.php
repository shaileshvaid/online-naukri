<?php

namespace Lib\Traits;

/**
 * StatisticTraits Trait Class
 *
 * @category  PHP
 * @package   Traits
 * @author    Anthony Pillos <dev.anthonypillos@gmail.com>
 * @license   commercial http://anthonypillos.com
 * @link      http://anthonypillos.com
 * @copyright Copyright (c) 2017 Anthony Pillos.
 * @version   v1
 */

trait StatisticTraits
{

    public function statistic()
    {
        return $this->morphOne('Lib\Entities\Statistic', 'statisticable');
    }

     public function statistics()
    {
        return $this->morphMany('Lib\Entities\Statistic', 'statisticable');
    }
}