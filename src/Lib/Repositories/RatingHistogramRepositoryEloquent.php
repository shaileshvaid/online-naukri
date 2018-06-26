<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\RatingHistogramRepository;
use Lib\Entities\RatingHistogram;
use Lib\Validators\RatingHistogramValidator;

/**
 * Class RatingHistogramRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class RatingHistogramRepositoryEloquent extends BaseRepository implements RatingHistogramRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return RatingHistogram::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
