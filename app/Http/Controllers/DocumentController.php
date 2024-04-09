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
        $matchedDocsAccToRole = '';
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
        //dump($listEmployeeFiles);
        $employeeAllDocumentsArr = [];
        if(count($listEmployeeFiles) > 0){     
            if (isset($listEmployeeFiles['category'])) {
                foreach($listEmployeeFiles['category'] as $key=> $documents){
                       // $documentID = $this->getDocumentIdAndName($documents);
                       $employeeAllDocumentsArr[]=$this->getDocumentIdAndName($documents); //coming from api
                       //dump($this->getDocumentIdAndName($documents));
                }

                //now we will show the document acc to emp role and jobtitle
                $employeeDocumentIdsAccToRole = $this->departmentWiseDocument($empId);
                $matchedDocsAccToRole = $this->searchMatchDocKey($employeeAllDocumentsArr, $employeeDocumentIdsAccToRole);
                //dump($matchedDocsAccToRole);
            }
        }

        $ListEmployeeFilesAndCategories = $this->listEmployeeFilesAndCategories($empId,$matchedDocsAccToRole);
        // dump($ListEmployeeFilesAndCategories); dd('-----');
        
        return view('documents.listEmplyeeDocuments', compact('empData', 'base64Image', 'ListEmployeeFilesAndCategories'));
    }

    /**WE WILL RUN THE BAMBOO HR API WHICH WILL ALLOW US TO DOWNLOAD THE FILES USING A FILE LINK
     * BASICLLY WE ARE CREATING FILE LINK HERE
     */
    private function listEmployeeFilesAndCategories($empId, $matchedDocsAccToRole){
        $bhr = new BambooAPI("clhmentalhealth");
        $bhr->setSecretKey("40d056dd98d048b1d50c46392c77bd2bbbf0431f");
        //dump($matchedDocsAccToRole);
        $employeFilesAccToRole = [];
        $fileLinks = [];
        if(count($matchedDocsAccToRole) > 0){
            foreach($matchedDocsAccToRole as $key => $docsIds){
               // dump($docsIds);
                $employeFilesAccToRole[$key]['docId'] = $docsIds['docId'];
                $employeFilesAccToRole[$key]['docName'] = $docsIds['docName'];
                if (array_key_exists('files', $docsIds)) {
                    foreach($docsIds['files'] as $filekey => $fileId){
                        $fileData = [
                            'url' => "https://40d056dd98d048b1d50c46392c77bd2bbbf0431f:x@api.bamboohr.com/api/gateway.php/clhmentalhealth/v1/employees/$empId/files/".$fileId['id'],
                            'name' => $fileId['name']
                        ];
                        $employeFilesAccToRole[$key]['files'][] = $fileData;
                    }                   
                    
                }

            }
        }
        return $employeFilesAccToRole;
        //dump($employeFilesAccToRole);
       
    }

    private function searchMatchDocKey($targetArray, $documentIds) {
        $filteredArray = array();
    
        foreach ($targetArray as $element) {
            if (in_array($element['docId'], $documentIds)) {
                $filteredArray[] = $element;
            }
        }
    
        return $filteredArray;
    }
    

    private function departmentWiseDocument($empId){
        $empData = $this->getEmployeeDetailByID($empId);
        //dump($empData); dd();
        $empDivision = $empData['empData']['division'];
        $empDepartment = $empData['empData']['department'];
        $empJobInfo = $empData['empData']['jobTitle'];

        $documentIds = [];
        if($empDepartment == env('GROUP_HOME')){ //if department is `Residential Group Home` 
            if($empJobInfo == env('JOBINFO_GROUP_HOME_CHILD_YOUTH')){ //Group Home Residential Child Youth Care Practitioner
                $documentIds = [54, 42, 43, 41, 24, 26, 164, 160, 31, 39, 28, 40, 139, 78, 50, 141, 35, 20, 37, 47, 55, 56, 48, 43, 25, 165, 141, 52, 38];
            }else if($empJobInfo == env('JOBINFO_GROUP_HOME_YOUTH')){ //Group Home Youth	
                $documentIds = [];
            }
        }
        return $documentIds;
    }

    /**
     * THIS METHOD WILL GET THE DOCUMENT NAME AND ID FOR AN EMPLOYEE
     * AND IF THAT DOCUMENT HAS FURTHER ARRAY OF FILES THEN INJECTING files_id WITH THIS ARRAY TOO
     */
    private function getDocumentIdAndName($arrayObj){     
        // dump($arrayObj);  
        $docIdAndName = [];
        $fileIds = [];
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

                if($key == 'file'){
                    $fileArr = $val;
                    if(array_key_exists('@attributes', $val)){ // has single file
                        //dump($fileArr['@attributes']['id']);
                        $fileInfo = [
                            'id' => $fileArr['@attributes']['id'],
                            'name' => $fileArr['name']
                        ];
                        $docIdAndName['files'][] = $fileInfo;
                    }else{
                       // dump($fileArr); // further arrays
                        foreach($fileArr as $files){
                            $fileInfo = [
                                'id' => $files['@attributes']['id'],
                                'name' => $files['name']
                            ];
                            $docIdAndName['files'][] = $fileInfo;
                        }
                    }
                }
            }
            //$docID = $arrayObj['id'];
        } 
        return $docIdAndName;
    }

    /**
     * THIS SECTION IS COMMON TO GET THE BASIC EMPLOYEE DETAILS BY USING IT'S ID
     */
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

    /**
     * THIS METHOD WILL CHECK THE STRUCTURE OF THE RETURN ARRAY RESULT FROM API
     * WHICH IS FURTHER USED AS A INNER METHOD
     */
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
