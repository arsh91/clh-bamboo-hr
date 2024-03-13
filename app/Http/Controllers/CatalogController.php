<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\AddCatalog;
use App\Http\Requests\UpdateCatalog;
use App\Models\Catalog;
use Illuminate\Http\Request;

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
            'name' => $validatedData['name'],
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'sku' => $validatedData['sku'],
            'base_price' => $validatedData['base_price'],
            'status' => $validatedData['status'],
            'publish_date' => $validatedData['status'] == 'publish' ? now() : '',
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
            'name' => $validatedData['name'],
            'title' => $validatedData['title'],
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
}
