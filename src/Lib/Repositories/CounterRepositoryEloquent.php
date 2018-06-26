<?php

namespace Lib\Repositories;

/**
 * Class CounterRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class CounterRepositoryEloquent
{
    
    /**
     * parentCategoryCounter
     */
    public function parentCategoryCounter($hasLimit = false)
    {

        $parentCategoryModel = app('Lib\Entities\ParentCategory');


        $allIds = $parentCategoryModel->select(['id'])->get();

        $idArray = [];
        if(!$allIds->isEmpty())
            $idArray = $allIds->pluck('id')->toArray();
        
        
        
        $result = $parentCategoryModel->leftJoin('categories','parent_categories.id','=','categories.parent_category_id')
                        ->leftJoin('categoreables','categories.id','=','categoreables.category_id')
                        ->leftJoin('app_markets','categoreables.categoreable_id','=','app_markets.id')
                        ->whereIn('parent_categories.id',$idArray)
                        ->select(\DB::raw('parent_categories.identifier,parent_categories.title,COUNT(DISTINCT app_markets.id) as total'))
                        ->groupBy('parent_categories.id')
                        ->get();
        
        if($hasLimit == true)
            $result = $result->take(3);
        return $result;
       
    }
}
