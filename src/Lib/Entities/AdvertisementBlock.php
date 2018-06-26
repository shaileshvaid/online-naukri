<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Cache;
class AdvertisementBlock extends Model implements Transformable
{
    use TransformableTrait;


    protected $fillable = [
        'identifier',
        'title',
        'code',
        'is_demo'
    ];

    public function adsPlacement()
    {
        return $this->hasMany('Lib\Entities\AdvertisementPlacement');
    }

    public function data()
    {
        return [
            'id'         => $this->id,
            'identifier' => $this->identifier,
            'title'      => $this->title,
            'code'       => $this->code
        ];
    }

    /**
     * boot
     *
     * @access  protected
    */
    protected static function boot() {
        parent::boot();

        static::deleted(function($model) {
            Cache::flush();

        });

        static::saved(function($model) {
            Cache::flush();
        });
    }
}
