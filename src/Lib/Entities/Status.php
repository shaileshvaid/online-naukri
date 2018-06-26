<?php

namespace Lib\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Status extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
    	'identifier',
    	'name',
    	'type',
    ];

	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'statuses';


    /**
     * Get model id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function pages()
    {
        return $this->hasMany('Lib\Entities\Page');
    }

}
