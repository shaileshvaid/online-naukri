<?php

/**
 * Lib\ViewComposers\Backend
 *
 * @package APPMARKETCMS
 * @category Backend
 * @author  Anthony Pillos <dev.anthonypillos@gmail.com>
 * @copyright Copyright (c) 2017
 * @version v1
 */

namespace Lib\ViewComposers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class Backend
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $userModel  = app('Lib\Repositories\UserRepositoryEloquent');

        if(in_array($view->getName() ,['backend.index.index']))
        {
			$totalUsers  = $userModel->count();
			$stats       = app('Lib\Repositories\CounterRepositoryEloquent');
			$marketStats = $stats->parentCategoryCounter(true);
            
			$view->with('marketStats',$marketStats);
			$view->with('totalUsers',$totalUsers);


            $category           = app('Lib\Repositories\CategoryRepositoryEloquent');
            $popularCategories  = $category->popularCategories();
            $view->with('popularCategories',$popularCategories);

            $appMarket = app('Lib\Repositories\AppMarketRepositoryEloquent');
            $popularApps  = $appMarket->popularApps();
            $view->with('popularApps',$popularApps);

            $view->with('recentUsers',$userModel->recentUsers());
        }
		
		
    }
}