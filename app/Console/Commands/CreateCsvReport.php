<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployeeController;
use App\Models\Reports;
class CreateCsvReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-csv-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    protected $documentController;
    protected $employeeController;

    public function __construct(DocumentController $documentController, EmployeeController $employeeController)
    {
        parent::__construct();
        $this->documentController = $documentController;
        $this->employeeController = $employeeController;
    }
    // public function __construct(EmployeeController $documentController)
    // {
    //     parent::__construct();
    //     $this->documentController = $documentController;
    // }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $requestedReport = Reports::where('status', 'requested')->first();
        $requestedReportExists = Reports::where('status', 'inprocess')->exists();
        if ($requestedReport && $requestedReportExists === false) {
            $requestedReport->update([
                'status' => 'inprocess',
            ]);
            $employeeFieldsIndexes = array(
                'ID'=>17,
                'firstname' => 1,
                'lastname' => 2,
                'designation' => 4,
                'email' => 7,
                'department'=>8,
                'manager'=>13,
                'jobTitle'=>4,
                'division'=>10,            
            );
    
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
            $employees = $dataArray['employees'];
            $employees = $employees['employee'];
            
            $empMainArr = array();
            $i = 0;
      
            foreach($employees as $empKeys=> $emp){
                $empID = $emp['@attributes']['id'];
                $empMainArr[$i]['ID'] = $empID;
                $empMainArr[$i]['firstname'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['firstname']]);
                $empMainArr[$i]['lastname'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['lastname']]);
                $empMainArr[$i]['designation'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['designation']]);
                $empMainArr[$i]['email'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['email']]);
                $empMainArr[$i]['department'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['department']]);
                $empMainArr[$i]['manager'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['manager']]);
                $empMainArr[$i]['jobTitle'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['jobTitle']]);
                $empMainArr[$i]['division'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['division']]);
    
                 try {
                    $html = '';
                    $blankPersonalFields = $this->employeeController->getPersonalBlankFields($empID);
                    $blankJobFields = $this->employeeController->getJobBlankFields($empID);
                    $getEmergencyContacts = $this->employeeController->getEmergencyFields($empID);
                    $blankEmergencyFields =$getEmergencyContacts['empty'];
                    if( count($blankJobFields) > 0 ){
                        $html .= 'Job : '.count($blankJobFields);
                    }else if ( count($blankJobFields) == 0) {
                        $html .= 'Job : '.count($blankJobFields);
                    }
                  
                    if( count($blankPersonalFields) > 0 ){
                        $html .= 'Personal : '.count($blankPersonalFields);
                    }else if ( count($blankPersonalFields) == 0) {
                        $html .= 'Personal : '.count($blankPersonalFields);
                    }
            
                    if( count($blankEmergencyFields) > 0 ){
                        $html .= 'Emergency : '.count($blankEmergencyFields);
                    }else if ( count($blankEmergencyFields) == 0) {
                        $html .= 'Emergency : '.count($blankEmergencyFields);
                    }
                    $empMainArr[$i]['blankFeilds'] = $html;
                } catch (\Exception $e) {
                    $empMainArr[$i]['blankFeilds'] = 'NA';
                }
    
                try {
                   $data =  $this->employeeController->getTimeTrackerData($this->checkIfArray($emp['field'][$employeeFieldsIndexes['department']]), $this->checkIfArray($emp['field'][$employeeFieldsIndexes['jobTitle']]), $this->checkIfArray($emp['field'][$employeeFieldsIndexes['division']]), $empID);
    
                    $html = '';
                    if($data['expire'] > 0 ||  $data['expire'] > 0){
                    if($data['expire'] > 0){
                        $html .= 'Expire : '.$data['expire'];
                    }
                    if($data['expire_soon'] > 0){
                        $html .= 'Going to Expire : '.$data['expire_soon'];
                    }
                    }else{
                        $html .= 'No Expire date';
                    }
                    $empMainArr[$i]['expirationDateCount'] = $html;
                   
                } catch (\Exception $e) {
                    // Set default value if an exception occurs
                    $empMainArr[$i]['expirationDateCount'] = 'NA';
                }

                try {
                    $empMainArr[$i]['emptyDocumentCount'] = $this->documentController->getDoucumentData($this->checkIfArray($emp['field'][$employeeFieldsIndexes['department']]), $this->checkIfArray($emp['field'][$employeeFieldsIndexes['jobTitle']]), $this->checkIfArray($emp['field'][$employeeFieldsIndexes['division']]), $empID);
                } catch (\Exception $e) {
                    $empMainArr[$i]['emptyDocumentCount'] = 'NA';
                }

                $empIdsAr[] = $empID;
                $i++;
    
            }  
            $directory = 'csv/';
            
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory); // Create directory if it doesn't exist
            }
            
            // Define CSV file path with timestamp
            $filename = 'data_' . date('Y-m-d_H-i-s') . '.csv';
            $filePath = $directory . $filename;
            
            // Generate CSV content as a string
            $csvContent = '';
            
            // Write headers
            $csvContent .= implode(',', array_keys($empMainArr[0])) . "\n";
            
            // Write data
            foreach ($empMainArr as $row) {
                $csvContent .= '"' . implode('","', $row) . '"' . "\n";
    
            }
            
            // Write CSV content to file
            Storage::disk('public')->put($filePath, $csvContent);

            $fileUrl = Storage::disk('local')->url($filePath);
            $requestedReport->update([
                'url' => $fileUrl,
                'status' => 'created',
                'report_created_at' => now(),
            ]);
        
        }
       
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
}
