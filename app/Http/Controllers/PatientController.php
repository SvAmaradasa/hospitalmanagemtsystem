<?php

namespace App\Http\Controllers;

use App\common;
use App\Patient;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;

class PatientController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Create Patient.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $patient = new Patient();
            $patient->fill($request->input('patient'));

            if ($patient == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            if (!$patient->validate()) {
                $error = $patient->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $patient->save();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_SAVE_PATIENT], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all Patients.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            $patients = Patient::all();

            if ($patients == null) {
                return response()->json([common::$PATIENT_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('patients'));
            }
        } catch (Exception $e) {
            return response()->json([common::$PATIENT_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Patient details.
     *
     * @param $id string Patient Id.
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        if ($id == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $patient = Patient::find($id);

            if ($patient == null) {
                return response()->json([common::$PATIENT_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('patient'));
            }
        } catch (Exception $e) {
            return response()->json([common::$PATIENT_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Patient count.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        try {
            $patientCount = Patient::all()->count();
            return response()->json(compact('patientCount'));
        } catch (Exception $e) {
            return response()->json([common::$PATIENT_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search Patients.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $searchBy = $request->input('searchBy');
        $searchText = $request->input('searchText');

        if ($searchBy == null || $searchBy == "" || $searchText == null || $searchText == "") {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        if (!in_array($searchBy, common::$PATIENT_SEARCH_BY_FIELDS)) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $patients = Patient::where($searchBy, 'LIKE', '%' . $searchText . '%')->get();
            return response()->json(compact('patients'));
        } catch (Exception $e) {
            return response()->json([common::$PATIENT_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Patient.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {

            $patient = Patient::find($request->json('patient.id'));
            $patient->fill($request->json('patient'));

            if ($patient == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $patient->setUpdateRules();

            if (!$patient->validate()) {
                $error = $patient->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $patient->save();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_UPDATE_PATIENT], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Patient.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $patientId = $request->json('patientId');

        try {
            if ($patientId == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $patient = Patient::find($patientId);

            if ($patient == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $patient->delete();
            return response()->json([common::$SUCCESS]);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_PATIENT], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
