<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use Cache;
class ParentCategory extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
    	'identifier',
    	'title',
		'description',
		'seo_title',
		'seo_keywords',
		'seo_descriptions',
		'is_enabled',
		'is_featured',
        'is_demo',
        'is_enabled',
		'icon'
    ];

    protected $appends = [
        'backend_list_url',
        'backend_detail_url',
        'detail_url'
    ]; 


    public function scopeFindByIdentifier($query,$value)
    {
        return $query->where('identifier',$value);
    }

    public function scopeIsFeatured($query)
    {
    	return $query->where('is_featured',1);
    }

    public function getDetailUrlAttribute() {
        return route('frontend.index.parent.category',$this->identifier);
    }

    public function getBackendListUrlAttribute() {
        return route('backend.category.index');
    }

    public function getBackendDetailUrlAttribute() {
        return route('backend.category.detail',[$this->id]);
    }

    public function categories()
    {
        return $this->hasMany('Lib\Entities\Category');
    }

    /**
     * boot
     *
     * @access  protected
    */
    protected static function boot() {
        parent::boot();

        
        static::deleted(function($model) {
            Cache::forget('parent_categories');
            Cache::forget('parent_category_details');
        });

        static::saved(function($model) {
            Cache::forget('parent_categories');
            Cache::forget('parent_category_details');
        });
    }
}