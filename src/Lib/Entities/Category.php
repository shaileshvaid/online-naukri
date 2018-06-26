<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Cache;

use Lib\Traits\StatisticTraits;

class Category extends Model implements Transformable
{
    use TransformableTrait,StatisticTraits;

    protected $fillable = [
        'parent_category_id',
    	'identifier',
    	'title',
		'description',
		'seo_title',
		'seo_keywords',
		'seo_descriptions',
		'is_enabled',
		'is_featured',
        'is_demo',
        'views',
        'is_enabled',
		'icon'
    ];

    protected $appends = [
        'backend_list_url',
        'backend_detail_url',
        'detail_url'
    ]; 

    public function scopeIsEnabled($query)
    {
        return $query->where('is_enabled',1);
    }

    public function scopeFindByIdentifier($query,$value)
    {
        return $query->where('identifier',$value);
    }

    public function scopeIsFeatured($query)
    {
    	return $query->isEnabled()->where('is_featured',1);
    }

    public function getDetailUrlAttribute() {
        return route('frontend.index.category',$this->identifier);
    }

    public function getBackendListUrlAttribute() {
        return route('backend.sub.category.index',$this->parent_category_id);
    }

    public function getBackendDetailUrlAttribute() {
        return route('backend.sub.category.detail',[$this->parent_category_id,$this->id]);
    }

    public function parentCategory()
    {
        return $this->belongsTo('Lib\Entities\ParentCategory');
    }


    public function apps()
    {
        return $this->morphedByMany('Lib\Entities\AppMarket', 'categoreable');
    }

    /**
     * boot
     *
     * @access  protected
    */
    protected static function boot() {
        parent::boot();

        static::deleted(function($model) {
            Cache::forget('child_categories');
            Cache::forget('is_featured_categories');

            if($model->statistic)
                $model->statistic->delete();
        });

        static::saved(function($model) {
            Cache::forget('is_featured_categories');
        });
    }

}