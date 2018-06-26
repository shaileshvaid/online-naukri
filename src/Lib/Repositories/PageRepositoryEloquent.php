<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\PageRepository;
use Lib\Entities\Page;
use Lib\Validators\PageValidator;

/**
 * Class PageRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class PageRepositoryEloquent extends BaseRepository implements PageRepository
{
    private $perPage = 30;
    
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Page::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * pageMenu
     *
     * @access  public
     * @return Array
     */
    public function pageMenu()
    {
        $m = $this->model->all();
        $collections = [];
        foreach ($m as $key => $page) {
            $collections[] = $page->data();
        }
        return $collections;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function itemLists()
    {

        $request = app('Illuminate\Http\Request');
        $m = $this->model->query();

        if( $request->input('search') )
            $m->where('title','LIKE','%'.$request->input('search').'%');
        

        if( $request->input('per_page') )
            $this->perPage = $request->input('per_page');

        return $m->with('status')
                ->orderBy('created_at','desc')
                ->paginate($this->perPage);

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
        
        if($data['slug'] == '')
            throw new SystemError("Slug is required.", 400);


        if($modelObj->slug != $data['slug'])
        {
            $identifierObj = $this->model->where('slug',$data['slug']);
            $mObj          = $identifierObj->get();

            if(!$mObj->isEmpty())
                throw new Exception("Slug must be unique, please try again", 1);
        }


        $statusModel = app('Lib\Repositories\StatusRepositoryEloquent');
        if($data['is_draft'] == 1)
            $status = $statusModel->findByIdentifier(STAT_DRAFT);
        else
        {
            if(isset($data['status']['identifier']))
                $status = $statusModel->findByIdentifier($data['status']['identifier']);
            else
                $status = $statusModel->findByIdentifier(STAT_PUBLISHED);
        }

        $parentId = 0;
        if(isset($data['parent_page']) )
            $parentId = @$data['parent_page']['id'];

        if(isset($data['seo_keywords']) && !empty($data['seo_keywords']))
            $data['seo_keywords'] = arrayKeywordsToCommaString( $data['seo_keywords'] );


        $modelObj->fill([
            'parent_page_id' => $parentId,
            'title'          => $data['title'],
            'content'        => $data['content'],
            'is_enabled'     => $data['is_enabled'],
            'slug'           => str_slug($data['slug']),
            'status_id'      => $status->getId(),

            'seo_title'         => $data['seo_title'],
            'seo_descriptions'  => $data['seo_descriptions'],
            'seo_keywords'      => $data['seo_keywords'],
        ]);

        $modelObj->save();
        return $modelObj;
    }


    /**
     * Lists all published pages
     * @return array
     * @access  public
     */
    public function listPages()
    {
        $statusModel = app('Lib\Repositories\StatusRepositoryEloquent');
        $status      = $statusModel->findByIdentifier(STAT_PUBLISHED);

        $listsArray = [];
        if($status)
        {
            $data = $this->model->where('status_id',$status->getId())->get();

            foreach ($data as $key => $item) {
                $listsArray[] = [
                    'id'    => $item->id,
                    'slug'  => $item->slug,
                    'title'  => $item->title,
                ];
            }
        }
        $default = [
                'id'       => 0,
                'slug'     => 'none',
                'title'    => 'None',
            ];

        if(count($listsArray) > 0)
            array_unshift($listsArray, $default);
        else
            $listsArray[] = $default;

        return $listsArray;
    }


    /**
     * Format Pages return.
     * @return array
     * @access  public
     */
    public function details( $items )
    { 
        $data = [];
        if(count($items) > 0)
        {
            foreach ($items as $key => $item) {

                $modelData = $item->data();
                $additional = [
                    
                ];
                $data[] = array_merge($modelData,$additional);
            }
        }
        return $data;
    }


    /**
     * Format Pages return.
     * @return boolean
     * @access  public
     */
    public function bulkDeletions( $ids )
    { 
        $bulkModel = $this->model->whereIn('id',$ids)->get();
        if( !$bulkModel->isEmpty() )
            foreach($bulkModel as $model)
                $model->delete();
        return true;
    }

    /**
     * findBySlug
     *
     * @access  public
     * @return Array
     */
    public function findBySlug($slug)
    {
        $modelObj = $this->model->findBySlug($slug);
        return $modelObj->first();
    }
}