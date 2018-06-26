<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class AppMarketReview extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [

    	'user_id',
		'app_market_id',
		'author_name',
		'published_at',
		'comments',
		'image_url',
		'is_google_play'

    ];

}
