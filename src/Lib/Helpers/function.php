<?php

/**
* pre()
* short hand for printing array/string data
*
* @return array/string
**/
if(!function_exists('pre')){
    function pre($str) {
        echo '<pre/>';
        return print_r($str);
    }
}

/**
* truncate()
* truncate string
*
* @return void
**/
if(!function_exists('truncate')){
        function truncate($string = null, $length = 60 , $middle = false,$etc = '...',$charset='UTF-8',  $break_words = false) {
                if ($length == 0)
                        return '';
         
                if (mb_strlen($string) > $length) {
                        $length -= min($length, mb_strlen($etc));
                        if (!$break_words && !$middle) {
                                $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1));
                        }
                        if(!$middle) {
                                return mb_substr($string, 0, $length,$charset) . $etc;
                        } else {
                                return mb_substr($string, 0, $length/2,$charset) . $etc . mb_substr($string, -$length/2);
                        }
                } else {
                        return $string;
                }
        }
}


/**
* arrayKeywordsToCommaString()
*
* @return string
**/
if (!function_exists('arrayKeywordsToCommaString')) {
    function arrayKeywordsToCommaString($data)
    {
                if($data == '')
                        return '';
                
                $keywordArray = [];
                foreach ($data as $key => $val) {
                        $keywordArray[] = $val['text'];
                }
                return implode(',', $keywordArray);
    }
}


/**
* commaStringToArrayKeywords()
*
* @return string
**/
if (!function_exists('commaStringToArrayKeywords')) {
    function commaStringToArrayKeywords($data)
    {
                $keyArr = explode(',', $data);
                $keywordArray = [];
                foreach ($keyArr as $key => $word) {
                        $keywordArray[] = ['text' => $word];
                }
                return $keywordArray;
    }
}


/**
* numberInAString()
*
* @return string
**/
if (!function_exists('numberInAString')) {

    function numberInAString($string)
    {
            preg_match_all('/([0-9]+\.[0-9]+)/', $string, $matches);
            return is_array($matches[0]) ? @$matches[0][0] : 5;
    }
}

/**
* elixit()
* We used this for getting our js/css file assets and for generating build
* in our productions.
*
* @return void
**/
if (!function_exists('elixit')) {

        function elixit($file)
        {

            if( !File::exists(public_path('build/rev-manifest.json')))
                return asset($file);
         
            static $manifest = null;
            if (is_null($manifest)) {
                    $manifest = json_decode(file_get_contents( public_path('build/rev-manifest.json') ) , true);
            }
            if (isset($manifest[$file]))
                $file = '/build/' . $manifest[$file];

            return asset($file);
        }
}


/**
* showAds()
*
* @return string
**/
if (!function_exists('showAds')) {

    function showAds($code)
    {
            $adsModel = app('Lib\Repositories\AdvertisementBlockRepositoryEloquent');
            $ads = $adsModel->findByIdentifier($code);
            if($ads)
                return $ads->code;
            return 'Setup ads codes';
    }
}


/**
* generateFileNameFromUrl()
* Extract url/path and return path informations
*
* @return void
**/
if(!function_exists('generateFileNameFromUrl')){
        function generateFileNameFromUrl($url) {
                $path       = @pathinfo($url);
                $ext        = @$path['extension'];
                $basename   = @$path['basename'];
                $fileName   = md5($basename.time().uniqid()).'.'.$ext;
                return [
                        'filename'  => $fileName,
                        'basename'  => $basename,
                        'extension' => $ext
                ];
        }
}

/**
* uploadFileInfo()
* Get File Info
*
* @return void
**/
if(!function_exists('uploadFileInfo')){
        function uploadFileInfo($file) {

                if(!$file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)
                        throw new Exception("Your file submitted is not an instance of Symfony\Component\HttpFoundation\File\UploadedFile Class", 400);

                return [
                        'path'      => @$file->getRealPath(),
                        'name'      => @$file->getClientOriginalName(),
                        'extension' => @$file->getClientOriginalExtension(),
                        'size'      => @$file->getSize(),
                        'mime'      => @$file->getMimeType()
                ];
        }
}


if(!function_exists('isImageFile')){
        function isImageFile($file) {
                $info = pathinfo($file);
                return in_array(strtolower($info['extension']), 
                                                array("jpg", "jpeg", "gif", "png", "bmp"));
        }
}

if (!function_exists('formattedFileSize')) {
    function formattedFileSize($bytes, $si = false)
    {
        $thresh = 1024;
        if ($si) $thresh = 1000;
        $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
        if ($si)
            $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $bytes > 0 ? floor(log($bytes, $thresh)) : 0;
        return round($bytes / pow($thresh, $power), 1) . $units[$power];
    }
}

/**
* countFormat()
**/
if(!function_exists('countFormat')){
        function countFormat($num) {
            $x = round($num);
            $x_number_format = number_format($x);
            $x_array = explode(',', $x_number_format);
            $x_parts = array('K', 'M', 'B', 'T');
            $x_count_parts = count($x_array) - 1;
            $x_display = $x;
            $x_display = @$x_array[0] . ((int) @$x_array[1][0] !== 0 ? '.' . @$x_array[1][0] : '');
            $x_display .= @$x_parts[@$x_count_parts - 1];
            return $x_display;
        }
}

/**
* downloadLink()
**/
if(!function_exists('downloadLink')){
    function downloadLink($title,$appId,$version) {
        return route('frontend.download',str_slug($title)).'?app_id='.$appId.'&token='.encrypt($version);
    }
}


/**
* systemConfig()
**/
if(!function_exists('systemConfig')){
        function systemConfig() {
                return Cache::remember('site_configuration', 60, function()
                {
                        $configuraton = app('Lib\Repositories\ConfigurationRepositoryEloquent');
                        $temp = [];
                        $config = $configuraton->all(['key', 'value']);
                        foreach ($config as $key => $value)
                                $temp[$value['key']] = $value['value'];
                        
                        return $temp;
                });
        }
}


// Returns a file size limit in bytes based on the PHP upload_max_filesize
// and post_max_size

if(!function_exists('file_upload_max_size')){
    function file_upload_max_size() {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $post_max_size = parse_size(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }
}

if(!function_exists('parse_size')){
    function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }
}


if(!function_exists('firefoxImage')){
    function firefoxImage($url) {
        $isFireFox = false;
        $agent = request()->server('HTTP_USER_AGENT');
        if (strlen(strstr(strtolower($agent), 'firefox')) > 0) {
            $isFireFox = true;
        }
        if($isFireFox == true)
            return str_replace('-rw', '', $url);

        return $url;
    }
}

/**
 * 
 */
if(!function_exists('counter'))
{
    function counter($obj)
    { 

        $countable = app('SystemCore\Support\Counter');

        if(is_array($obj))
            return count($obj);
        
        return $countable->count($obj);

    }
}
