<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\DepartmentRole;
use App\Models\Folder;
use App\Models\Reports;
use \BambooHR\API\BambooAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function index()
    {
        $employeeFieldsIndexes = array(
            'ID'=>17,
            'photo'=>15,
            'firstname' => 1,
            'lastname' => 2,
            'designation' => 4,
            'email' => 7,
            'department'=>8,
            'manager'=>13,
            'jobTitle'=>4,
            'division'=>10,            
        );

        //Count of Users Without Super Admin
        $usersCount = User::whereHas('role', function($q) {
            $q->where('name', '!=', 'SUPER_ADMIN');
        })->count();

        $apiKey = '40d056dd98d048b1d50c46392c77bd2bbbf0431f';
        $endpoint = 'https://api.bamboohr.com/api/gateway.php/clhmentalhealth/v1/employees/directory';
    
        $response = file_get_contents($endpoint, false, stream_context_create([
            'http' => [
                'header' => "Authorization: Basic " . base64_encode($apiKey . ':x')
            ]
        ]));
    
        $xml = simplexml_load_string($response);

        // Convert the XML object to an associative array
        $employees = json_encode($xml);  
        
      
        $dataArray = json_decode($employees, true);
         
        $empMainArr = [];
        $employeeFields = $dataArray['fieldset'];
       
        $employeeFields = $employeeFields['field'];
        //dump($dataArray); dd();
        $employees = $dataArray['employees'];
        $employees = $employees['employee'];
        
        $empMainArr = array();
        $empIdsArr = array();
        $i = 0;
        foreach($employees as $empKeys=> $emp){
            $empID = $emp['@attributes']['id'];
            $empMainArr[$i]['ID'] = $empID;
            $empMainArr[$i]['photo'] = $this->checkIfImageExists($emp['field'][$employeeFieldsIndexes['photo']]);
            $empMainArr[$i]['firstname'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['firstname']]);
            $empMainArr[$i]['lastname'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['lastname']]);
            $empMainArr[$i]['designation'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['designation']]);
            $empMainArr[$i]['email'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['email']]);
            $empMainArr[$i]['department'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['department']]);
            $empMainArr[$i]['manager'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['manager']]);
            $empMainArr[$i]['jobTitle'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['jobTitle']]);
            $empMainArr[$i]['division'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['division']]);
            //$empMainArr[$i]['profile'] = 'https://clhmentalhealth.bamboohr.com/employees/employee.php?id='.$empID;
            $empIdsAr[] = $empID;
            $i++;

            
            // foreach($employeeFields as $key=>$fieldName){
                
            //     //CASE: in some fields it's further a blank array if anything is not provided against these keys
            //     if(!is_array($emp['field'][$key])){
            //         $empMainArr[$empKeys][$key] = $emp['field'][$key];
            //         //CASE: We will check if any array key contains some value of image
            //         if (strpos($emp['field'][$key], "https://images") === 0 || strpos($emp['field'][$key], "https://resources") === 0) {                        
            //            // echo $emp['field'][$key];
            //            $empMainArr[$empKeys][$key] = '<img src="'.$emp['field'][$key].'" alt="Photo URL">';
            //         }
                    
            //     }else{
            //         $empMainArr[$empKeys][$key] = 'N/A';
            //     }
                
            // }
            
        }  
        $latestReport = Reports::latest()->first();
        $empIdsAr = json_encode($empIdsAr);     
        return view('dashboard.index',compact('usersCount', 'empMainArr', 'employeeFieldsIndexes', 'empIdsAr', 'latestReport'));
    }

    public function employeDetail($empId){

        $apiKey = '40d056dd98d048b1d50c46392c77bd2bbbf0431f';
        $params = 'firstName,lastName,jobTitle,workPhone,mobilePhone,workEmail,department,location,division,supervisor,photoUrl,canUploadPhoto';
        $endpoint = 'https://40d056dd98d048b1d50c46392c77bd2bbbf0431f:x@api.bamboohr.com/api/gateway.php/clhmentalhealth/v1/employees/'.$empId.'/?fields='.$params.'&format=JSON';
    
        $response = file_get_contents($endpoint, false, stream_context_create([
            'http' => [
                'header' => "Authorization: Basic " . base64_encode($apiKey . ':x')
            ]
        ]));

        //call employee image api
        $imageEndpoint = 'https://40d056dd98d048b1d50c46392c77bd2bbbf0431f:x@api.bamboohr.com/api/gateway.php/clhmentalhealth/v1/employees/'.$empId.'/photo/small';
        $headers = [
            'Authorization: Basic ' . base64_encode($apiKey . ':x'),
            'Content-Type: image/jpeg'
            // Add more headers as needed
        ];
        $imgResponse = file_get_contents($imageEndpoint, false, stream_context_create([
            'http' => [
                'header' => implode("\r\n", $headers),
            ]
        ]));


        if ($imgResponse !== false) {
            // Get the MIME type of the image
            $imageInfo = getimagesizefromstring($imgResponse);
            $imageMimeType = $imageInfo['mime'];
        
            // Generate a base64 encoded string of the image
            $base64Image = 'data:' . $imageMimeType . ';base64,' . base64_encode($imgResponse);
        }

//dump($base64Image);dd('--');
  //      $imgXml = simplexml_load_string($imgResponse);
    //    $employeeImgData = json_encode($imgXml);       
      //  $dataImgArray = json_decode($employeeImgData, true);
        //print_r($dataImgArray); dd('-');
    
        $xml = simplexml_load_string($response);
        $employeeData = json_encode($xml);       
        $dataArray = json_decode($employeeData, true);
        $empKeyArr = explode(',', $params);
        $empData = [];
        foreach($dataArray['field'] as $key=> $field){
            $empData[$empKeyArr[$key]]= $this->checkIfArray($field);            
        }
        $empData['ID'] = $empId;
        //dump($empData);
        // dd('--');
        return view('dashboard.employee',compact('empData', 'base64Image'));
        
    }

    private function checkIfArray($arrayObj)
    {
        $finalVal = '';
        if(!is_array($arrayObj)){
            $finalVal = $arrayObj;
        }else{
            $finalVal = 'N/A';
        }
        return $finalVal;
    }

    private function checkIfImageExists($arrayObj){
        $finalImage = '';
        if(!is_array($arrayObj)){
            if (strpos($arrayObj, "https://images") === 0 || strpos($arrayObj, "https://resources") === 0) {                        
                
                $finalImage = '<img src="'.$arrayObj.'" alt="Photo URL">';
            }
        }else{
            $finalImage = 'N/A';

        }
        return $finalImage;
    }

    public function startCreatingReport(){
        $report = Reports::create([
            'status' => 'requested'
        ]);
        return response()->json(['status' => $report->status]);
    }

    public function folder(Request $request){
        $bhr = new BambooAPI(env('YOUR_COMPANY_ID'));
        $bhr->setSecretKey(env('YOUR_API_KEY'));
        $data = $bhr->getReport('168', 'csv', true );
        if ($data->isError()) {
            $request->session()->flash('error','Some error occured while connecting with Bamboo HR.');
            return redirect()->back();
        }
        $rows = Str::of($data->content)->trim()->explode("\n")->map(function ($row) {
            return str_getcsv($row);
        })->toArray();
        $rows = Str::of($data->content)
        ->trim()
        ->explode("\n")
        ->skip(1) 
        ->map(function ($row) {
            return str_getcsv($row);
        })
        ->filter(function ($row) {
            // Filter out rows where all three columns are empty
            return (!empty($row[0]) || !empty($row[1]) || !empty($row[2])) && !empty($row[0]);
        })
        ->toArray();    
        $departmentArray = [];
        foreach ($rows as $row) {
            $department = $row[0];
            $job = $row[1] ?? null;
            $division = $row[2] ?? null;
        
            if (isset($departmentArray[$department])) {
                if (!in_array($job, $departmentArray[$department]['job'])) {
                 $departmentArray[$department]['job'][] = $job;
                $departmentArray[$department]['division'][] = $division;
                }
              
            } else {
                $departmentArray[$department] = [
                    'job' => [$job],
                    'division' => [$division],
                ];
            }
        }
        // dd($departmentArray);
        $getAllEmpFolders = $bhr->listEmployeeFiles(env('LATRILL_EMP_ID') );
        
        $listEmployeeFiles = $getAllEmpFolders->getContent();
        $listEmployeeFiles = json_encode($listEmployeeFiles);       
        $listEmployeeFiles = json_decode($listEmployeeFiles, true);

       $employeeAllDocumentsArr = [];
       if(count($listEmployeeFiles) > 0){     
            if (isset($listEmployeeFiles['category'])) {
                foreach($listEmployeeFiles['category'] as $key=> $documents){
                    $employeeAllDocumentsArr[]=$this->getDocumentIdAndName($documents); //coming from api
                }
                
            }
        }

        // dd($employeeAllDocumentsArr);
        return view('documents.addDocuments', compact('departmentArray', 'listEmployeeFiles', 'employeeAllDocumentsArr'));
    }


    /**
     * get the name of employee folder and id from 'document tab'
     */
    private function getDocumentIdAndName($arrayObj)
    {
        $docIdAndName = [];
        
        if (is_array($arrayObj)) {
             
            foreach($arrayObj as $key =>$val){
                
                if(is_array($val)){
                    if(array_key_exists('id', $val)){
                        $docIdAndName['docId'] = $val['id'];        
                    }
                    
                }else{
                    if($key == 'name'){
                        $docIdAndName['docName'] = $val;
                    }                    
                }
            }
        }
        return $docIdAndName;
    }
    
    public function getSavedFolder(Request $request)
    {
        $job = $request->input('job');
        $department = $request->input('department');
        
            $departmentRole = DepartmentRole::where([
                    'role' => $job,
                    'department' => $department
                ])
                ->with('folder')
                ->get();
            return $departmentRole;
    }

    public function saveFolder(Request $request)
    {
        $job = $request->input('job');
        $department = $request->input('department');
        $folder = $request->input('folder');
        // $folder = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155, 159, 23];
        if(count($folder) > 0){
            $getDepartmentRole = DepartmentRole::where([
                'role' => $job,
                'department' => $department
            ])
            ->with('folder')
            ->get();

            $folderValues = [];
            if(count($getDepartmentRole) < 1){
                $departmentRole = DepartmentRole::create([
                    'role' => $job,
                    'department' => $department,
                ]);
                $insertedId = $departmentRole->id;
            }else{

                $insertedId = $getDepartmentRole[0]->id;
                foreach ($getDepartmentRole[0]->folder as $folderData) {
                    $folderColumnValue = $folderData->folder_id;
                    $folderValues[] = $folderColumnValue;
                }

                $deleteFolder = array_diff($folderValues, $folder);
                // dd($deleteFolder);

                if(count($deleteFolder) > 0){
                    foreach ($deleteFolder as $delete_id) {
                        Folder::where('folder_id', $delete_id)
                        ->where('department_role', $insertedId)
                        ->delete();
                    }
                }
            }
        
            if(count($folder) > 0){
                foreach ($folder as $folder_id) {
                    $departmentRole = Folder::firstOrCreate([
                        'department_role' => $insertedId,
                        'folder_id' => $folder_id
                    ]);
                }
            }
        }

        $request->session()->flash('message', 'Sucessfully Saved.');
        return response()->json(['success' => true]);
    }
}
