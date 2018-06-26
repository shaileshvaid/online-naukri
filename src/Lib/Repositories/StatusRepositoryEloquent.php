<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\StatusRepository;
use Lib\Entities\Status;
use Lib\Validators\StatusValidator;

/**
 * Class StatusRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class StatusRepositoryEloquent extends BaseRepository implements StatusRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Status::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Display Table Name
     */
    public function table()
    {
        return $this->model->getTable();
    }

    /**
     * Find by identifier
     */
    public function findByIdentifier( $identifier )
    {
        return  $this->model->where(['identifier' => $identifier ] )
                            ->first();
    }

    /**
     * Show all status for visibility
     */
    public function visibilityLists()
    {
        return  $this->model->where(['type' => 2 ] )
                            ->get();
    }

    /**
     * Show all status that we used for posts,page, etc..
    */
    public function statusLists()
    {
        return  $this->model->where(['type' => 1 ] )
                            ->get();
    }
}
