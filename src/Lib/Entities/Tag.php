<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Tag extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['identifier','name','position','is_demo'];

    protected $appends = [
        'text'
    ];

    public function getTextAttribute($value) {
        return $this->name;
    }


    public function scopeFindByIdentifier($query,$identifier)
    {
        return $query->where('identifier',$identifier);
    }

    public function apps()
    {
        return $this->morphedByMany('Lib\Entities\AppMarket', 'taggable');
    }

    public function data()
    {
        return [
            'id'            => $this->id,
            'identifier'    => $this->identifier,
            'name'          => $this->name,
            'text'          => $this->name
        ];
    }

    /**
     * boot
     *
     * @access  protected
    */
    protected static function boot() {
        parent::boot();

        static::deleting(function($tag) {
            if($tag->taggable)
            {
                $tag->taggable->each(function($m){
                    return $m->delete();
                });
            }
        });

    }

}
