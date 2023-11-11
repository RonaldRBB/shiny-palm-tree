<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::all();
        $mesage = $this->message(true, 'Patients found', $patients);
        return response()->json($mesage);
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients',
            'phone' => 'required|string|max:15',
            'document_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $image_path = $this->saveImage($request->document_photo);
        $patient = new Patient();
        $patient->name = $request->name;
        $patient->email = $request->email;
        $patient->phone = $request->phone;
        $patient->document_photo = $image_path;
        $patient->save();
        $mesage = $this->message(true, 'Patient created', $patient);
        return response()->json($mesage);
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $message = $this->message(true, 'Patient found', $patient);
        return response()->json($message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email,' . $patient->id,
            'phone' => 'required|string|max:15',
            // 'document_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $patient->name = $request->name;
        $patient->email = $request->email;
        $patient->phone = $request->phone;
        // $patient->document_photo = $request->document_photo;
        $patient->save();
        $message = $this->message(true, 'Patient updated', $patient);
        return response()->json($message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return response()->json(['message' => 'Paciente deleted', 'patient' => $patient]);
    }
    private function message($success, $message, $data = [])
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];
        if (!empty($data)) {
            $response['data'] = $data;
        }
        return $response;
    }
    private function saveImage($image)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
        $image_path = 'images/' . $imageName;
        return $image_path;
    }
}
