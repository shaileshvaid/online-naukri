<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use Cache;
use Storage;

class AppMarketVersion extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
        'user_id',
        'app_market_id',
		'app_version',
		'signature',
		'sha_1',
		'description',
		'file_path',
		'size',
		'original_name',
		'app_link',
		'is_link',
		'position'
	
    ];

    protected $appends = [
        'download_link'
    ]; 


    /**
     * getDownloadLinkAttribute
     *
     * @access  public
     */
    public function getDownloadLinkAttribute() {

        if($this->is_link == 1)
            return $this->app_link;
        return $this->linkPath($this->app_version);
    }

    public function app()
    {
        return $this->belongsTo('Lib\Entities\AppMarket');
    }

    /**
     * boot
     *
     * @access  protected
    */
    protected static function boot() {
        parent::boot();

        static::deleting(function($upload) {
            Storage::disk('uploads')->deleteDirectory( $upload->app_market_id.DIRECTORY_SEPARATOR.'apk/'.$upload->app_version );
        });
    }


    private function linkPath($path)
    {
        return url('/uploads/'.$this->app_market_id.'/apk/'.$path.'/'.$this->file_path);
    }
}
