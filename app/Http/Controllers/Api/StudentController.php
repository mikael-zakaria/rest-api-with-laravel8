<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Student;
use App\Http\Resources\StudentResource;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index()
    {
        //get posts
        $students = Student::all();
        //return collection of posts as a resource
        return new StudentResource(true, 'List of student data', $students);
    }


    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
            'name'      => 'required',
            'gender'    => 'required',
            'parent'    => 'required',
            'address'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/students', $image->hashName());

        //create post
        $student = Student::create([
            'image'     => $image->hashName(),
            'name'      => $request->name,
            'gender'    => $request->gender,
            'parent'    => $request->parent,
            'address'   => $request->address,
        ]);

        //return response
        return new StudentResource(true, 'Student data successfully added!', $student);
    }


    public function show(Student $student)
    {
        //return single post as a resource
        return new StudentResource(true, 'Student data found!', $student);
    }


    public function update(Request $request, Student $student)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2000',
            'name'      => 'required',
            'gender'    => 'required',
            'parent'    => 'required',
            'address'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/students', $image->hashName());

            //delete old image
            Storage::delete('public/students/'.$student->image);

            //update post with new image
            $student->update([
                'image'     => $image->hashName(),
                'name'      => $request->name,
                'gender'    => $request->gender,
                'parent'    => $request->parent,
                'address'   => $request->address,
            ]);

        } else {

            //update post without image
            $student->update([
                'name'      => $request->name,
                'gender'    => $request->gender,
                'parent'    => $request->parent,
                'address'   => $request->address,
            ]);
        }

        //return response
        return new StudentResource(true, 'Student data successfully updated!', $student);
    }


    public function destroy(Student $student)
    {   
        //delete image
        Storage::delete('public/students/'.$student->image);

        //delete post
        $student->delete();

        //return response
        return new StudentResource(true, 'Student data has been successfully deleted!', null);
    }
}
