<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use Cache;
class SearchLog extends Model implements Transformable
{
    use TransformableTrait;
    
    protected $fillable = [
        'search_keyword'
    ];
    
    /**
     * boot
     *
     * @access  protected
    */
    protected static function boot() {
        parent::boot();

        static::deleted(function($config) {
            Cache::flush();
        });

        static::saved(function($config) {
            Cache::flush();
        });
    }
    
}
