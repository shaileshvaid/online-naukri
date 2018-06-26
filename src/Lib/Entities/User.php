<?php

namespace Lib\Entities;

/**
 * Lib\Entities\User
 * 
 * __DESCRIPTION__
 *
 * @package Lib\Entities\User
 * @category User
 * @author  Anthony Pillos <dev.anthonypillos@gmail.com>
 * @copyright Copyright (c) 2017
 * @version v1
 */
use Cache;
use Cartalyst\Sentinel\Users\EloquentUser as SentinelUser;

class User extends SentinelUser
{

    protected $fillable = [
        'email',
        'username',
        'password',
        'last_name',
        'first_name',
        'permissions',
    ];

    protected $loginNames = ['email', 'username'];

    protected $hidden = array('password', 'permissions');

	protected $appends = array('full_name','backend_list_url','backend_detail_url');

    public function scopeByEmailOrUsername($q,$info) {
        return $q->where('email',$info)->orWhere('username',$info);
    }


	public function getFullNameAttribute() {
        return $this->first_name . ' ' . $this->last_name;
	}

    public function getBackendListUrlAttribute() {
        return route('backend.setting.usermgt');
    }

    public function getBackendDetailUrlAttribute() {
        return route('backend.setting.usermgt.detail',$this->id);
    }


    public function appMarkets()
    {
        return $this->hasMany('Lib\Entities\AppMarket');
    }

    public function uploads()
    {
        return $this->hasMany('Lib\Entities\Upload');
    }


    /**
     * boot
     *
     * @access  protected
    */
    protected static function boot() {
        parent::boot();


        static::deleting(function($model) {
            $model->appMarkets->each(function($m){
                return $m->delete();
            });

            $model->uploads->each(function($m){
                return $m->delete();
            });
        });

        static::deleted(function($model) {
            Cache::flush();

        });

        static::saved(function($model) {
            Cache::flush();
        });
    }
}