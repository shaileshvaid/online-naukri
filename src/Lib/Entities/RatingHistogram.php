<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class RatingHistogram extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [

    	'app_market_id',
		'num',
		'bar_length',
		'bar_number',
    ];

}
