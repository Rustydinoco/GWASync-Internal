<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  array<string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login')->withErrors(['Anda harus login terlebih dahulu.']);
        }

        $userRole = auth()->user()->role ?? null;

        if (!in_array($userRole, $roles)) {
            if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
                return response()->json(['message' => 'Akses ditolak untuk role Anda.'], 403);
            }

            return redirect()->back()->withErrors(['Maaf, Anda tidak memiliki akses ke halaman/fitur ini.']);
        }

        return $next($request);
    }
}
