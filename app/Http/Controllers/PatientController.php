<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
        $validated = $this->validateRequest($request);
        if ($validated !== true) {
            return $validated;
        }
        $image_path = $this->saveImage($request->document_photo);
        $patient = $this->fillModel($request, $image_path);
        $patient->save();
        $mesage = $this->message(true, 'Patient created', $patient);
        Mail::to($patient->email)->queue(new \App\Mail\ConfirmationMail());
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
        $validated = $this->validateRequest($request);
        if ($validated !== true) {
            return $validated;
        }
        $image_path = $this->saveImage($request->document_photo);
        $patient = $this->fillModel($request, $image_path, $patient);
        $patient->save();
        $mesage = $this->message(true, 'Patient updated', $patient);
        return response()->json($mesage);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return response()->json(['message' => 'Paciente deleted', 'patient' => $patient]);
    }
    private function validateRequest($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients',
            'phone' => 'required|string|max:15',
            'document_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            $message = $this->message(false, 'Validation failed', $validator->errors());
            return response()->json($message, 422);
        }
        return true;
    }
    private function fillModel(Request $request, $image_path, Patient $patient = null)
    {
        if (is_null($patient)) {
            $patient = new Patient();
        }
        $patient->name = $request->name;
        $patient->email = $request->email;
        $patient->phone = $request->phone;
        $patient->document_photo = $image_path;
        return $patient;
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
