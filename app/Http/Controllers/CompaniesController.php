<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCompany;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompaniesController extends Controller
{
    public function index()
    {
        // $companies = Company::with('user')->get();
        $companies = [];
        return view('companies.index',compact('companies'));
    }

    public function store(AddCompany $request)
    {
        //Request Data validation
        $validatedData = $request->validated();

        //Create User
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'role' => 1,
            'password' =>  Hash::make($validatedData['password']),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->hasFile('profile_picture')) {
            $uploadedFile = $request->file('profile_picture');
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();
            $uploadedFile->storeAs('public/Profile/images/', $filename);
            $path = 'Profile/images/' . $filename;
            $user->profile_picture = $path;
            $user->save();

        }

        $userId = $user->id;
        //Create Company On User Created
        if ($user) {
            $company = Company::create([
                'company_name' => $validatedData['company_name'],
                'address' => $validatedData['address'],
                'country' => $validatedData['country'],
                'city' => $validatedData['city'],
                'state' => $validatedData['state'],
                'zip' => $validatedData['pincode'],
                'status' => 'active',
            ]);

            if ($request->hasFile('logo')) {
                $uploadedFile = $request->file('logo');
                $filename = time() . '_' . $uploadedFile->getClientOriginalName();
                $uploadedFile->storeAs('public/Companies/logo/', $filename);
                $path = 'Companies/logo/' . $filename;
                $company->logo = $path;
                $company->save();
            }

            if ($company) {
                //Update company id in user Model
                User::where('id',$userId)->update(['company_id' => $company->id]);
                return response()->json(['success' => true, 'message' => 'Company created successfully']);
            } else {
                // Handle the case where Company creation fails
                $user->delete(); // Rollback the user creation
                return response()->json(['error' => 'Failed to create Company'], 500);
            }
        } else {
            // Handle the case where User creation fails
            return response()->json(['error' => 'Failed to create User'], 500);
        }
    }

    public function destroy($id)
    {

        $company = Company::findOrFail($id);
        $company->delete();

        $user = User::where('company_id',$id)->first();
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Company Deleted successfully']);
    }
}
