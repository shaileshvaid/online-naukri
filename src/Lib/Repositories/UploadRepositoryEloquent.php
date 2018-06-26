<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\UploadRepository;
use Lib\Entities\Upload;
use Lib\Validators\UploadValidator;

/**
 * Class UploadRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class UploadRepositoryEloquent extends BaseRepository implements UploadRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Upload::class;
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
    public function removeById($id)
    {
        $model = $this->model->findOrFail($id);
        if($model)
            return $model->delete();

        throw new \Exception("Failed to delete id.", 1);
        
    }
}
