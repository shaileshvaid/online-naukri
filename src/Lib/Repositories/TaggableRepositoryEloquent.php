<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\TaggableRepository;
use Lib\Entities\Taggable;
use Lib\Validators\TaggableValidator;

/**
 * Class TaggableRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class TaggableRepositoryEloquent extends BaseRepository implements TaggableRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Taggable::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
