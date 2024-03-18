<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function getCatalogDetail()
    {
        $response = [
            'success' => false,
            'status' => 400,
            'message' => 'Error fetching catalog details.'
        ];
    
        if (request()->has('q')) {
            $q = request()->input('q');
    
            // Get Catalogs By Searched params
            $results = Catalog::where('title', 'LIKE', '%' . $q . '%')->get();
    
            // Update image path for search results
            foreach ($results as $result) {
                if ($result->image != null) {
                    $result->image = '/storage/' . $result->image;
                }
            }
        } else {
            // Get All Available Catalogs
            $results = Catalog::all();
    
            // Update image path for all available catalogs
            foreach ($results as $result) {
                if ($result->image != null) {
                    $result->image = '/storage/' . $result->image;
                }
            }
        }
    
        if ($results->isNotEmpty()) {
            $response['success'] = true;
            $response['status'] = 200;
            $response['message'] = 'Catalog details retrieved successfully.';
            $response['data'] = $results;
        }
    
        return response()->json($response); 
    }
    
}
