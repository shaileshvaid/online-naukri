<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\ParentCategoryRepository;
use Lib\Entities\ParentCategory;
use Lib\Validators\ParentCategoryValidator;
use Cache;
/**
 * Class ParentCategoryRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class ParentCategoryRepositoryEloquent extends BaseRepository implements ParentCategoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ParentCategory::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
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

        // $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // $currentResult = $data->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();

        // return new LengthAwarePaginator($currentResult, $data->count(), $this->perPage);
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
            throw new SystemError("Failed to find page id.", 400);
        
        
        if($modelObj->identifier != $data['identifier'])
        {
            $identifierObj = $this->model->where('identifier',$data['identifier']);
            $mObj          = $identifierObj->get();

            if(!$mObj->isEmpty())
                throw new Exception("Identifier must be unique, please try again", 1);
        }

        if(isset($data['seo_keywords']) && !empty($data['seo_keywords']))
            $data['seo_keywords'] = arrayKeywordsToCommaString( $data['seo_keywords'] );

        
        $modelObj->fill([
            'title'       => $data['title'],
            'description' => $data['description'],
            'is_enabled'  => $data['is_enabled'],
            'icon'        => $data['icon'],

            'seo_title'         => $data['seo_title'],
            'seo_descriptions'  => $data['seo_descriptions'],
            'seo_keywords'      => $data['seo_keywords'],
        ]);

        $modelObj->save();
        return $modelObj;
    }

    /**
     * Show all parent category
     */
    public function parentCategories()
    {
        $that = $this;
        return Cache::remember('parent_categories', 1500, function() use ($that) {
            return $that->model->where('is_enabled',1)->get();
        });
        // return $this->model->where('is_enabled',1)->get();
    }

    /**
     * Show all parent category
     */
    public function findByIdentifier($identifier)
    {
        // $that = $this;
        // return Cache::remember('parent_category_details', 1500, function() use ($that,$identifier) {
        //     return $that->model->findByIdentifier($identifier)->first();
        // });
        return $this->model->with('categories')->findByIdentifier($identifier)->first();
    }

}