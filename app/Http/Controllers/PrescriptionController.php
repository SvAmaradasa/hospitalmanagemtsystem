<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\common;
use App\Doctor;
use App\Drug;
use App\Enum\AppointmentStatus;
use App\Patient;
use App\Prescription;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use JWTAuth;
use Mockery\Exception;

class PrescriptionController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Create Prescription.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
        } else if ($request->input('prescription') == null || $request->input('prescription.appointment') == null || $request->input('prescription.patient') == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $prescription = new Prescription();
            $prescription->fill($request->input('prescription'));
            $appointment = Appointment::find($request->input('prescription.appointment.id'));
            $doctor = Doctor::findOrFail($user->doctor_id);
            $patient = Patient::findOrFail($request->input('prescription.patient.id'));

            if (!$prescription->validate()) {
                $error = $prescription->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                DB::transaction(function () use ($prescription, $appointment, $doctor, $request, $patient) {
                    $appointment->appointmentStatus = AppointmentStatus::IN_PROGRESS;
                    $appointment->save();

                    $prescription->appointment()->associate($appointment);
                    $prescription->doctor()->associate($doctor);
                    $prescription->patient()->associate($patient);
                    $prescription->save();

                    if ($request->input('prescription.drugs') != null) {
                        foreach ($request->input('prescription.drugs') as $drug) {
                            $prescription->drugs()->save(Drug::findOrFail($drug['id']), ["days" => $drug['days'], "schedule" => $drug['schedule'], "note" => isset($drug['note']) ? $drug['note'] : null]);
                        }
                    }
                });

                return response()->json($prescription->id, common::$HTTP_SUCCESS);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$APP_NOT_FOUND], common::$HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_SAVE_PRESCRIPTION], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get All Prescriptions.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            $prescriptions = Prescription::all();

            if ($prescriptions == null) {
                return response()->json([common::$PRESCRIPTION_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('prescriptions'));
            }
        } catch (Exception $e) {
            return response()->json([common::$PRESCRIPTION_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Prescription from ID.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        try {
            $prescription = Prescription::find($id);

            if ($prescription == null) {
                return response()->json([common::$PRESCRIPTION_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('prescription'));
            }
        } catch (Exception $e) {
            return response()->json([common::$PRESCRIPTION_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Prescription.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
        } else if ($request->input('prescription') == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $prescription = Prescription::find($request->input('prescription.id'));
            $doctor = Doctor::findOrFail($user->doctor_id);

            if (!$prescription->validate()) {
                $error = $prescription->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $prescription->fill($request->input('prescription'));

                DB::transaction(function () use ($prescription, $request, $doctor) {
                    $prescription->save();
                    $prescription->doctor()->associate($doctor);
                    $prescription->drugs()->detach();

                    foreach ($request->input('prescription.drugs') as $drug) {
                        $prescription->drugs()->save(Drug::findOrFail($drug['id']), ["days" => $drug['days'], "schedule" => $drug['schedule'], "note" => isset($drug['note']) ? $drug['note'] : null]);
                    }
                });

                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_UPDATE_PRESCRIPTION], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Prescription.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        try {
            $prescription = Prescription::findOrFail($request->input('prescriptionId'));

            if ($prescription == null) {
                return response()->json([common::$PRESCRIPTION_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                $prescription->delete();
                return response()->json([common::$SUCCESS]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$PRESCRIPTION_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_PRESCRIPTION], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Prescriptions For Drug Issue.
     * @param $appointmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForDrugIssue($appointmentId)
    {
        try {
            $prescriptions = Prescription::whereHas('appointment', function ($query) use ($appointmentId) {
                $query->whereIn('appointmentStatus', [AppointmentStatus::IN_PROGRESS, AppointmentStatus::CLOSE])->where('displayId', $appointmentId);
            })
                ->with(['appointment', 'patient'])
                ->get();

            if (sizeof($prescriptions) == 0) {
                return response()->json([common::$PRESCRIPTION_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('prescriptions'));
            }
        } catch (Exception $e) {
            return response()->json([common::$PRESCRIPTION_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Prescriptions for Update.
     * @param $appointmentId
     * @param $patientId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForUpdate($appointmentId, $patientId)
    {
        if ($patientId == null || $appointmentId == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $prescription = Prescription::whereHas('appointment', function ($query) {
                $query->whereIn('appointmentStatus', [AppointmentStatus::IN_PROGRESS]);
            })
                ->with('doctor')
                ->where('patient_id', $patientId)
                ->where('appointment_id', $appointmentId)
                ->firstOrFail();

            if ($prescription == null) {
                return response()->json([common::$PRESCRIPTION_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('prescription'));
            }
        } catch (Exception $e) {
            return response()->json([common::$PRESCRIPTION_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mark Prescription as Issued.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function issue(Request $request)
    {
        if ($request->input('prescription') == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $prescription = Prescription::find($request->input('prescription.id'));

            DB::transaction(function () use ($prescription, $request) {
                $prescription->issued = true;
                $prescription->save();

                if ($prescription->appointment->prescriptions->where('issued', false)->count() == 0) {
                    $prescription->appointment->appointmentStatus = AppointmentStatus::CLOSE;
                    $prescription->appointment->save();
                }

                foreach ($request->input('prescription.drugs') as $drugData) {
                    if (isset($drugData['issuedQty'])) {
                        $drug = Drug::where('id', $drugData['id'])->firstOrFail();
                        $drug->qty = $drug->qty - intval($drugData['issuedQty']);
                        $drug->save();
                    }
                }
            });

            return response()->json([common::$SUCCESS]);

        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_UPDATE_PRESCRIPTION], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
