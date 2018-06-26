<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\CategoryRepository;
use Lib\Entities\Category;
use Lib\Validators\CategoryValidator;
use Cache;

/**
 * Class CategoryRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class CategoryRepositoryEloquent extends BaseRepository implements CategoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Category::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function findByIdentifier($identifier)
    {
        return $this->model->findByIdentifier($identifier)->first();
    }


    /**
     * Show all Category lists
     */
    public function itemLists()
    {

        $request = app('Illuminate\Http\Request');


        if( !$request->has('parent_category_id') )
            throw new Exception("No Parent Category Found", 1);
            
        
        $m = $this->model->query();

        if( $request->has('letter') && $request->input('letter') != '')
            $m->where('title','LIKE',$request->input('letter').'%');

        if( $request->input('search') )
            $m->where('title','LIKE','%'.$request->input('search').'%');
        

        if( $request->input('per_page') )
            $this->perPage = $request->input('per_page');

        return $m->where('parent_category_id',$request->input('parent_category_id'))->orderBy('created_at','desc')->paginate($this->perPage);

    }


    /**
     * Generate Sitemap for appmarkets
     */
    public function generateSitemap()
    {
        return $this->model
                    ->isEnabled()->orderBy('created_at','desc');
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
            'is_featured' => $data['is_featured'],
            'icon'        => $data['icon'],

            'seo_title'         => $data['seo_title'],
            'seo_descriptions'  => $data['seo_descriptions'],
            'seo_keywords'      => $data['seo_keywords'],
        ]);

        $modelObj->save();
        return $modelObj;
    }

    /**
     * Show all Category lists by Parents
     */
    public function categories($byGroup = true,$isFront = false)
    {
        $parent          = app('Lib\Repositories\ParentCategoryRepositoryEloquent');
        $pCategories     = $parent->parentCategories();

        $childCategories = $this->childCategories();
        $catList         = [];

        if($byGroup == true)
        {
            foreach ($pCategories as $key => $pCat) {
                $catList[$pCat->identifier] = [
                    'id'         => $pCat->id,
                    'title'      => $pCat->title,
                    'categories' => []
                ];
                foreach ($childCategories as $key => $childCat) {
                    if($childCat['parent_category_id'] == $pCat->id)
                        $catList[$pCat->identifier]['categories'][] = $childCat;
                }
            }
        }
        else
        {
            foreach ($pCategories as $key => $pCat) {
                
                foreach ($childCategories as $key => $childCat) {
                    if($childCat['parent_category_id'] == $pCat->id)
                    {
                        $childCat['group'] = $pCat->title;

                        if($isFront == true)
                            $catList[] = [
                                'id'         => $childCat['id'],
                                'identifier' => $childCat['identifier'],
                                'title'      => $childCat['title'],
                            ];
                        else
                            $catList[] = $childCat;
                    }
                }
            }
        }
        return $catList;
    }

    /**
     * Show all Category lists by Parents
     */
    public function childCategories()
    {
        $that = $this;
        return Cache::remember('child_categories', 500, function() use ($that) {
            return $that->model->where('is_enabled',1)->get()->toArray();
        });
        // return $this->model->where('is_enabled',1)->get()->toArray();
    }


    /**
     * Show all Featured category, if no featured category was set, pick random categories
    */
    public function isFeaturedCategories()
    {
        $parent          = app('Lib\Repositories\ParentCategoryRepositoryEloquent');
        $pCategories     = $parent->parentCategories();

        if( $pCategories->isEmpty() )
            return [];

        $pCategories = $pCategories->take(2);
        $that = $this;
        return Cache::remember('is_featured_categories', 500, function() use ($that,$pCategories) {

            $categoryLists = [];
            foreach ($pCategories as $key => $pCat) {

                $categories = $that->model->isFeatured()->where('parent_category_id',$pCat->id)->get();
                if($categories->isEmpty())
                {
                    $model = $that->model->where('parent_category_id',$pCat->id);
                    $categories = $model->count() <= 10 ? $model->get() : $model->get()->random(10)->shuffle();
                }
                $categoryLists[$pCat->identifier] = [
                    'id'         => $pCat->id,
                    'title'      => $pCat->title,
                    'categories' => $categories->toArray()
                ];
            }

            return $categoryLists;
        });
    }

    /**
     * Get most viewed categories
    */
    public function popularCategories($takeOnly = 5)
    {
        $statistic = app('Lib\Entities\Statistic');
        $popular = $statistic->categories()->get()->take($takeOnly);
        return $popular;
    }


    /**
     * Get all apps/games connected based on category identifier
     */
    public function appsByCategoryIdentifier($identifier)
    {
        $app = $this->model
                    ->with('parentCategory')
                    ->findByIdentifier($identifier)
                    ->first();

        if(!$app)
            return abort(404);

        return [
            'detail' => $app,
            'apps'   => $app->apps()->paginate(30),
        ];

    }
}