<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\AdvertisementBlockRepository;
use Lib\Entities\AdvertisementBlock;
use Lib\Validators\AdvertisementBlockValidator;

use Illuminate\Pagination\LengthAwarePaginator;
use Lib\Exceptions\SystemError;
use Cache;
/**
 * Class AdvertisementBlockRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class AdvertisementBlockRepositoryEloquent extends BaseRepository implements AdvertisementBlockRepository
{

    private $perPage = 30;
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AdvertisementBlock::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }


    /**
     * findByIdentifier
     *
     * Show all blocks
     *
     * @access  public
     */
    public function findByIdentifier($identifier)
    {
        $that = $this;
        return Cache::remember('identifier_'.$identifier, 1500, function() use($that,$identifier)
        {
            return $that->model->where('identifier',$identifier)->first();
        }); 
    }


    /**
     * blockLists
     *
     * Show all blocks
     *
     * @access  public
     */
    public function blockLists($returnAll = true)
    {
        $input = app('Illuminate\Http\Request');
        $m     = $this->model->query();

        if($input->get('per_page') != '')
            $this->perPage = $input->get('per_page');
        
        if($returnAll == true)
            return $m->orderBy('id','desc')->get();
        
        return $m->orderBy('id','desc')->paginate($this->perPage);
        
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

        $modelObj = $this->model->find($data['id']);
        if(!$modelObj)
            throw new SystemError("Ads Code exists already,please try different name", 1);
        
        $modelObj->fill([
            'title'      => $data['title'],
            'code'       => $data['code'],
            'identifier' => $data['identifier'],
        ]);

        $modelObj->save();
        return true;
    }
}
