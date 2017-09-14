<?php

namespace App\Http\Controllers;

use App\common;
use App\Employee;
use App\Schedule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Psy\Exception\Exception;

class ScheduleController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Create Schedule.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $schedule = new Schedule();
            $schedule->fill($request->input('roster'));

            if ($schedule == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            if (!$schedule->validate()) {
                $error = $schedule->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $employee = Employee::findOrFail($request->input('roster.employee.id'));
                $schedule->employee()->associate($employee);
                $schedule->save();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_SAVE_DRUG], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get All Schedules.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            $rosters = Schedule::all()->load('employee');

            if ($rosters == null) {
                return response()->json([common::$DRUG_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('rosters'));
            }
        } catch (Exception $e) {
            return response()->json([common::$DRUG_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Schedule from ID.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        try {
            $rosters = Schedule::find($id);

            if ($rosters == null) {
                return response()->json([common::$DRUG_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('rosters'));
            }
        } catch (Exception $e) {
            return response()->json([common::$DRUG_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        try {
            $roster = Schedule::findOrFail($request->input('rosterId'));

            if ($roster == null) {
                return response()->json([common::$DRUG_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                $roster->delete();
                return response()->json([common::$SUCCESS]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$DRUG_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_DRUG], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
