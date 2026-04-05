<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureVendorHasProfile
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->isVendor() && !$user->vendor) {
            return redirect()->route('vendor.setup')
                ->with('info', 'Lengkapi profil kantin Anda terlebih dahulu.');
        }

        return $next($request);
    }
}