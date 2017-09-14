<?php

namespace App\Http\Controllers;

use App\common;
use App\Doctor;
use App\DoctorSpecialty;
use App\Enum\DoctorType;
use App\User;
use DB;
use Illuminate\Http\Request;
use Mail;
use Mockery\CountValidator\Exception;

/**
 * Class DoctorController
 * @package App\Http\Controllers
 */
class DoctorController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Get all Doctor Types.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllDoctorTypes()
    {
        try {

            $doctorTypes = DoctorType::getAll();

            return response()->json(compact('doctorTypes'));

        } catch (Exception $e) {
            return response()->json([common::$DOC_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all Doctor Specialties.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllDoctorSpecialties()
    {
        try {
            $doctorSpecialties = DoctorSpecialty::all();
            return response()->json(compact('doctorSpecialties'));
        } catch (Exception $e) {
            return response()->json([common::$DOC_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all Doctors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            $doctors = Doctor::all()->load('User');

            if ($doctors == null) {
                return response()->json([common::$DOC_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('doctors'));
            }
        } catch (Exception $e) {
            return response()->json([common::$DOC_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create Doctor.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        if ($request->input('doctor') == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $doctor = new Doctor();
            $user = new User();

            $doctor->fill($request->input('doctor'));
            $user->setRememberToken(common::getConfirmationId());
            $user->userRole = 'Doctor';

            if ($doctor->doctorType === 'Channelling') {
                $doctorSpecialty = DoctorSpecialty::find($request->input('doctor.doctorSpecialty.id'));

                if ($doctorSpecialty == null) {
                    return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
                }

                $doctor->doctorSpecialty()->associate($doctorSpecialty);
            } else if ($doctor->doctorType === 'OPD') {
                $doctor->doctorSpecialty()->associate(null);
            }

            if (!$doctor->validate()) {
                $error = $doctor->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                DB::transaction(function () use ($doctor, $user) {
                    $doctor->save();
                    $doctor->user()->save($user);

                    Mail::send('email.newAccount', ['employee' => $doctor], function ($message) use ($doctor) {
                        $message->from(common::$DEFAULT_EMAIL, "Biyagama Private Hospital");
                        $message->to($doctor->email, $doctor->firstName . ' ' . $doctor->lastName);
                        $message->subject("New Account");
                    });
                });

                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_SAVE_DOC], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Doctor details.
     *
     * @param $id string Doctor Id.
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        if ($id == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $doctor = Doctor::find($id)->load('User');

            if ($doctor == null) {
                return response()->json([common::$DOC_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('doctor'));
            }
        } catch (Exception $e) {
            return response()->json([common::$DOC_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Doctor count.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        try {

            $doctorCount = Doctor::all()->count();

            return response()->json(compact('doctorCount'));

        } catch (Exception $e) {
            return response()->json([common::$DOC_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search Doctors.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $searchBy = $request->input('searchBy');
        $searchText = $request->input('searchText');
        $doctorType = $request->input('doctorType');

        if ($searchBy == null || $searchBy == "" || $searchText == null || $searchText == "" || $doctorType == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        if (!in_array($searchBy, common::$DOCTOR_SEARCH_BY_FIELDS)) {
            return response()->json(common::$INVALID_SEARCH_BY, common::$HTTP_BAD_REQUEST);
        }

        if ($doctorType == null || !DoctorType::isValidValue($doctorType)) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $doctors = Doctor::where($searchBy, 'LIKE', '%' . $searchText . '%')->where('doctorType', $doctorType)->get();
            return response()->json(compact('doctors'));
        } catch (Exception $e) {
            return response()->json([common::$DOC_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Doctor.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $doctor = Doctor::find($request->input('doctor.id'));
            $doctor->fill($request->input('doctor'));

            if ($doctor == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            if ($doctor->doctorType === 'Channelling') {
                $doctorSpecialty = DoctorSpecialty::find($request->input('doctor.doctorSpecialty.id'));

                if ($doctorSpecialty == null) {
                    return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
                }

                $doctor->doctorSpecialty()->associate($doctorSpecialty);
            } else if ($doctor->doctorType === 'OPD') {
                $doctor->doctorSpecialty()->associate(null);
            }

            $doctor->setUpdateRules();

            if (!$doctor->validate()) {
                $error = $doctor->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $doctor->save();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_UPDATE_DOC], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Doctor.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $doctorId = $request->json('doctorId');

        try {
            if ($doctorId == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $doctor = Doctor::find($doctorId);

            if ($doctor == null) {
                return response()->json([common::$DOC_NOT_FOUND], common::$HTTP_NOT_FOUND);
            }

            $doctor->delete();

            return response()->json([common::$SUCCESS]);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_DOC], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Toggle Doctor status.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request)
    {
        $doctorId = $request->input('doctorId');

        try {
            if ($doctorId == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $employee = Doctor::find($doctorId);

            if ($employee == null) {
                return response()->json([common::$DOC_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {

                $employee->user->disabled = !$employee->user->disabled;
                $employee->user->save();

                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DISABLE_DOC], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
