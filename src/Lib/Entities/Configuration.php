<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Lib\Traits\UploadTraits;
use Cache;

class Configuration extends Model implements Transformable
{
    use TransformableTrait,UploadTraits;

    protected $fillable = [
        'group_slug',
        'key',
        'value',
        'description'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'name'
    ];

    public function scopeFindByKey($query,$value)
    {
        return $query->where('key',$value);
    }


    public function getNameAttribute()
    {
        return ucwords( str_replace('_', ' ', $this->key));
    }


    public function data()
    {
        return [
            'group_name'    => ucwords(str_replace('_', ' ',$this->group_slug)),
            'group_slug'    => $this->group_slug,
            'key'           => $this->key,
            'value'         => $this->value,
            'name'          => $this->name,
            'description'   => $this->description,
            'image_logo'    => $this->upload['image_link']
        ];
    }

    /**
     * boot
     *
     * @access  protected
    */
    protected static function boot() {
        parent::boot();

        static::deleted(function($config) {
            Cache::forget('site_configuration');
        });

        static::saved(function($config) {
            Cache::forget('site_configuration');
        });
    }

}