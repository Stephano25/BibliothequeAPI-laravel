<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Borrow;

class CheckBorrowLimit
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        }

        if ($user->subscription_status === 'free') {
            $activeBorrows = Borrow::where('user_name', $user->name)
                ->whereNull('returned_at')
                ->count();

            if ($activeBorrows >= 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Limite de 2 emprunts atteinte'
                ], 403);
            }
        }

        return $next($request);
    }
}