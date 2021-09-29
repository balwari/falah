<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Helper\JsonApiResponse;

class StudentController extends Controller
{
    function add(Request $request)
    {
        $rules = [
            'full_name' => 'required|string|min:3|max:255',
            'class' => 'required|integer',
            'gender' => 'required|string',
            'fees' => 'required|integer',
            'photo' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'entry_year' => 'required|integer',
            'dob' => 'required',
            'phone' => 'required|integer',
            'current_address' => 'required|string',
            'guardian_name' => 'required|string',
            'guardian_mobile' => 'required|integer',
            'aadhar_no' => 'required|integer|unique:students,aadhar_no',
        ];

        $customMessages = [
            'required' => 'The :attribute field is required.',
            'unique'    => ':attribute is already used',
        ];

        $student_details =  $this->validate($request, $rules, $customMessages);

        if (isset($request->photo)) {
            $imageName = time() . '.' . $request->photo->extension();

            $upload = $request->photo->move(public_path(), $imageName);

            if (!$upload) {
                return JsonApiResponse::error('Something went wrong in Photo Uploading',422);
            }
            $student_details['photo'] = $imageName;
        }
        $create = Student::create($student_details);

        if (!$create) {
            return JsonApiResponse::error('Something went wrong in adding Student',422);
        }
        return JsonApiResponse::success('Successfully Added Student', ['data' => $student_details]);
    }
}
