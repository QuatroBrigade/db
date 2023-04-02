<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    public function getCommunityPost(Request $request) {
        $communityId = $request->query('communityId');
        $limit = $request->query('limit');
        $offset = $request->query('offset');
        $onlyIsPromoted = $request->query('onlyIsPromoted');

        if(!$communityId) {
            return response(['message' => 'missing communityId query parameter'])->setStatusCode(400);
        }

        $data = DB::table('post')
            ->join('communities_posts', 'communities_posts.post_id', '=', 'post.post_id')
            ->where("communities_posts.community_id", $communityId);

        if($limit) {
            $data->limit($limit);
        }
        if($offset) {
            $data->offset($offset);
        }
        if($onlyIsPromoted) {
            $data->where('communities_posts.is_promoted', 1);
        }

        $result = [];
        foreach($data->get() as $row) {
            $result[] = [
                'id' => $row->post_id,
                'userId' => $row->author_id,
                'title' => $row->name,
                'description' => $row->description,
                'location' => [
                    'lat' => $row->coord_x,
                    'lon' => $row->coord_y
                ],
                'walkableRadius' => $row->radius,
                'createdAt' =>  date_format(date_create($row->cdate), 'c'),
                'isPromoted' => (bool)($row->is_promoted)
            ];
        }

        return $result;
    }

    public function getCommunities(Request $request) {
        $limit = $request->query('limit');
        $offset = $request->query('offset');

        $data = DB::table('community');
        if($limit) {
            $data->limit($limit);
        }
        if($offset) {
            $data->offset($offset);
        }

        $result = [];
        foreach($data->get() as $row) {
            $result[] = [
                'id' => $row->community_id,
                'name' => $row->name,
                'comunityParentId' => $row->community_parent_id,
                'level' => $row->level
            ];
        }

        return $result;
    }

    public function getCommunityById(int $communityId) {
        $data = DB::table('community')
            ->where('community.community_id', $communityId)->get();

        $data = $data->toArray();

        if(count($data) < 1) {
            return [];
        }
        $result = [
            'id' => $data[0]->community_id,
            'name' => $data[0]->name,
            'comunityParentId' => $data[0]->community_parent_id,
            'level' => $data[0]->level
        ];

        return $result;
    }
}
