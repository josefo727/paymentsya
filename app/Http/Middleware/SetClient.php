<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;

class SetClient
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $account = $request->header('account') ?? null;
        $appKey = $request->header('X-VTEX-API-AppKey') ?? null;
        $appToken = $request->header('X-VTEX-API-AppToken') ?? null;

        $client = app(Client::class)->getClientByCredentials($account, $appKey, $appToken);

        if (is_null($client) || !$client->credential()->exists()) {
            return response()->error('No autorizado', 401);
        }

        $client->credential->setCredentials();

        return $next($request);
    }
}
