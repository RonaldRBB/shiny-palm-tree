<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Notifications\ConfirmationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    private const ERROR_MESSAGE = 'There was an error, please try again later';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $patients = Patient::all();
            return $this->message(true, 'Patients found', 200, $patients);
        } catch (\Exception $e) {
            return $this->errorMessage($e, 500);
        }
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
        try {
            $validated = $this->validateRequest($request);
            if ($validated !== true) {
                return $validated;
            }
            $image_path = $this->saveImage($request->document_photo);
            $patient = $this->fillModel($request, $image_path);
            $patient->save();
            $patient->notify(new ConfirmationMessage(false));
            return $this->message(true, 'Patient created', 201, $patient);
        } catch (\Exception $e) {
            return $this->errorMessage($e, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        try {
            return $this->message(true, 'Patient found', 200, $patient);
        } catch (\Exception $e) {
            return $this->errorMessage($e, 500);
        }
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
        try {
            $validated = $this->validateRequest($request);
            if ($validated !== true) {
                return $validated;
            }
            $image_path = $this->saveImage($request->document_photo);
            $patient = $this->fillModel($request, $image_path, $patient);
            $patient->save();
            return $this->message(true, 'Patient updated', 200, $patient);
        } catch (\Exception $e) {
            return $this->errorMessage($e, 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        try {
            $patient->delete();
            return $this->message(true, 'Patient deleted', 200);
        } catch (\Exception $e) {
            return $this->errorMessage($e, 500);
        }
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
            return $this->message(false, $validator->errors(), 400);
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
    private function message($success, $message, $code, $data = [])
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];
        if (!empty($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, $code);
    }
    private function errorMessage($message, $code)
    {
        Log::error($message, ['exception' => $message]);
        return $this->message(false, self::ERROR_MESSAGE, $code);
    }
    private function saveImage($image)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
        $image_path = 'images/' . $imageName;
        return $image_path;
    }
}
