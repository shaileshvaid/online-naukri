<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\AppMarketRepository;
use Lib\Entities\AppMarket;
use Lib\Validators\AppMarketValidator;

use Exception;
use File;
use Storage;
use Lib\Exceptions\SystemError;

/**
 * Class AppMarketRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class AppMarketRepositoryEloquent extends BaseRepository implements AppMarketRepository
{

    private $perPage = 30;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AppMarket::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }


    /**
     * Get Info by App ID
     */
    public function byAppId($appId)
    {
        return $this->model->isEnabled()->byAppId($appId)->first();
    }

    /**
     * Get detailed info by APP id
     */
    public function byAppIdWithDetails($appId)
    {
        $dataArray = $this->model
                        ->with(['statistic',
                                'categories',
                                'tags',
                                'screenshots',
                                'appImage',
                                'versions',
                                'reviews',
                                'histogram'])
                        ->isEnabled()
                        ->byAppId($appId)
                        ->first();

        if(!$dataArray)
            throw new Exception("App ID Doesnt exists", 1);
            
        return $dataArray;

    }


    /**
     * Get all Featured Items
     */
    public function featuredItemLists($length = null)
    {
        $model = $this->model->with(['appImage'])->isEnabled()->where('is_featured',1)->orderBy('created_at','desc');
        if($length)
            return $model->get()->take($length);

        return $model->paginate($this->perPage);
    }


    /**
     * Get Recently Added Apps/games etc.
     */
    public function newestItemLists($length = 15,$isSubmittedApp = false)
    {
        $isSubmit = ($isSubmittedApp == true) ? 1 : 0;
        $model = $this->model->with(['appImage'])->isEnabled()->isSubmittedApp($isSubmit)->orderBy('created_at','desc');
        return $model->get()->take($length);
    }


    /**
     * Get most viewed apps
    */
    public function popularApps($takeOnly = 5)
    {
        $statistic = app('Lib\Entities\Statistic');
        $popular = $statistic->apps()->get()->take($takeOnly);
        return $popular;
    }


    /**
     * Show Submitted Apps by User
     */
    public function submittedItemLists($userId)
    {
        $m = $this->model->query();
        return $m->with(['appImage'])->isSubmittedApp()->where('user_id',$userId)->orderBy('created_at','desc')->paginate($this->perPage);
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

        if( $request->input('is_featured') )
            $m->where('is_featured',0);


        if( $request->has('is_submitted')  )
            $m->where('is_submitted_app',1);
        else
            $m->where('is_submitted_app',0);

        return $m->with(['appImage'])->orderBy('created_at','desc')->paginate($this->perPage);

    }

    /**
     * Generate Sitemap for appmarkets
     */
    public function generateSitemap()
    {
        return $this->model
                    ->with(['appImage','user'])
                    ->isEnabled()->orderBy('created_at','desc');
    }


    /**
     * Search from android markets
     */
    public function search($query)
    {

        $m = $this->model->query();
        if( $query )
            $m->where('title','LIKE','%'.$query.'%');
        
        return $m->with(['appImage'])->orderBy('created_at','desc')->get();

    }


    /**
     * Import Apps from google play to our system.
     */
    public function importApp( $input )
    {   

        $data = array_filter($input['data']);
        
        if(isset($input['is_bulk']))
        {
            foreach ($data as $key => $value) {
                $input['data'] = $value;
                $this->singleApp($input);
            }
        }
        else
            $this->singleApp($input);
        
        return;
    }


    /**
     * Create new App.
    */
    public function createDetails($data)
    {

        if(isset($data['seo_keywords']) && !empty($data['seo_keywords']))
            $data['seo_keywords'] = arrayKeywordsToCommaString( $data['seo_keywords'] );

        $input = [
            'app_id'         => $data['app_id'],
            'title'          => $data['title'],
            'description'    => $data['description'],
            'is_enabled'     => $data['is_enabled'],
            'is_featured'    => $data['is_featured'],
            'link'           => $data['link'],
            'developer_name' => $data['developer_name'],
            'developer_link' => $data['developer_link'],
            'image_url'      => @$data['image_url'],
            'user_id'        => @$data['user_id'],

            'is_submitted_app' => isset($data['is_submitted_app']) ? $data['is_submitted_app'] : 0,

            'seo_title'        => isset($data['seo_title']) ? $data['seo_title']        : '',
            'seo_descriptions' => isset($data['seo_descriptions']) ? $data['seo_descriptions'] : '',
            'seo_keywords'     => isset($data['seo_keywords']) ? $data['seo_keywords']     : '',
        ];

        $modelObj = $this->model->create($input);

        if($modelObj)
        {
            $this->categories($data,$modelObj);
            $this->imageApp($data,$modelObj);
            $this->screenShots($data,$modelObj);
            $this->uploadApk($data,$modelObj);
            $this->rateHistogram($data,$modelObj);
        }
        return $modelObj;
    }

    /**
     * Update App Details.
    */
    public function updateDetails($data)
    {
        
        $modelObj = $this->model->byAppId($data['app_id'])->first();
        if(!$modelObj)
            throw new SystemError("Failed to find app id.", 400);
        
        if(isset($data['seo_keywords']) && !empty($data['seo_keywords']))
            $data['seo_keywords'] = arrayKeywordsToCommaString( $data['seo_keywords'] );

        $this->categories($data,$modelObj);
        $this->imageApp($data,$modelObj);
        $this->screenShots($data,$modelObj);
        $this->uploadApk($data,$modelObj);
        $this->updateReviews($data,$modelObj);
        $this->rateHistogram($data,$modelObj);

        $customInput = [];
        if(isset($data['custom']))
            $customInput = $data['custom'];
        
        $customInput = json_encode($customInput);

        $modelObj->fill([
            'title'          => $data['title'],
            'description'    => $data['description'],
            'is_enabled'     => $data['is_enabled'],
            'link'           => $data['link'],
            'developer_name' => $data['developer_name'],
            'developer_link' => $data['developer_link'],
            'image_url'      => @$data['image_url'],

            'ratings'         => @$data['ratings'],
            'required_android' => @$data['required_android'],
            'current_version'  => @$data['current_version'],
            'installs'         => @$data['installs'],
            'custom'         => @$customInput,
            
            'ratings_total'         => @$data['ratings_total'],
            'published_date'         => @$data['published_date'],


            'seo_title'        => isset($data['seo_title']) ? $data['seo_title']        : '',
            'seo_descriptions' => isset($data['seo_descriptions']) ? $data['seo_descriptions'] : '',
            'seo_keywords'     => isset($data['seo_keywords']) ? $data['seo_keywords']     : '',
        ]);

        $modelObj->save();
        return $modelObj;
    }


    /**
     * Update App Apk Version Details.
    */
    public function updateVersionDetails($data)
    {

        $modelObj = $this->model->find($data['app_market_id'])->first();
        if(!$modelObj)
            throw new SystemError("Failed to find app id.", 400);



        $input = [

            'app_version' => $data['app_version'],
            'signature'   => $data['signature'],
            'sha_1'       => $data['sha_1'],
            'description' => $data['description'],
            'app_link'    => @$data['app_link'],
            'is_link'     => (@$data['app_link']) ? 1 : 0,
        ];

        if(isset($data['apk_file_upload']))
            $input['file'] = $data['apk_file_upload']['file'];

        return $this->uploadApkFiles($input,$modelObj,'is_edit');
    }


    /**
     *  Upload Image
     */
    public function uploadScreenshots($images,$model)
    { 

        if(!$model->screenshots->isEmpty())
        {
            $model->screenshots()->each(function($m){
                $m->delete();
            });
        }
        foreach ($images as $key => $image) {

            if($image instanceof \Illuminate\Http\UploadedFile)
            {
                
                $uploadInfo = uploadFileInfo($image);
                $fileInfo   = generateFileNameFromUrl($uploadInfo['name']);

                $path = $model->id.'/'.UPLOAD_SCREENSHOT.'/'.$fileInfo['filename'];
                Storage::disk('uploads')->put($path, file_get_contents($image->getRealPath()) );

                $data = [
                    'user_id'       => $model->user_id,
                    'file_path'     => $fileInfo['filename'],
                    'original_name' => $fileInfo['basename'],
                    'upload_type'   => UPLOAD_FILE,
                    'position'      => ++$key
                ];
                $model->screenshots()->create($data);
            }
            // image link
            else
            {
                $data = [
                    'user_id'       => $model->user_id,
                    'file_path'     => '',
                    'original_name' => '',
                    'upload_type'   => UPLOAD_LINK,
                    'image_url'     => isset($image['link']) ? $image['link'] : $image,
                    'position'      => ++$key
                ];
                $model->screenshots()->create($data);
            }
        }
        return;
    }


    /**
     *  Upload Image
     */
    public function uploadAppImage($image,$model)
    { 
        if($image instanceof \Illuminate\Http\UploadedFile)
        {
            $uploadInfo = uploadFileInfo($image);
            $fileInfo   = generateFileNameFromUrl($uploadInfo['name']);

            $path = $model->id.'/'.UPLOAD_APPIMAGE.'/'.$fileInfo['filename'];
            Storage::disk('uploads')->put($path, file_get_contents($image->getRealPath()) );

            $data = [
                'user_id'       => $model->user_id,
                'file_path'     => $fileInfo['filename'],
                'original_name' => $fileInfo['basename'],
                'upload_type'   => UPLOAD_APPIMAGE,
                'position'      => 1
            ];
            $model->appImage()->create($data);
        }
        return;
    }

    /**
     *  Upload Custom Apk files
     */
    public function uploadApkFiles($data,$model,$type = '')
    {

        if( !isset($data['app_version']) )
            throw new Exception("App Version is missing in your requests.", 1);

        $appMarket  = app('Lib\Repositories\AppMarketVersionRepositoryEloquent');
        $appVersion = $data['app_version'];

        $input   = [
            'user_id'       => $model->user_id,
            'app_market_id' => $model->id,
            'app_version'   => $appVersion,
            'signature'     => @$data['signature'],
            'sha_1'         => @$data['sha_1'],
            'description'   => @$data['description'],
            'app_link'      => @$data['app_link'],
            'is_link'       => @$data['app_link'] ? 1 : 0,
        ];

        if($type == 'is_create')
        {
            $appObj = $appMarket->byAppVersion($model->id,$appVersion);
            if($appObj)
                throw new Exception(sprintf('%s version exists already in the system. please try different version or update it.',$appVersion), 1);
                
        }


        if( isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile )
        {
            $file       = $data['file'];
            $uploadInfo = uploadFileInfo($file);
            $fileInfo   = generateFileNameFromUrl($uploadInfo['name']);

            $input = array_merge($input,[
                        'file_path'     => $fileInfo['filename'],
                        'size'          => @$uploadInfo['size'],
                        'original_name' => $fileInfo['basename'],
                    ]);
        }

        $obj = $appMarket->updateOrCreate(['app_version' => $appVersion],$input);
        if($obj)
        {

            if(isset($data['file']))
            {
                $folderPath = $model->id.'/'.UPLOAD_APP_APK.'/'.$appVersion;
                Storage::disk('uploads')->deleteDirectory($folderPath);
                
                $path = $folderPath.'/'.$fileInfo['filename'];
                Storage::disk('uploads')->put($path, file_get_contents($file->getRealPath()) );
            }
        }
        return true;
    }


    /**
     * Set all item ids to featured item
     */
    public function setFeaturedItems($ids,$isFeatured = 1)
    {
        return $this->model->whereIn('id',$ids)->update(['is_featured' => $isFeatured]);
    }


    /**
     * Process single app
    */
    private function singleApp($input)
    {
        
        $data = $input['data'];
        $res = [

            'user_id'     => $data['user_id'],
            'app_id'      => $data['app_id'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'link'        => $data['link'],
            'image_url'   => isset($data['image']['small']) ? $data['image']['small'] : $data['image']['large'],
            'ratings'     => $data['ratings'],

            'developer_name' => $data['developer']['name'],
            'developer_link' => $data['developer']['link'],


            'seo_title'        => $data['title'],
            'seo_keywords'     => $data['title'],
            'seo_descriptions' => truncate(trim(htmlentities($data['description'])),150),

            'is_enabled'    => 1
        ];

        $obj = $this->model->where('app_id',$data['app_id'])->first();

        if(!$obj)
        {
            $objModel = $this->model->create($res);
            if($objModel)
            {
                if(isset($input['categories']))
                    $objModel->categories()->sync($input['categories']);

                return ['message' => 'App Successfully created'];
            }
        }
        return;
    }


    /**
     * Process categories
    */
    private function categories($data,$modelObj)
    {
        if(isset($data['categories']))
        {
            $catArrIds = array_pluck($data['categories'],'id');
            if(count($catArrIds) > 0)
                $modelObj->categories()->sync($catArrIds);
        }
        return;
    }

    /**
     * Process imageApp
    */
    private function imageApp($data,$modelObj)
    {
        if(isset($data['image_app']))
            $this->uploadAppImage($data['image_app'],$modelObj);
        return;
    }

    /**
     * Process screenShots
    */
    private function screenShots($data,$modelObj)
    {
        
        if(isset($data['screenshots']))
            $this->uploadScreenshots($data['screenshots'],$modelObj);
        return;
    }

    /**
     * Process uploadApk
    */
    private function uploadApk($data,$modelObj)
    {
        
        if(isset($data['apk_file_upload']))
            $this->uploadApkFiles($data['apk_file_upload'],$modelObj,'is_create');
        return;
    }

    /**
     * Process updateReviews
    */
    private function updateReviews($data,$modelObj)
    {
        if(isset($data['reviews']))
        {
            if(!$modelObj->reviews->isEmpty())
            {
                $modelObj->reviews()->each(function($m){
                    if($m->is_google_play == 1)
                        $m->delete();
                });
            }
            foreach ($data['reviews'] as $key => $review) {
                $reviewArr = [
                    'author_name'    => (trim($review['author']) != '') ? $review['author'] : 'Unknown',
                    'published_at'   => $review['published_date'],
                    'comments'       => $review['comments'],
                    'image_url'      => $review['image'],
                    'is_google_play' => 1
                ];
                $modelObj->reviews()->create($reviewArr);
            }
        }
        return;
    }

    /**
     * Process rateHistogram
    */
    private function rateHistogram($data,$modelObj)
    {
        if(isset($data['rating_histogram']))
        {
            if(!$modelObj->histogram->isEmpty())
            {
                $modelObj->histogram()->each(function($m){
                    $m->delete();
                });
            }
            foreach ($data['rating_histogram'] as $key => $data) {
                $itemArr = [

                    'num'        => $data['num'],
                    'bar_length' => $data['bar_length'],
                    'bar_number' => $data['bar_number']
                ];
                $modelObj->histogram()
                    ->create($itemArr);
            }
        }
        return;
    }
}