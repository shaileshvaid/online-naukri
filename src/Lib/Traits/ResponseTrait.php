<?php

namespace Lib\Traits;

/**
 * Lib\Traits
 * 
 * __DESCRIPTION__
 *
 * @package MyCMS
 * @category ResponseTrait
 * @author  Anthony Pillos <dev.anthonypillos@gmail.com>
 * @copyright Copyright (c) 2016
 * @version v1
 */

trait ResponseTrait
{

   	/**
     * @var array HTTP response codes and messages
     */
    protected $statuses = array(
        //Informational 1xx
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        //Successful 2xx
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        //Redirection 3xx
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Unused)',
        307 => '307 Temporary Redirect',
        //Client Error 4xx
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Long',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested Range Not Satisfiable',
        417 => '417 Expectation Failed',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        //Server Error 5xx
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported'
    );


	public function cmsResponse($messages, $code = 200)
    {
    	return $this->make($messages, $code);
    }

	private function make($data, $code, $overwrite = false)
    {
    	// Status returned.
        $status = (preg_match('/^(1|2|3)/', $code)) ? 'success' : 'error';
        // Change object to array.
        if (is_object($data))
        {
            $data = $data->toArray();
        }
        // Data as a string.
        if (is_string($data))
        {
            $data = array('message' => $data);
        }

        // Overwrite response format.
        if ($overwrite === true)
        {
            $response = $data;
        }
        else
        {
            $message =  array_key_exists($code, $this->statuses) ? $this->statuses[$code] : 'System Message';
            // Custom return message.
            if (isset($data['message']))
            {
                $message = $data['message'];
                unset($data['message']);
            }
            // Available data response.
            $response = array(
                'status'     => $status,
                'code'       => $code,
                'message'    => $message,
                'data'       => $data,
                'pagination' => null
            );
            // Merge if data has anything else.
            if (isset($data['data']))
            {
                $response = array_merge($response, $data);
            }
            // Remove empty array.
            $response = array_filter($response, function($value)
            {
                return ! is_null($value);
            });
            // Remove empty data.
            if (empty($response['data']))
            	unset($response['data']);

	        // Header response.
            // return response()->json($response,$code);
	        return response($response, $code);
        }
    }
}