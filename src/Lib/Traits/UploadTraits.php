<?php

namespace Lib\Traits;

/**
 * UploadTraits Trait Class
 *
 * PHP version 5.4
 *
 * @category  PHP
 * @package   Traits
 * @author    Anthony Pillos <dev.anthonypillos@gmail.com>
 * @license   commercial http://anthonypillos.com
 * @link      http://anthonypillos.com
 * @copyright Copyright (c) 2017 Anthony Pillos.
 * @version   v1
 */

trait UploadTraits
{

    public function appImage()
    {
        return $this->morphOne('Lib\Entities\Upload', 'uploadable')->where('upload_type',UPLOAD_APPIMAGE);
    }

    public function screenshots()
    {
        return $this->morphMany('Lib\Entities\Upload', 'uploadable')->where('upload_type','!=',UPLOAD_APPIMAGE);
    }

    public function uploads()
    {
        return $this->morphMany('Lib\Entities\Upload', 'uploadable');
    }

    public function upload()
    {
        return $this->morphOne('Lib\Entities\Upload', 'uploadable');
    }
}