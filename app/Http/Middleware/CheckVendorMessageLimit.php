<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\VendorUsage;

class CheckVendorMessageLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $vendor = auth()->user()->vendor;

        $subscription = $vendor->activeSubscription;

        if (!$subscription) {

            abort(
                403,
                'No active subscription.'
            );
        }

        $plan = $subscription->plan;

        $usage = VendorUsage::firstOrCreate([
                'vendor_id' => $vendor->id,
                'month' => now()->format('Y-m')
            ]);

        if (

            $usage->messages_sent >= $plan->monthly_message_limit

        ) {

            abort(403, 'Monthly message limit exceeded.');
        }

        return $next($request);
    }
}

