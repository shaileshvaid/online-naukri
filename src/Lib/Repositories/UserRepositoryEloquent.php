<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\UserRepository;
use Lib\Entities\User;
use Lib\Validators\UserValidator;
use Sentinel;
use Exception;
/**
 * Class UserRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{

    private $perPage = 40;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Total users
     */
    public function count()
    {
        return $this->model->count();
    }

    /**
     * Recent Users
     */
    public function recentUsers($takeOnly = 10)
    {
        return $this->model->with('appMarkets')->get()->take($takeOnly);
    }


    /**
     * Show all Category lists
     */
    public function itemLists()
    {

        $request = app('Illuminate\Http\Request');

        
        $m = $this->model->query();

        if( $request->has('letter') && $request->input('letter') != '')
            $m->where('title','LIKE',$request->input('letter').'%');

        if( $request->input('search') )
            $m->where('title','LIKE','%'.$request->input('search').'%');
        

        if( $request->input('per_page') )
            $this->perPage = $request->input('per_page');

        return $m->orderBy('created_at','desc')->paginate($this->perPage);

    }


    /**
     * updateDetails
     *
     * Update Detail Information
     *
     * @access  public
     */
    public function updateDetails( $data )
    {   

        $modelObj = Sentinel::findById($data['id']);
        if(!$modelObj)
            throw new Exception("User is not exists", 1);
        

        $userExists = $this->model->where('username',$data['username'])
                            ->orWhere('email',$data['email'])->first();

        if ($userExists) {
            if ($userExists->id != $modelObj->id) {
                throw new Exception("Email address/Username already exist.",1);
            }
        }

        if(isset($data['roles']))
        {
            $userRoles = $modelObj->roles->lists('id')->toArray();
            $modelObj->roles()->detach($userRoles);
            
            $selectedRoles = array_pluck($data['roles'],'id');
            $modelObj->roles()->attach($selectedRoles);
        }
        
        if(isset($data['password']))
        {
            Sentinel::update($modelObj, $data);
        }
        else
        {
            $modelObj->fill($data);
            $modelObj->save();
        }
        return true;
    }

    /**
     * findUserByEmailorUsername
     *
     * Find user by email and username
     *
     * @access  public
     */
    public function findUserByEmailorUsername( $detail )
    {   
        return $this->model->byEmailOrUsername($detail)->first();
    }
}