<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\AppMarketReviewRepository;
use Lib\Entities\AppMarketReview;
use Lib\Validators\AppMarketReviewValidator;

/**
 * Class AppMarketReviewRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class AppMarketReviewRepositoryEloquent extends BaseRepository implements AppMarketReviewRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AppMarketReview::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
