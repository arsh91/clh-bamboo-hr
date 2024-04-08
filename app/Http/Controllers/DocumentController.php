<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BambooHrService;
use \BambooHR\API\BambooAPI;

class DocumentController extends Controller
{
    protected $bambooHrService;

    public function __construct(BambooHrService $bambooHrService)
    {
        $this->bambooHrService = $bambooHrService;
    }
    public function listEmplyeeDocuments($empId)
    {

        $employeeDetails = $this->getEmployeeDetailByID($empId);
        $empData = $employeeDetails['empData'];
        $base64Image = $employeeDetails['base64Image'];
        //dump('we will show documents here'); dd('-----  ');


        //will run the api to get the files of an employee
        $bhr = new BambooAPI("clhmentalhealth");
        $bhr->setSecretKey("40d056dd98d048b1d50c46392c77bd2bbbf0431f");
        $listEmployeeFiles = $bhr->listEmployeeFiles($empId);
        if ($listEmployeeFiles->isError()) {
            trigger_error("Error communicating with BambooHR: " . $listEmployeeFiles->getErrorMessage());
        }

        $listEmployeeFiles = $listEmployeeFiles->getContent();
        $listEmployeeFiles = json_encode($listEmployeeFiles);       
        $listEmployeeFiles = json_decode($listEmployeeFiles, true);

        $commonDocumentArray = array();
        
        if(count($listEmployeeFiles) > 0){     
            if (isset($listEmployeeFiles['category'])) {
                foreach($listEmployeeFiles['category'] as $key=> $documents){
                        $documentID = $this->getDocumentIdAndName($documents);
                        //dump($this->getDocumentIdAndName($documents));dd('-----');
                }
            }
        }

        //CASE I: WE WILL SHOW EMPLOYEES DOCUMENTS ACC TO THEIR ROLE

        //dump($listEmployeeFiles);
        dd('---');

        return view('documents.listEmplyeeDocuments', compact('empData', 'base64Image'));
    }

    private function departmentWiseDocument($empId){
        $empData = $this->getEmployeeDetailByID($empId);
        $empDivision = $empData['division'];
        $empDepartment = $empData['department'];
        $empJobInfo = $empData['jobTitle'];

        $documentIds = [];
        if($empDepartment == env('GROUP_HOME')){ //if department is `Residential Group Home` 
            if($empJobInfo == env('JOBINFO_GROUP_HOME_CHILD_YOUTH')){ //Group Home Residential Child Youth Care Practitioner
                $documentIds = [54,42,43];
            }else if($empJobInfo == env('JOBINFO_GROUP_HOME_YOUTH')){ //Group Home Youth	
                $documentIds = [];
            }
        }
    }

    private function getDocumentIdAndName($arrayObj){
        $docIdAndName = [];
        if (is_array($arrayObj)) {
            foreach($arrayObj as $key =>$val){
                if(is_array($val)){
                    $docIdAndName['docId'] = $val['id'];
                }else{
                    if($key == 'name'){
                        $docIdAndName['docName'] = $val;
                    }                    
                }
            }
            //$docID = $arrayObj['id'];
        } 
        return $docIdAndName;
    }
    


    private function getEmployeeDetailByID($empId)
    {
        $empFieldsArray = array('firstName,lastName,jobTitle,workPhone,mobilePhone,workEmail,department,location,division,supervisor,employmentStatus');

        $bhr = new BambooAPI("clhmentalhealth");
        $bhr->setSecretKey("40d056dd98d048b1d50c46392c77bd2bbbf0431f");
        //$response = $bhr->getDirectory();
        $getEmployee = $bhr->getEmployee($empId, $empFieldsArray);
        if ($getEmployee->isError()) {
            trigger_error("Error communicating with BambooHR: " . $getEmployee->getErrorMessage());
        }

        $getEmployeeData = $getEmployee->getContent();
        $params = 'firstName,lastName,jobTitle,workPhone,mobilePhone,workEmail,department,location,division,supervisor,employmentStatus';

        $employeeData = json_encode($getEmployeeData);
        $dataArray = json_decode($employeeData, true);
        $empKeyArr = explode(',', $params);

        $empData = [];
        foreach ($dataArray['field'] as $key => $field) {
            $empData[$empKeyArr[$key]] = $this->checkIfArray($field);
        }
        $empData['ID'] = $empId;

        //FOR EMPLOYEE IMAGE
        $imgResponse = $bhr->downloadEmployeePhoto($empId, 'small', array(["width" => 100], ["height" => 100]));
        $imgResponse = $imgResponse->getContent();

        if ($imgResponse !== false &&  $imgResponse != '') {
            // Get the MIME type of the image
            $imageInfo = getimagesizefromstring($imgResponse);
            $imageMimeType = $imageInfo['mime'];

            // Generate a base64 encoded string of the image
            $base64Image = 'data:' . $imageMimeType . ';base64,' . base64_encode($imgResponse);

            return ['empData' => $empData, 'base64Image' => $base64Image];
        }
    }

    private function checkIfArray($arrayObj)
    {
        $finalVal = '';
        if (!is_array($arrayObj)) {
            $finalVal = $arrayObj;
        } else {
            $finalVal = 'N/A';
        }
        return $finalVal;
    }
}
