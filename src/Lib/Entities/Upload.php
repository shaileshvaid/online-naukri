<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Storage;
class Upload extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
        'user_id',
        'position',
        'file_path',
        'size',
        'original_name',
        'image_url',
        'is_link',
        'upload_type'
    ];
    protected $appends = [
        'image_link'
    ]; 

    /**
     * getImageLinkAttribute
     *
     * @access  public
     */
    public function getImageLinkAttribute() {

        if($this->upload_type == UPLOAD_FILE)
            $image =  $this->linkPath('screenshots');
        elseif($this->upload_type == UPLOAD_APPIMAGE)
            $image =  $this->linkPath(UPLOAD_APPIMAGE);
        elseif($this->upload_type == UPLOAD_LINK)
            $image =  $this->image_url;
        elseif($this->upload_type == UPLOAD_LOGO)
            $image = url('/uploads/'.UPLOAD_LOGO.'/'.$this->file_path);
        
        return $image;

    }

    public function uploadable()
    {
        return $this->morphTo();
    }

    /**
     * boot
     *
     * @access  protected
    */
    protected static function boot() {
        parent::boot();

        static::deleting(function($upload) {
            if($upload->file_path && $upload->upload_type == 'file')
                Storage::disk('uploads')->delete( $upload->uploadable_id.'/screenshots/'.$upload->file_path );

            if($upload->file_path && $upload->upload_type == 'app-image')
                Storage::disk('uploads')->delete( $upload->uploadable_id.'/app-image/'.$upload->file_path );

            if($upload->file_path && $upload->upload_type == UPLOAD_LOGO)
                Storage::disk('uploads')->delete( UPLOAD_LOGO.'/'.$upload->file_path );
        });

    }

    private function linkPath($path)
    {
        return url('/uploads/'.$this->uploadable_id.'/'.$path.'/'.$this->file_path);
    }

}
