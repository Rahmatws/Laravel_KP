<?php

namespace App\Http\Middleware;

use App\Models\Item;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOnboardingStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Keduanya Harus melewati onboarding
        if ($user && ($user->role === 'admin' || $user->role === 'staff')) {
            // Priority Check: If database is effectively empty, force import
            // This handles cases where data was wiped but user flags remain set
            if (Item::count() === 0) {
                if (
                    !$request->routeIs('kp.import') &&
                    !$request->routeIs('kp.import.post') &&
                    !$request->routeIs('kp.import.preview') &&
                    !$request->routeIs('kp.import.skip') &&
                    !$request->routeIs('kp.logout')
                ) {
                    return redirect()->route('kp.import')
                        ->with('error', 'Data stok kosong. Harap lakukan import data terlebih dahulu.');
                }
            }

            // Step 1: Import CSV SID
            if (!$user->has_imported) {
                // Only allow access to import routes and logout
                if (
                    !$request->routeIs('kp.import') &&
                    !$request->routeIs('kp.import.post') &&
                    !$request->routeIs('kp.import.preview') &&
                    !$request->routeIs('kp.import.skip') &&
                    !$request->routeIs('kp.logout')
                ) {
                    return redirect()->route('kp.import')
                        ->with('info', 'Silakan import data CSV SID atau klik Lewati.');
                }
            }
            // Step 2: Detail Barang
            elseif (!$user->has_viewed_details) {
                // Only allow access to detail_barang routes and logout
                if (
                    !$request->routeIs('kp.detail_barang') &&
                    !$request->routeIs('kp.detail_barang.complete') &&
                    !$request->routeIs('kp.logout')
                ) {
                    return redirect()->route('kp.detail_barang')
                        ->with('info', 'Silakan lihat detail barang hasil import.');
                }
            }
            // Step 3: Daftar Stok
            elseif (!$user->has_viewed_stock) {
                // Only allow access to daftar_stok routes and logout
                if (
                    !$request->routeIs('kp.daftar_stok') &&
                    !$request->routeIs('kp.daftar_stok.complete') &&
                    !$request->routeIs('kp.logout')
                ) {
                    return redirect()->route('kp.daftar_stok')
                        ->with('info', 'Silakan lihat daftar stok untuk menyelesaikan setup.');
                }
            }
        }

        return $next($request);
    }
}
