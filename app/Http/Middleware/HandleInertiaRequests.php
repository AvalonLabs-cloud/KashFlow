<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
 use Illuminate\Support\Facades\Log;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
    *
     * @return array<string, mixed>
     */
public function share(Request $request): array
{
    return [
        ...parent::share($request),

        'name' => config('app.name'),

        'auth' => [
            'user' => $request->user(),
        ],

        'flash' => [
            'error' => $request->session()->get('error'),
            'message' => $request->session()->get('message'),
            'profile_updated' => $request->session()->get('profile_updated'),
            'successfull_account_creation' => $request->session()->get('successfull_account_creation'),
            'transaction_data' => $request->session()->get('transaction_data'),
        ],

        'sidebarOpen' => ! $request->hasCookie('sidebar_state')
            || $request->cookie('sidebar_state') === 'true',
    ];
}
}
