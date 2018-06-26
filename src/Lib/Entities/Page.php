<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use Cache;
class Page extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
    	'user_id',
    	'status_id',
    	'parent_page_id',
    	'slug',
    	'title',
    	'content',
        'seo_title',
        'seo_keywords',
        'seo_descriptions',
        'position',
    	'is_enabled',
    ];

    protected $appends = [
        'backend_list_url',
        'backend_detail_url',
        'detail_url'
    ];

    public function getDetailUrlAttribute() {
        return route('frontend.index.page',$this->slug);
    }

    public function getBackendListUrlAttribute() {
        return route('backend.pages.index');
    }

    public function getBackendDetailUrlAttribute() {
        return route('backend.pages.detail',[$this->id]);
    }

    public function status()
    {
        return $this->hasOne('Lib\Entities\Status','id','status_id');
    }

    public function data()
    {
        return [
            'id'                 => $this->id,
            'slug'               => $this->slug,
            'title'              => $this->title,
            'content'            => $this->content,
            'status_identifier'  => $this->status->identifier,
            'status_name'        => $this->status->name,
            'backend_detail_url' => $this->backend_detail_url,
            'detail_url'         => $this->detail_url
        ];
    }

    public function scopeFindBySlug($query,$slug)
    {
        return $query->where('slug',$slug);
    }
    
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
