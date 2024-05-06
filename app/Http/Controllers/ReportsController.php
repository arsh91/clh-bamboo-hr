<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\DepartmentRole;
use App\Models\Folder;
use App\Models\Reports;
use \BambooHR\API\BambooAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmployeesData;
use App\Models\TimeTrackerData;
use App\Models\EmptyFieldsData;

class ReportsController extends Controller
{
    public function index()
    {
        // $mainEmployeeTableData = EmployeesData::select()->get();
        $empty_job_field = EmployeesData::select('empty_job_field')->sum('empty_job_field');
        $empty_personal_field = EmployeesData::select('empty_personal_field')->sum('empty_personal_field');
        $empty_emergency_field = EmployeesData::select('empty_emergency_field')->sum('empty_emergency_field');
        // dd($empty_job_field);
        return view('reports.index' , compact('empty_job_field', 'empty_personal_field', 'empty_emergency_field'));
    }

    public function getEmptyFiledsDetails(Request $request)
    {
        $tab = $request->input('tab');
        $results = EmptyFieldsData::selectRaw('field_name, COUNT(*) as count')
            ->where('tab', $tab)
            ->groupBy('field_name')
            ->get();
            return $results;

    }
}
