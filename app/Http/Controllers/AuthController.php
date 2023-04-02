<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function authenticate(Request $request) {
        $userSess = $request->get('user_session');
        $userId = $request->get('user_id');

        $dbRes = DB::select("select user_id from auth_user where user_session = ?", [$userSess]);

        return json_encode(['isCorrect' => (count($dbRes) > 0),'user_id' => $userId]);
    }
}
