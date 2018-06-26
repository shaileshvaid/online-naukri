<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Cache;

use Lib\Traits\UploadTraits;
use Lib\Traits\StatisticTraits;
use willvincent\Rateable\Rateable;
use Storage;

class AppMarket extends Model implements Transformable
{
    use TransformableTrait,UploadTraits,StatisticTraits,Rateable;

    protected $fillable = [
        'user_id',
        'app_id',
		'title',
		'description',
		'link',
		'image_url',
        'ratings',
		'ratings_total',
		'developer_name',
        'developer_link',
        'required_android',
		'installs',
        'custom',
		'seo_title',
		'seo_keywords',
		'seo_descriptions',
		'is_enabled',
		'is_featured',
        'is_submitted_app',
        'is_demo',
		'published_date',
    ];


    protected $appends = [
        'backend_list_url',
        'backend_detail_url',
        'detail_url'
    ]; 


    public function scopeByAppId($query,$appId) {
        return $query->where('app_id',$appId);
    }

    public function scopeIsSubmittedApp($query,$isSubmitApp = 1) {
        return $query->where('is_submitted_app',$isSubmitApp);
    }

    public function scopeIsEnabled($query,$enabled = 1) {
        return $query->where('is_enabled',$enabled);
    }

    public function getBackendListUrlAttribute() {
        return route('backend.apps.index');
    }

    public function getBackendDetailUrlAttribute() {
        return route('backend.apps.detail',$this->id);
    }

    public function getDetailUrlAttribute() {
        return route('frontend.index.detail',str_slug($this->title)).'?id='.$this->app_id;
    }

    public function getRatingsAttribute($value) {
        return str_replace(',', '.', $value);
    }

    public function user()
    {
        return $this->belongsTo('Lib\Entities\User');
    }

    public function histogram()
    {
        return $this->hasMany('Lib\Entities\RatingHistogram');
    }

    public function categories()
    {
        return $this->morphToMany('Lib\Entities\Category', 'categoreable');
    }

    public function tags()
    {
        return $this->morphToMany('Lib\Entities\Tag', 'taggable');
    }

    public function versions()
    {
        return $this->hasMany('Lib\Entities\AppMarketVersion')->orderBy('app_version','desc');
    }

    public function reviews()
    {
        return $this->hasMany('Lib\Entities\AppMarketReview');
    }

    /**
     * boot
     *
     * @access  protected
    */
    protected static function boot() {
        parent::boot();


        static::deleting(function($model) {
            $model->versions->each(function($m){
                return $m->delete();
            });

            $model->histogram->each(function($m){
                return $m->delete();
            });
        });

        static::deleted(function($model) {
            Cache::flush();

            if ($model->categories) {
                $model->categories()->detach();
            }

            if ($model->tags) {
                $model->tags()->detach();
            }

            if(!$model->screenshots->isEmpty())
            {
                foreach ($model->screenshots as $key => $upload) {
                    Storage::disk('uploads')->deleteDirectory( $model->id.DIRECTORY_SEPARATOR.UPLOAD_SCREENSHOT );
                    $upload->delete();
                }
            }

            if($model->appImage)
            {
                Storage::disk('uploads')->deleteDirectory( $model->id.DIRECTORY_SEPARATOR.UPLOAD_APPIMAGE);
                $model->appImage->delete();
            }

            Storage::disk('uploads')->deleteDirectory( $model->id);

            if($model->statistic)
                $model->statistic->delete();

            if($model->statistic)
                $model->statistic->delete();
        });

        static::saved(function($model) {
            Cache::flush();
        });
    }
}
