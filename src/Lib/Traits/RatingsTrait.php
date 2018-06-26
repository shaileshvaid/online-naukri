<?php

namespace Lib\Traits;

/**
 * RatingsTrait Trait Class
 *
 * @category  PHP
 * @package   Traits
 * @author    Anthony Pillos <dev.anthonypillos@gmail.com>
 * @license   commercial http://anthonypillos.com
 * @link      http://anthonypillos.com
 * @copyright Copyright (c) 2017 Anthony Pillos.
 * @version   v1
 */

use Sentinel;

trait RatingsTrait
{

    public function rateApp($model,$rateNumber = 5,$userId = null)
    {

    	if(!$userId)
    	{
    		$user = Sentinel::getUser();
    		$userId = $user->id;
    	}

        $rating = new \willvincent\Rateable\Rating;
		$rating->rating = $rateNumber;
		$rating->user_id = $userId;

		return $model->ratings()->save($rating);
    }

}



