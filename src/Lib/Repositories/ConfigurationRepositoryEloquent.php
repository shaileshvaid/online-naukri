<?php

namespace Lib\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Lib\Interfaces\ConfigurationRepository;
use Lib\Entities\Configuration;
use Lib\Validators\ConfigurationValidator;
use Storage;

/**
 * Class ConfigurationRepositoryEloquent
 * @package namespace Lib\Repositories;
 */
class ConfigurationRepositoryEloquent extends BaseRepository implements ConfigurationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Configuration::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Site Logo
     */
    public function imageSiteLogo()
    {
        $model = $this->model->with('upload')->findByKey('cms_upload_logo')->first();
        if($model->upload)
            return $model->upload->image_link;

        return '';
    }  


    /**
     * Boot up the repository, pushing criteria
     */
    public function generalLists()
    {
        $m = $this->model->query();
        
        $collections = $m->with('upload')->orderBy('id','asc')->get();
        $tempCollections    = [];
        foreach ($collections as $key => $data) {

            $info = $data->data();

            $tempCollections[$data->group_slug]['title'] = $info['group_name'];
            $tempCollections[$data->group_slug]['lists'][] = $info;        
        }
        return $tempCollections;
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
        foreach ($data as $key => $input) {
            $config = $this->model->where('key',$key)->first();
            if($config)
            {
                $config->value = $input;
                $config->save();
            }
        }
        return true;
    }


    /**
     * removeLogo
     *
     * Remove Logo Icon
     *
     * @access  public
     */
    public function removeLogo( $key )
    {

        $model = $this->model->findByKey($key)->first();
        if(!$model)
            throw new \Exception("Cannot find image_key", 1);

        return $model->upload->delete();
    }


    /**
     * uploadLogo
     *
     * Update Detail Information
     *
     * @access  public
     */
    public function uploadLogo( $data )
    {   
        $model = $this->model->findByKey($data['image_key'])->first();
        if(!$model)
            throw new \Exception("Cannot find image_key", 1);
        
        $image = $data['file'];
        if($image instanceof \Illuminate\Http\UploadedFile)
        {

            Storage::disk('uploads')->deleteDirectory(UPLOAD_LOGO);

            $uploadInfo = uploadFileInfo($image);
            $fileInfo   = generateFileNameFromUrl($uploadInfo['name']);

            $path = UPLOAD_LOGO.'/'.$fileInfo['filename'];
            Storage::disk('uploads')->put($path, file_get_contents($image->getRealPath()) );

            $data = [
                'user_id'       => 0,
                'file_path'     => $fileInfo['filename'],
                'original_name' => $fileInfo['basename'],
                'upload_type'   => UPLOAD_LOGO,
                'position'      => 1
            ];
            $model->upload()->updateOrCreate(['upload_type' => UPLOAD_LOGO],$data);
        }
        return;
    }
}