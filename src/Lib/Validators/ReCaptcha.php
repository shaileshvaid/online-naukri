<?php

namespace Lib\Validators;

/**
 * ReCaptcha Validator Class
 *
 * @category  PHP
 * @package   Validator
 * @author    Anthony Pillos <dev.anthonypillos@gmail.com>
 * @license   commercial http://anthonypillos.com
 * @link      http://anthonypillos.com
 * @copyright Copyright (c) 2017 Anthony Pillos.
 * @version   v1
 */

use GuzzleHttp\Client;

class ReCaptcha
{
    public function validate(
        $attribute, 
        $value, 
        $parameters, 
        $validator
    ){
    
        $client = new Client();
        $config = systemConfig();

        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['form_params'=>
                [
                    'secret'    =>  $config['recaptcha_secret_key'],
                    'response'  =>  $value
                 ]
            ]
        );
        $body = json_decode((string)$response->getBody());

        return $body->success;
    }

}