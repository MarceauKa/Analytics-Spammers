<?php

namespace Akibatech\Spammers\Laravel\Http\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Pdp\Parser;
use Pdp\PublicSuffixList;

/**
 * This middleware protects your app from spammers based on a dictionary.
 *
 * Class CheckForSpammers
 * @package Akibatech\Spammers\Laravel\Http\Middleware
 */
class CheckForSpammers
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $referer = $request->server->get('referer');

        // Referer is not provided, we continue
        if (is_null($referer) OR env('APP_ENV', 'production') !== 'production') {
            return $next($request);
        }

        // Handle the dictionnary.
        // @todo Refactor that
        $dir  = realpath( dirname(__FILE__) . '/../../../../../' ) . '/';
        $path = $dir . 'spammers.json';
        $file = file_get_contents($path);

        // Dictionnary is not readable, abort.
        if (empty($file)) {
            abort(500, 'Unable to read spammers.json');
        }

        $dictionary = json_decode($file);

        // Parse the referer
        $url  = new Parser(new PublicSuffixList([]));
        $host = $url->parseHost($referer)->host;

        // Compare dictionary against the referer...
        $search = Arr::where($dictionary, function($key, $value) use ($host) {
            return mb_stripos($host, $value) !== false;
        });

        // ...and check for results
        if (count($search) > 0) {
            abort(500, 'Spammers protection');
        }

        // No spam, we can continue :)
        return $next($request);
    }
}
