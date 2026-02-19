<?php

// app/Http/Middleware/UserTypeMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserTypeMiddleware
{
    public function handle(Request $request, Closure $next, ...$types)
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if (!in_array($user->user_type, $types)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
