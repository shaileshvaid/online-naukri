<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Categoreable extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];


    public function parentCategory()
    {
        return $this->hasOne('Lib\Entities\ParentCategory');
    }
}
