<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\AddCatalog;
use App\Http\Requests\UpdateCatalog;
use App\Models\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {    
        $catalogs = Catalog::all();

        return view('catalogs.index',compact('catalogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddCatalog $request)
    {
          //Request Data validation
          $validatedData = $request->validated();
          

           //Create User
         $catalog = Catalog::create([
            'author_id' => auth()->user()->id,
            // 'name' => $validatedData['title'],
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'wp_category_id' => $validatedData['category'],
            'sku' => $validatedData['sku'],
            'base_price' => $validatedData['base_price'],
            'status' => $validatedData['status'],
            'publish_date' => $validatedData['status'] == 'publish' ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        //Only if Needs to update the preview image then this will update the image
        if ($request->hasFile('image')) {
            //Delete The Old Stored Image in path And Upload New
                $uploadedFile = $request->file('image');
                $filename = time() . '_' . $uploadedFile->getClientOriginalName();
                $uploadedFile->storeAs('public/Catalogs', $filename);
                $path = 'Catalogs/' . $filename;
                Catalog::where('id', $catalog->id)->update(['image' => $path]);

        }

        $request->session()->flash('message','Catalog Saved Successfully.');

        return Response()->json(['status'=>200, 'catalog'=>$catalog]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $catalog = Catalog::find($id);


        return Response()->json(['catalogs' =>$catalog]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCatalog $request, Catalog $catalog)
    {
          //Request Data validation
          $validatedData = $request->validated();
           //Update User
           $catalogs = Catalog::where('id', $catalog->id)->update([
            // 'name' => $validatedData['title'],
            'title' => $validatedData['title'],
            'wp_category_id' => $validatedData['category'],
            'content' => $validatedData['content'],
            'sku' => $validatedData['sku'],
            'base_price' => $validatedData['base_price'],
            'status' => $validatedData['status'],
            'publish_date' => $validatedData['status'] == 'publish' ? now() : null,
            'updated_at' => now(),
        ]);

         //Only if Needs to update the preview image then this will update the image
         if ($request->hasFile('image')) {
            $oldFilePath = 'storage/'.$catalog->image;
            //Delete The Old Stored Image in path And Upload New
            if (Helper::deleteFile($oldFilePath)) {
                $uploadedFile = $request->file('image');
                $filename = time() . '_' . $uploadedFile->getClientOriginalName();
                $uploadedFile->storeAs('public/Catalogs', $filename);
                $path = 'Catalogs/' . $filename;
                Catalog::where('id', $catalog->id)->update(['image' => $path]);
            } else {
                return back()->with("File $oldFilePath not found.");
            }

        }

        $request->session()->flash('message','Catalog updated successfully.');
		return Response()->json(['status'=>200]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $catalog = Catalog::findOrFail($id);

         $oldFilePath = 'storage/'.$catalog->image;
            // Delete The Old Stored Image in path And Upload New
            if (Helper::deleteFile($oldFilePath)) {
            } else {
                return back()->with("File $oldFilePath not found.");
            }

        $catalog->delete();

        session()->flash('message','Catalog Deleted successfully.');
        return response()->json(['success' => true]);
    }

    // public function fetchCategories()
    // {
    //     $url = 'https://recollection.com/wp-json/wc/v3/products/categories';
        
    //       // Get the selected category value from the request
    //     //   $selectedCategory = request()->input('category');
    //     //   if($selectedCategory){
    //     //     dd($selectedCategory);
    //     //     $url = 'https://recollection.com/wp-json/wc/v3/products/categories?search='.$selectedCategory;
    //     //   }

    //     $consumerKey = 'ck_db66350c57384308f7ffe8045cada46ee3e7d96e';
    //     $consumerSecret = 'cs_7c01bf3a4f3fae66a8cd8c4f40890b91c36151d2';
        
    //     // Generate OAuth nonce and timestamp
    //     $oauthNonce = md5(uniqid(rand(), true));
    //     $oauthTimestamp = time();
        
    //     // Generate OAuth signature
    //     $baseString = 'GET&' . urlencode($url) . '&'
    //         . urlencode('oauth_consumer_key=' . $consumerKey
    //         . '&oauth_nonce=' . $oauthNonce
    //         . '&oauth_signature_method=HMAC-SHA1'
    //         . '&oauth_timestamp=' . $oauthTimestamp
    //         . '&oauth_version=1.0'
    //     );
        
    //     $key = urlencode($consumerSecret) . '&';
    //     $oauthSignature = base64_encode(hash_hmac('sha1', $baseString, $key, true));
        
    //     // Set CURL options
    //     $curl = curl_init();
    //     curl_setopt_array($curl, [
    //         CURLOPT_URL => $url,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_SSL_VERIFYPEER => false, // Disable SSL certificate verification
    //         CURLOPT_HTTPHEADER => [
    //             'Authorization: OAuth oauth_consumer_key="' . $consumerKey . '", '
    //             . 'oauth_nonce="' . $oauthNonce . '", '
    //             . 'oauth_signature="' . urlencode($oauthSignature) . '", '
    //             . 'oauth_signature_method="HMAC-SHA1", '
    //             . 'oauth_timestamp="' . $oauthTimestamp . '", '
    //             . 'oauth_version="1.0"'
    //         ]
    //     ]);
        
    //     // Execute the request
    //     $response = curl_exec($curl);
    //     // dd( $response );
    //     // Check for errors
    //     if ($response === false) {
    //         $error = curl_error($curl);
    //         // Handle error
    //         return "CURL Error: $error";
    //     }
    //     // dd($response);
        
    //     // Close CURL
    //     curl_close($curl);
        
    //     // Process the response as needed
    //     return $response;
        
    // }

    public function fetchCategories()
    {
        $consumerKey = 'ck_db66350c57384308f7ffe8045cada46ee3e7d96e';
        $consumerSecret = 'cs_7c01bf3a4f3fae66a8cd8c4f40890b91c36151d2';
        $apiUrl = 'https://recollection.com/wp-json/wc/v3/products/categories';

        $response = Http::withBasicAuth($consumerKey, $consumerSecret)
        ->withoutVerifying()->get($apiUrl, ['per_page' => 100]); // Or use a large number to get all categories
        
        if ($response->successful()) {
            $categories = $response->json();
            return response()->json($categories);
        } else {
            return response()->json(['error' => 'Error fetching categories'], 500);
        }
    }
}