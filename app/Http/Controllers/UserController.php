<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function newUser(Request $request) {
        $content = $request->getContent();

        $data = json_decode($content, true);

        $data = $data['user'] ?? NULL;

        if(!$data) {
            return response(['err' => 'post data wasnt loaded'])->setStatusCode(400);
        }

        die(var_dump($data));
    }

    public function getUserById(int $userId) {
        $data = DB::table('user')
            ->join('rights', 'rights.rights_id', '=', 'user.rights_id')
            ->where('user.user_id', $userId)
            ->get();

        $data = $data->toArray();
        if(count($data) < 1) {
            return [];
        }

//        die(var_dump($data));
        return response()->json([
            'firstname' => $data[0]->first_name,
            'lastname' => $data[0]->last_name,
            'rola' => $data[0]->name
        ]);
    }
}
