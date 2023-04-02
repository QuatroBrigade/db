<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POIController extends Controller {

    public function poi(Request $request) {
        $x = $request->query('x');
        $y = $request->query('y');

        if(!isset($x) || !isset($y)) {
            return response(['err' => 'X and Y are both mandatory query parameters'])->setStatusCode(400);
        }

        $data = DB::table('points_of_interest')
            ->select(['points_of_interest.id', 'points_of_interest.name', 'points_of_interest.typ_0', 'points_of_interest.typ_1', 'points_of_interest.x', 'points_of_interest.y', 'points_of_interest.filter_type'])
            ->whereRaw('ST_Contains(poly_15, POINT(x,y))')
            ->get();

//        die(var_dump(json_decode($data)));
        $result = [];

        foreach($data as $row) {
            $result[] = [
                'id' => $row->id,
                'name' => $row->name,
                'typ0' => $row->typ_0,
                'typ1' => $row->typ_1,
                'lat' => $row->x,
                'lon' => $row->y,
                'filter_type' => $row->filter_type
            ];
        }

        return $result;
    }

    public function poiFilterlist() {
        return DB::select('SELECT filter_type FROM v_poi_filter_types');
    }
}
