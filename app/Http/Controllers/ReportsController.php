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
use App\Models\DocumentData;
use App\Models\EmptyFieldsData;
use App\Models\ReportStatus;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        $report_status = ReportStatus::latest()->first();
        $empty_job_field = EmployeesData::select('empty_job_field')->sum('empty_job_field');
        $empty_personal_field = EmployeesData::select('empty_personal_field')->sum('empty_personal_field');
        $empty_emergency_field = EmployeesData::select('empty_emergency_field')->sum('empty_emergency_field');
        $empty_doucument_field = DocumentData::count();
        
        $today = Carbon::today()->startOfDay();
        $date30DaysFromNow = $today->copy()->addDays(30);
        $date15DaysFromNow = $today->copy()->addDays(15);
        $going_to_expire = TimeTrackerData::whereDate('expiration', '>', $today)
        ->whereDate('expiration', '<=', $date30DaysFromNow)
        ->whereDate('expiration', '>', $date15DaysFromNow)->count();
        $total = TimeTrackerData::count();
        $expired = TimeTrackerData::whereDate('expiration', '<', $today)->count();
        return view('reports.index' , compact('empty_job_field', 'empty_personal_field', 'empty_emergency_field', 'empty_doucument_field', 'going_to_expire', 'expired', 'report_status', 'total'));
    }

    public function getEmptyFiledsDetails(Request $request)
    {
        $results = [];
        $tab = $request->input('tab');
        $today = Carbon::today()->startOfDay();
        if($tab ===  'document'){
            $results = DocumentData::selectRaw('doc_name as name, COUNT(*) as count')
            ->groupBy('doc_name')
            ->get();
        }else if($tab ==='expired'){
            $results = TimeTrackerData::selectRaw('type as name, COUNT(*) as count')
            ->whereDate('expiration', '>', $today)
            ->groupBy('type')
            ->get();
        }else if($tab ==='going_to_expire'){
            $date30DaysFromNow = $today->copy()->addDays(30);
            $date15DaysFromNow = $today->copy()->addDays(15);
            $results = TimeTrackerData::selectRaw('type as name, COUNT(*) as count')
            ->whereDate('expiration', '>', $today)
            ->whereDate('expiration', '<=', $date30DaysFromNow)
            ->whereDate('expiration', '>', $date15DaysFromNow)
            ->groupBy('type')
            ->get();
        }else{
            $results = EmptyFieldsData::selectRaw('field_name as name, COUNT(*) as count')
            ->where('tab', $tab)
            ->groupBy('field_name')
            ->get();
        }
        return $results;
    }
    public function generateData()
    {
        $newRecord = new ReportStatus();
        $newRecord->date = Carbon::now();
        $newRecord->status = 'requested';
        $newRecord->save();
    }
}
