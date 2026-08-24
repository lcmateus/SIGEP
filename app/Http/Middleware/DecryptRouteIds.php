<?php

namespace App\Http\Middleware;

use App\Support\EncryptedId;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DecryptRouteIds
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($route = $request->route()) {
            foreach ($route->parametersWithoutNulls() as $name => $parameter) {
                if (is_string($parameter) && ($decrypted = EncryptedId::decrypt($parameter)) !== null) {
                    $route->setParameter($name, $decrypted);
                }
            }
        }

        return $next($request);
    }
}
