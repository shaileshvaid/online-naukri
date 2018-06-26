<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\SearchLogRepository;
use Lib\Entities\SearchLog;
use Lib\Validators\SearchLogValidator;

/**
 * Class PageRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class SearchLogRepositoryEloquent extends BaseRepository implements SearchLogRepository
{
    private $perPage = 30;
    
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SearchLog::class;
    }    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    /**
     * Lists all published pages
     * @return array
     * @access  public
     */
    public function listSearchLogs()
    {
        $listsArray = [];
        $data = $this->model->get();

        foreach ($data as $key => $item) {
            $listsArray[] = [
                'id'    => $item->id,
                'search_keyword'  => $item->search_keyword,
                'created_at' => $item->created_at
            ];
        }
        
        $default = [
                'id'       => 0,
                'search_keyword' => 'none',
                'created_at'    => '0000:00:00 00:00:00',
            ];

        if(count($listsArray) > 0)
            array_unshift($listsArray, $default);
        else
            $listsArray[] = $default;

        return $listsArray;
    }
    
    /**
     * Show all Search Log lists
     */
    public function itemLists()
    {

        $request = app('Illuminate\Http\Request');
        $m = $this->model->query();

        if( $request->has('letter') && $request->input('letter') != '')
            $m->where('search_keyword','LIKE',$request->input('letter').'%');
        
        if( $request->input('search') )
            $m->where('search_keyword','LIKE','%'.$request->input('search').'%');
        

        if( $request->input('per_page') )
            $this->perPage = $request->input('per_page');

        return $m->orderBy('created_at','desc')
                ->paginate($this->perPage);

        // $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // $currentResult = $data->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();

        // return new LengthAwarePaginator($currentResult, $data->count(), $this->perPage);
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
     * Create new App.
    */
    public function setSearchKeyword($data)
    {

        if(isset($data['q']) && !empty($data['q']))
            $data['search_keyword'] = $data['q'];

        $input = [
            'search_keyword' => $data['search_keyword']
        ];

        $obj = $this->model->where('search_keyword', $data['search_keyword'])->first();

        if(!$obj)
        {
            $objModel = $this->model->create($input);
            return $objModel;
        }
        return;
    }
        
}