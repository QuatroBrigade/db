<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function getPostById(int $postId) {
        $data = DB::table('post')
                    ->where('post_id', $postId)
                    ->get();
        $data = $data->toArray();
        $result = [
            'id' => $data[0]->post_id,
            'userId' => $data[0]->author_id,
            'title' => $data[0]->name,
            'description' => $data[0]->description,
            'location' => [
                'lat' => $data[0]->coord_x,
                'lon' => $data[0]->coord_y
             ],
            'walkableRadius' => $data[0]->radius,
            'createdAt' => date_format(date_create($data[0]->cdate), 'c')
        ];

        return $result;
    }

    public function getWithVotes(int $postId) {
        if(!$postId) {
            return response(['err' => 'missing postId parameter'])->setStatusCode(400);
        }

        $aff = DB::table('users_votes')
            ->where(['users_votes.post_id' => $postId, 'users_votes.vote' => 1])
            ->count();

        $neg = DB::table('users_votes')
                ->where(['users_votes.post_id' => $postId, 'users_votes.vote' => 0])
                ->count();

        return [
            'aff' => $aff,
            'neg' => $neg
        ];
    }

    public function newPost(Request $request) {
        $body = $request->getContent();

        $data = json_decode($body, true);

        $data = $data['post'] ?? NULL;

        if(!$data) {
            var_dump($data);
            return response(['err' => 'post data wasnt loaded'])->setStatusCode(400);
        }

        $userData = DB::table('user')
            ->join('rights', 'rights.rights_id', '=', 'user.rights_id')
            ->where('user.user_id', $data['userId'])
            ->get();
        $userData = $userData->toArray();
        $user = $userData[0];

        try {
            $postId = DB::table('post')->insertGetId([
                'name' => $data['title'],
                'description' => $data['description'],
                'coord_x' => $data['location'][0],
                'coord_y' => $data['location'][1],
                'cdate' => date('Y-m-d H:i:s', time()),
                'mdate' => date('Y-m-d H:i:s', time()),
                'author_id' => $data['userId']
            ]);

            if ($postId && $data['communityId']) {
                $joinTableSucc = DB::table('communities_posts')->insert([
                    'post_id' => $postId,
                    'community_id' => $data['communityId'],
                    'is_promoted' => ($user->name === 'Predseda')
                ]);
            }
        } catch (\Exception $e) {
            return response(['err' => 'post data wasnt loaded. err:'.$e->getMessage()])->setStatusCode(400);
        }

        return true;
    }

    public function postPromote(Request $request, int $postId) {
        $communityId = $request->get('communityId');
        $promote = $request->get('promote');

        if(!isset($communityId) || !isset($promote)) {
            return response(['err' => 'communityId and promote are both required parameters!'])->setStatusCode(400);
        }

        $aff = DB::table('communities_posts')
            ->where([
                'community_id' => $communityId,
                'post_id' => $postId
            ])
            ->update(['is_promoted' => $promote]);

        return $aff;

    }

    public function userPostVote(Request $request, $postId, $userId) {
        $decision = $request->get('decision');

        DB::table('users_votes')
            ->updateOrInsert([
                ['post_id' => $postId, 'user_id' => $userId],
                ['vote' => $decision]
            ]);

        return [
            'postId' => $postId,
            'userId' => $userId,
            'vote' => $decision
        ];
    }
}
