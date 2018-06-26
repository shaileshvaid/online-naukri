<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\CategoreableRepository;
use Lib\Entities\Categoreable;
use Lib\Validators\CategoreableValidator;

/**
 * Class CategoreableRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class CategoreableRepositoryEloquent extends BaseRepository implements CategoreableRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Categoreable::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
