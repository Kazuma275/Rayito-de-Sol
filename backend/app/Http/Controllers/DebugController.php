<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class DebugController extends Controller
{
    // GET /log-test
    public function logTest()
    {
        Log::info('¡Laravel está escribiendo en el log!');
        return response('ok', 200);
    }

    // GET /debug-users
    public function debugUsers()
    {
        $users = User::select('id', 'email', 'reset_password_token', 'reset_password_expires_at')->get();
        return response()->json($users);
    }

    // GET /debug-token-search/{token}
    public function debugTokenSearch($token)
    {
        $user = User::where('reset_password_token', $token)->first();

        return response()->json([
            'token_searched' => $token,
            'user_found' => (bool) $user,
            'user_data' => $user ? [
                'id' => $user->id,
                'email' => $user->email,
                'reset_password_token' => $user->reset_password_token,
                'reset_password_expires_at' => $user->reset_password_expires_at,
            ] : null,
            'all_users_with_tokens' => User::whereNotNull('reset_password_token')
                ->select('id', 'email', 'reset_password_token')
                ->get()
        ]);
    }
}
