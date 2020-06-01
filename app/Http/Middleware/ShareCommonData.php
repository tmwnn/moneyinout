<?php
/**
 * Description of ShareCommonData.php
 */

namespace App\Http\Middleware;

use View;
use Illuminate\Http\Request;

class ShareCommonData
{

    public function handle(Request $request, \Closure $next)
    {
        /*
        View::share([
           'locale' => \App::getLocale(),
        ]);
        */
        return $next($request);
    }

}
