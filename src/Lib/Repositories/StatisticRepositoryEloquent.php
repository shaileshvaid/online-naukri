<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\StatisticRepository;
use Lib\Entities\Statistic;
use Lib\Validators\StatisticValidator;

/**
 * Class StatisticRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class StatisticRepositoryEloquent extends BaseRepository implements StatisticRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Statistic::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 
     */
    public function views()
    {
        return $this->model->with('statisticable')->orderBy('views','desc')->take(10)->get();
    }
}
