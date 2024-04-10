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
                       $employeeAllDocumentsArr[]=$this->getDocumentIdAndName($documents); //coming from api
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
        $empDivision = $empData['empData']['division'];
        $empDepartment = $empData['empData']['department'];
        $empJobInfo = $empData['empData']['jobTitle'];

        $documentIds = [];
        if($empDepartment == env('GROUP_HOME')){ //if department is `Residential Group Home` 
            if($empJobInfo == env('JOBINFO_GROUP_HOME_CHILD_YOUTH')){ //Group Home Residential Child Youth Care Practitioner
                $documentIds = [54, 42, 43, 41, 24, 26, 164, 160, 31, 39, 28, 40, 139, 78, 50, 141, 35, 20, 37, 47, 55, 56, 48, 43, 25, 165, 141, 52, 38, 16, 19, 155];
            }else if($empJobInfo == env('JOBINFO_GROUP_HOME_YOUTH')){ //Group Home Youth	
                $documentIds = [100, 16, 19];
            }
        }else if($empDepartment == env('DEPARTMENT_PRP')){ 
            if($empDivision == env('DIVISION_PRP_FAMILY_COORD')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 80, 28, 35, 141, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }else if($empDivision == env('DIVISION_PRP_COORDINATOR_SPEC') && $empJobInfo == env('JOBINFO_PRP_MAYAA')){ //specialist
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 80, 28, 35, 141, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }
            else if($empDivision == env('DIVISION_PRP_COORDINATOR_SPEC')){ //specialist
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 80, 28, 35, 141, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }
        }else if($empDepartment == env('DEPARTMENT_OMHC')){
            if($empJobInfo == env('JOBINFO_COOCCURING_OMHC')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }else if($empJobInfo == env('JOBINFO_Intern_OMHC')){ //when the department is `OMHC;Substance Use Disorder (SUD)` and jobtitle is `INTERN`
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }else if($empJobInfo == env('JOBINFO_GROUP_SUBSTANCE_USE_DISORDER_COUNSELOR')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }
        }else if($empDepartment == env('DEPARTMENT_MENTAL_HEALTH_OMHC')){ //when department is OMHC and MENTAL HEALTH
            if($empJobInfo == env('JOBINFO_MENTAL_HEALTH_OMHC')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }else if($empJobInfo == env('JOBINFO_Clinical_OMHC')){ //when department is OMHC and jobtitle is `clinical director`
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }else if($empJobInfo == env('JOBINFO_Nurse_Practitioner_OMHC')){  // jobtitle is `Psychiatric Nurse Practitioner`0
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }
        }else if($empDepartment == env('DEPARTMENT_LATRILL_ERTHA')){ 
            if($empJobInfo == env('JOBINFO_EXECUTIVE_DIRECTOR')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155, 159, 23];
            }else if($empJobInfo == env('JOBINFO_PRP_ERTHA')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155, 23];
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
                        $fileInfo = [
                            'id' => $fileArr['@attributes']['id'],
                            'name' => $fileArr['name']
                        ];
                        $docIdAndName['files'][] = $fileInfo;
                    }else{
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


    public function getDoucumentCount($empId, Request $request){
        $empId =  '758';
        $empDivision = $request->input('division');
        $empDepartment = $request->input('department');
        $empJobInfo = $request->input('jobInfo');
       $data = $this->getDoucumentData($empDepartment, $empJobInfo,$empDivision, $empId);
       $html = 'vfdgffh';
//        if($data['expire'] > 0 ||  $data['expire'] > 0){
//        if($data['expire'] > 0){
//            $html .= '<span class="badge bg-danger">Expire : '.$data['expire'].'</span>';
//        }
//        if($data['expire_soon'] > 0){
//            $html .= '<span class="badge bg-warning text-dark">Going to Expire : '.$data['expire_soon'].'</span>';
//        }
//    }else{
//        $html .= '<span class="badge bg-success">No Expire date </span>';
//    }
       return $html;
   }


   private function getDoucumentData($empDepartment,$empJobInfo, $empDivision,  $empId  ){
       $matchedDocsAccToRole = '';
       $bhr = new BambooAPI(env('YOUR_COMPANY_ID'));
        $bhr->setSecretKey(env('YOUR_API_KEY'));
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
       $totalFileCount = 0;
       if(count($listEmployeeFiles) > 0){     
           if (isset($listEmployeeFiles['category'])) {
            $employeeDocumentIdsAccToRole = $this->getDocumentIdsBasedOnEmployeeDetails($empDepartment, $empJobInfo, $empDivision);

               foreach($listEmployeeFiles['category'] as $key=> $documents){

        
                    //   $employeeAllDocumentsArr[]=$this->getFileCountForDocumentIds($documents, $employeeDocumentIdsAccToRole); 
                    $totalFileCount += $this->getFileCountForDocumentIds($documents, $employeeDocumentIdsAccToRole); 
               }
            //    dd($listEmployeeFiles['category']);
            dd($employeeDocumentIdsAccToRole);

               dd($totalFileCount);



            dd("djbgdf");

            // dump($listEmployeeFiles['category']);

            // dd($employeeAllDocumentsArr);


               //now we will show the document acc to emp role and jobtitle
            //    $matchedDocsAccToRole = $this->searchMatchDocKey($employeeAllDocumentsArr, $employeeDocumentIdsAccToRole);
               //dump($matchedDocsAccToRole);
           }
       }
}


private function getDocumentIdsBasedOnEmployeeDetails($empDepartment, $empJobInfo, $empDivision) {
    $documentIds = [];
        if($empDepartment == env('GROUP_HOME')){ //if department is `Residential Group Home` 
            if($empJobInfo == env('JOBINFO_GROUP_HOME_CHILD_YOUTH')){ //Group Home Residential Child Youth Care Practitioner
                dd("hsfgdhj");
                $documentIds = [54, 42, 43, 41, 24, 26, 164, 160, 31, 39, 28, 40, 139, 78, 50, 141, 35, 20, 37, 47, 55, 56, 48, 43, 25, 165, 141, 52, 38, 16, 19, 155];
            }else if($empJobInfo == env('JOBINFO_GROUP_HOME_YOUTH')){ //Group Home Youth	
                $documentIds = [100, 16, 19];
            }
        }else if($empDepartment == env('DEPARTMENT_PRP')){ 
            if($empDivision == env('DIVISION_PRP_FAMILY_COORD')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 80, 28, 35, 141, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }else if($empDivision == env('DIVISION_PRP_COORDINATOR_SPEC') && $empJobInfo == env('JOBINFO_PRP_MAYAA')){ //specialist
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 80, 28, 35, 141, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }
            else if($empDivision == env('DIVISION_PRP_COORDINATOR_SPEC')){ //specialist
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 80, 28, 35, 141, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }
        }else if($empDepartment == env('DEPARTMENT_OMHC')){
            if($empJobInfo == env('JOBINFO_COOCCURING_OMHC')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }else if($empJobInfo == env('JOBINFO_Intern_OMHC')){ //when the department is `OMHC;Substance Use Disorder (SUD)` and jobtitle is `INTERN`
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }else if($empJobInfo == env('JOBINFO_GROUP_SUBSTANCE_USE_DISORDER_COUNSELOR')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }
        }else if($empDepartment == env('DEPARTMENT_MENTAL_HEALTH_OMHC')){ //when department is OMHC and MENTAL HEALTH
            if($empJobInfo == env('JOBINFO_MENTAL_HEALTH_OMHC')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }else if($empJobInfo == env('JOBINFO_Clinical_OMHC')){ //when department is OMHC and jobtitle is `clinical director`
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }else if($empJobInfo == env('JOBINFO_Nurse_Practitioner_OMHC')){  // jobtitle is `Psychiatric Nurse Practitioner`0
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155];
            }
        }else if($empDepartment == env('DEPARTMENT_LATRILL_ERTHA')){ 
            if($empJobInfo == env('JOBINFO_EXECUTIVE_DIRECTOR')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155, 159, 23];
            }else if($empJobInfo == env('JOBINFO_PRP_ERTHA')){
                $documentIds = [54, 42, 43, 41, 24, 26, 31, 44, 139, 39, 78, 40, 23, 28, 35, 20, 37, 47, 55, 56, 48, 43, 25, 68, 165, 81, 16, 19, 155, 23];
            }
        }

    return $documentIds;
}

private function getFileCountForDocumentIds($arrayObj, $idsToCount) {
    $fileCount = 0;
    if (is_array($arrayObj)) {
        $docId = $arrayObj['@attributes']['id'];

            if (in_array($docId, $idsToCount)) {
                if(array_key_exists('file', $arrayObj)){  
                $val = $arrayObj['file'];

                if(array_key_exists('@attributes', $val)){ // has single file
                    $fileCount++;
                }else{
                    foreach($val as $files){
                        $fileCount++;
                    }
                }

                }
            }
        }
            //  dump($fileCount);

    return $fileCount;
}

}
