<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Statistic extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
        'views'
    ];

    /**
     * Get all of the owning statisticable models.
     */
    public function statisticable()
    {
        return $this->morphTo();
    }

    /**
     * Get all categories collections
     */
    public function scopeCategories()
    {
        return $this->with('category')->where('statisticable_type','Lib\Entities\Category')->orderBy('views','desc');
    }


    /**
     * Get all categories collections
     */
    public function scopeApps()
    {
        return $this->with('app.appImage')->where('statisticable_type','Lib\Entities\AppMarket')->orderBy('views','desc');
    }


    /**
     * Get category detail
     */
    public function category()
    {
        return $this->hasOne('Lib\Entities\Category','id','statisticable_id');
    }

    /**
     * Get app detail
     */
    public function app()
    {
        return $this->hasOne('Lib\Entities\AppMarket','id','statisticable_id')->where('is_enabled',1);
    }
}
