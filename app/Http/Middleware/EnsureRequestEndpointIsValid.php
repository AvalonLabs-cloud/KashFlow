<?php

namespace App\Http\Middleware;

use App\Utils\TransactionTypeEndPointMap;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use App\Domains\Transfer\Utility\Utility;

class EnsureRequestEndpointIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $transactionType = $request->input('transactionType');

            $endpointMap = Utility::TransactionTypeEndPointMap();

            $expectedEndpoint = $endpointMap[$transactionType];

            // Get the endpoint/path the request is currently navigating to.
            $currentEndpoint = $request->getPathInfo();

            if ($currentEndpoint !== $expectedEndpoint) {
                throw new Exception(
                    'The transaction type does not match the requested endpoint.'
                );
            }

            return $next($request);

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}