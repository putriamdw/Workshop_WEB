<?php

namespace App\Http\Middleware;

use Closure; // meneruskan request ke proses berikutnya
use Illuminate\Http\Request; // menangani data request
use Illuminate\Support\Facades\Auth; // cek user yang sedang login

class RoleMiddleware    
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) { // cek apakah ada user yang sedang login
            return redirect('/login');
        }

        if (Auth::user()->role !== $role) { // ambil data user yang login
            abort(403, 'Akses ditolak!');
        }

        return $next($request); // diteruskan ke controller
    }
}
