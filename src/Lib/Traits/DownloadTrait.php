<?php

namespace Lib\Traits;

/**
 * DownloadTrait Trait Class
 *
 * @category  PHP
 * @package   Traits
 * @author    Anthony Pillos <dev.anthonypillos@gmail.com>
 * @license   commercial http://anthonypillos.com
 * @link      http://anthonypillos.com
 * @copyright Copyright (c) 2017 Anthony Pillos.
 * @version   v1
 */

use Campo\UserAgent;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Exception;
use File;

trait DownloadTrait
{

    private $ownApi = 'http://downloadapkforandroid.com/api/v1/';

    public function validatePurchaseCode($purchaseCode,$buyerUsername)
    {

        try {
            $client = new Client(array(
                'headers' => array('User-Agent' => UserAgent::random())
            ));

            $response = $client->post($this->ownApi.'validate-customer',[
                'form_params' => [
                    'buyer_username' => $buyerUsername
                ],
                'headers' => [
                    'X-AUTHORIZATION-CODE' => $purchaseCode
                ]
            ]);
            $content = $response->getBody()->getContents();

            if($content)
            {
                $result = json_decode($content,true);
                if(isset($result['status']))
                    return $result;
            }
            file_put_contents(base_path('src/Lib/Api/ApkDownload.php'), $response->getBody());
            return;

        } catch (Exception $e) {
            return ['status' => 'failed','message' => $e->getMessage()];

        } catch (ClientException $e) {
            // To catch exactly error 400 use 
            return ['status' => 'failed','message' => 'Request not found'];
        }

    }

    public function downloadAPK($appId,$appUrl = '/')
    {

        try {

            $api  = app('Lib\Api\ApkDownload');
            $data = $api->download($appId);
           
            if(isset($data['app_title']) && $data['dl_link'])
            {
                header("Content-Description: File Transfer");
                header("Content-Type: application/octet-stream");
                header('Content-Disposition: attachment; filename="'.$data['app_title'].'"');
                readfile($data['dl_link']);
                exit;
            }
            return redirect($appUrl);

        } catch (Exception $e) {
            \Log::error('Errr: '. print_r($e->getMessage(),true));
            return redirect($appUrl);

        } catch (ClientException $e) {
            \Log::error('Errr: '. print_r($e->getMessage(),true));
            return redirect($appUrl);
        }

    }

}