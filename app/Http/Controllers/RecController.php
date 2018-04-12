<?php

namespace App\Http\Controllers;

use App\market_structure;
use Illuminate\Http\Request;

class RecController extends Controller
{
    //
    public function getRec(Request $request)
    {
        $q = $request->get('q');
        return market_structure::where('name', 'like', "%$q%")->paginate(null, ['cid as id', 'name as text']);
    }
}
