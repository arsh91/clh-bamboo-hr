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
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        $report_status = ReportStatus::latest()->first();
        $empty_job_field = EmployeesData::select('empty_job_field')->sum('empty_job_field');
        $empty_personal_field = EmployeesData::select('empty_personal_field')->sum('empty_personal_field');
        $empty_emergency_field = EmployeesData::select('empty_emergency_field')->sum('empty_emergency_field');
        $empty_doucument_field = DocumentData::select(DB::raw('COUNT(DISTINCT emp_id, doc_id) as count'))->get()->pluck('count')->first();


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
            $responseArr = [];
            $results = DocumentData::with('EmployeesDocs')->groupBy('emp_id','doc_id')->get()->toArray();
            foreach($results as $res){
                $responseArr[$res['doc_name']][] = $res['employees_docs']; 
            }
            return $responseArr;
        }else if($tab ==='expired'){
            $results = TimeTrackerData::whereDate('expiration', '<', $today)
            ->with('timetracker')
            // ->groupBy('emp_id')
            ->get()->toArray();
            $responseArr = [];
            foreach ($results as $resultVal) {
                $responseArr[$resultVal['type']][] = $resultVal['timetracker']; 
            }
            return $responseArr;
        }else if($tab ==='going_to_expire'){
            $date30DaysFromNow = $today->copy()->addDays(30);
            $date15DaysFromNow = $today->copy()->addDays(15);
            $results = TimeTrackerData::whereDate('expiration', '>', $today)
            ->whereDate('expiration', '<=', $date30DaysFromNow)
            ->whereDate('expiration', '>', $date15DaysFromNow)
            ->with('timetracker')
            ->get()->toArray();
            $responseArr = [];
            foreach ($results as $resultVal) {
                $responseArr[$resultVal['type']][] = $resultVal['timetracker']; 
            }
            return $responseArr;
        }else{
            $results = EmptyFieldsData::where('tab', $tab)
            ->with('EmployeesData')
            ->get()->toArray();
            $responseArr = [];
            foreach ($results as $resultVal) {
                $responseArr[$resultVal['field_name']][] = $resultVal['employees_data']; 
            }
            return $responseArr;
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
