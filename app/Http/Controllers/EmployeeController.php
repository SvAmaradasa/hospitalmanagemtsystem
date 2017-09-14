<?php

namespace App\Http\Controllers;

use App\common;
use App\Employee;
use App\User;
use DB;
use Illuminate\Http\Request;
use Mail;
use Mockery\CountValidator\Exception;

/**
 * Class EmployeeController
 * @package App\Http\Controllers
 */
class EmployeeController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Get all employees
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            //Retrieve Employee records from database with associated Employee Role record
            $employees = Employee::all()->load('User');

            if ($employees == null) {
                return response()->json([common::$EMP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('employees'));
            }
        } catch (Exception $e) {
            return response()->json([common::$EMP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get employee details
     *
     * @param $id integer Employee Id.
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        if ($id == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            //Retrieve Employee record from Employee Id from database
            $employee = Employee::find($id)->load('User');

            if ($employee == null) {
                return response()->json([common::$EMP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('employee'));
            }
        } catch (Exception $e) {
            return response()->json([common::$EMP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all employee roles
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllEmployeeRoles()
    {
        try {
            $employeeRoles = ['Administrator', 'Manager', 'Accountant', 'Nurse', 'Receptionist', 'Pharmacist'];

            if ($employeeRoles == null) {
                return response()->json([common::$EMP_ROLE_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('employeeRoles'));
            }
        } catch (Exception $e) {
            return response()->json([common::$EMP_ROLE_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create Employee.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {

            if ($request->json('employee') == null || $request->json('employee') == '') {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $employee = new Employee();
            $user = new User();
            $employee->fill($request->json('employee'));
            $user->setRememberToken(common::getConfirmationId());
            $user->fill($request->json('employee.user'));

            if (!$employee->validate()) {
                $error = $employee->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                DB::transaction(function () use ($employee, $user) {
                    $employee->save();
                    $employee->user()->save($user);

                    Mail::send('email.newAccount', ['employee' => $employee], function ($message) use ($employee) {
                        $message->from(common::$DEFAULT_EMAIL, "Biyagama Private Hospital");
                        $message->to($employee->email, $employee->firstName . ' ' . $employee->lastName);
                        $message->subject("New Account");
                    });
                });

                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_SAVE_EMP], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Toggle Employee status.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus(Request $request)
    {
        $empId = $request->input('employeeId');

        try {
            if ($empId == null) {
                //If request is null return bad request response
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            //Retrieve Employee record from Employee Id from database
            $employee = Employee::find($empId);

            if ($employee == null) {
                return response()->json([common::$EMP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {

                $employee->user->disabled = !$employee->user->disabled;
                $employee->user->save();

                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DISABLE_EMP], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Employee.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {

            $employee = Employee::find($request->json('employee.id'));

            if ($employee == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $employee->fill($request->json('employee'));
            $employee->user->fill($request->json('employee.user'));
            $employee->setUpdateRules();

            if (!$employee->validate()) {
                $error = $employee->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                DB::transaction(function () use ($employee) {
                    $employee->user->save();
                    $employee->save();
                });

                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_UPDATE_EMP], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Employee Count.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        try {

            $employeeCount = Employee::whereNotNull('displayId')->get()->count();

            return response()->json(compact('employeeCount'));

        } catch (Exception $e) {
            return response()->json([common::$EMP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Employee.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $employeeId = $request->json('employeeId');

        try {
            if ($employeeId == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $doctor = Employee::find($employeeId);

            if ($doctor == null) {
                return response()->json([common::$EMP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            }

            $doctor->delete();

            return response()->json([common::$SUCCESS]);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_EMP], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search Employees.
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
            $employees = Employee::where($searchBy, 'LIKE', '%' . $searchText . '%')->get();
            return response()->json(compact('employees'));
        } catch (Exception $e) {
            return response()->json([common::$PATIENT_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
