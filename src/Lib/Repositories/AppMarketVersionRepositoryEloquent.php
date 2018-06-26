<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\AppMarketVersionRepository;
use Lib\Entities\AppMarketVersion;
use Lib\Validators\AppMarketVersionValidator;

/**
 * Class AppMarketVersionRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class AppMarketVersionRepositoryEloquent extends BaseRepository implements AppMarketVersionRepository
{

    
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AppMarketVersion::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Find By App Version
     */
    public function byAppVersion($appMarketId,$appVersion)
    {
       return $this->model
                    ->where('app_market_id',$appMarketId)
                    ->where('app_version',$appVersion)->first();
    }

    /**
     * 
     */
    public function removeById($id)
    {
        $model = $this->model->findOrFail($id);
        if($model)
            return $model->delete();

        throw new \Exception("Failed to delete id.", 1);
        
    }
}
