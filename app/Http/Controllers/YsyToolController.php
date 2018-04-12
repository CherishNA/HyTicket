<?php

namespace App\Http\Controllers;

use App\YsyClassModel;
use App\YsyDailyModel;
use App\YsyProject;
use App\YsySubjectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YsyToolController extends Controller
{
    public function getcls(Request $request)
    {
        return YsyClassModel::all('id', DB::raw('clsname as text'));
    }

    public function getsub(Request $request)
    {
        return YsySubjectModel::all('id', DB::raw('subname as text'));
    }

    public function getdai(Request $request)
    {
        return YsyDailyModel::all('id', DB::raw('dailyname as text'));
    }
    public function getpro(Request $request)
    {
        return YsyProject::all('id', DB::raw('project_name as text'));
    }


}
