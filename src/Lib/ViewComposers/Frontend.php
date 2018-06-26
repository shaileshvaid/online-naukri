<?php

/**
 * Lib\ViewComposers\Frontend
 *
 * @package APPMARKETCMS
 * @category Frontend
 * @author  Anthony Pillos <dev.anthonypillos@gmail.com>
 * @copyright Copyright (c) 2017
 * @version v1
 */

namespace Lib\ViewComposers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Cache;
use Barryvdh\TranslationManager\Models\Translation;

class Frontend
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {

        $viewName  = str_replace('frontend.', '', $view->getName());
        $appMarket = app('Lib\Repositories\AppMarketRepositoryEloquent');

        if($viewName == 'index.index')
            $view->with('is_index',true);

        if(in_array($viewName, ['index.index','index.category','index.parent-category','index.search']))
        {
            
            $featuredItems = $appMarket->featuredItemLists(5);
            $view->with('featuredItems',$featuredItems);
        }

        // featured categories
        $category           = app('Lib\Repositories\CategoryRepositoryEloquent');
        $popularCategories  = $category->popularCategories();
        $view->with('popularCategories',$popularCategories);


        $popularApps  = $appMarket->popularApps();
        $view->with('popularApps',$popularApps);

        $featuredCategories = $category->isFeaturedCategories();
        $view->with('featuredCategories',$featuredCategories);

        $parentCat = app('Lib\Repositories\ParentCategoryRepositoryEloquent');
        $parentCatCollections = $parentCat->parentCategories();
        $view->with('parentCatCollections',$parentCatCollections);
        
        // pages
        $page = app('Lib\Repositories\PageRepositoryEloquent');
        $pages = $page->pageMenu();
        $view->with('pages',$pages);

        
        $dbLocale = Cache::remember('locale_db', 1500, function()  {
            return Translation::groupBy('locale')->lists('locale')->toArray();
        });
        $view->with('dbLocale',$dbLocale);

    }
}