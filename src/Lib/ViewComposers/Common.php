<?php

/**
 * Lib\ViewComposers\Common
 *
 * @package APPMARKETCMS
 * @category Common
 * @author  Anthony Pillos <dev.anthonypillos@gmail.com>
 * @copyright Copyright (c) 2017
 * @version v1
 */

namespace Lib\ViewComposers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use Sentinel;

class Common
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = Sentinel::getUser();

        // super admin
        $isSuperAdmin = $this->isSuperAdmin($user);
        $view->with('isSuperAdmin', $isSuperAdmin);

        // administrator
        $isAdmin = $this->isAdmin($user);
        $view->with('isAdmin', $isAdmin);

        $view->with('userDetail', $user);
        $view->with('letters', range('A', 'Z') );

        $view->with('isDemo',($isSuperAdmin) ? false : env('DEMO_MODE_ON'));

        $configuration = systemConfig();
        $view->with('configuration',$configuration);


        $configuraton = app('Lib\Repositories\ConfigurationRepositoryEloquent');
        $image_logo = $configuraton->imageSiteLogo();
        $view->with('image_logo',$image_logo);
        
    }

    public function isSuperAdmin($user)
    {
        $isSuperAdmin = false;
        if ($user )
            if($user->inRole('elite'))
                $isSuperAdmin = true;

        return $isSuperAdmin;
    }

    public function isAdmin($user)
    {
        $isAdmin = false;
        if ($user )
            if($user->inRole('administrator'))
                $isAdmin = true;

        return $isAdmin;
    }
}