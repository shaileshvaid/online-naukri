<?php
/**
 * App\Http\Middleware\XSSProtection
 * 
 * Middleware for xss protection and sanitization
 *
 * @package MyCMS
 * @category XSSProtection
 * @author  Anthony Pillos <dev.anthonypillos@gmail.com>
 * @copyright Copyright (c) 2017
 * @version v1
 */

namespace App\Http\Middleware;

use Closure;

class XSSProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        
        if (!in_array(strtolower($request->method()), ['put', 'post','get'])) {
            return $next($request);
        }

        $input = $request->all();

        array_walk_recursive($input, function(&$input) {
            $input = strip_tags($input);
        });
        $request->merge($input);
        return $next($request);
    }  
}