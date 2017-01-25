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

        if (is_null($referer) OR env('APP_ENV', 'production') !== 'production') {
            return $next($request);
        }

        // Spammer detected?
        if ($this->requestIsFromASpammer($referer)) {
            abort(500, 'Nope!');
        }

        // No spam, we can continue :)
        return $next($request);
    }

    /**
     * Get the spammers list.
     *
     * @return array
     */
    protected function getSpammersList()
    {
        $minutes_in_a_day = 1440;

        return \Cache::remember('spammers.list', $seconds_in_a_day, function() {
            $dir  = realpath( dirname(__FILE__) . '/../../../../../' ) . '/';
            $path = $dir . 'spammers.json';
            $file = file_get_contents($path);

            if (empty($file)) {
                return [];
            }

            $dictionary = json_decode($file);

            return $dictionary;
        });
    }

    /**
     * Get the host based on the referer.
     *
     * @param string $referer
     * @return string
     */
    protected function getHost($referer)
    {
        $url  = new Parser(new PublicSuffixList([]));
        $host = $url->parseHost($referer)->host;

        return $host;
    }

    /**
     * Check for a nope request.
     *
     * @param string $referer
     * @return bool
     */
    protected function requestIsFromASpammer($referer)
    {
        // Get the dictionary
        $dictionary = $this->getSpammersList();

        // Parse the referer
        $host = $this->getHost($referer);

        // Compare dictionary against the referer...
        $search = Arr::where($dictionary, function($key, $value) use ($host) {
            return mb_stripos($host, $value) !== false;
        });

        // ...and check for results
        if (count($search) > 0) {
            return true;
        }

        return false;
    }
}
