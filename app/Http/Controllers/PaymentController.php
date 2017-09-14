<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\common;
use App\Employee;
use App\Enum\AppointmentStatus;
use App\Payment;
use App\Prescription;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use JWTAuth;
use Mockery\Exception;

class PaymentController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Create Payment.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {

            $prescription = null;
            $invoices = null;

            //validate request
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
            } else if ($request->input('payment') == null || ($request->input('payment.appointment') == null && $request->input('payment.prescription') == null)) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $employee = Employee::findOrFail($user->employee_id);

            if ($request->input('payment.appointment.id') != null) {
                $appointment = Appointment::where('id', $request->input('payment.appointment.id'))->firstOrFail();
            } else {
                $prescription = Prescription::where('id', $request->input('payment.prescription.id'))->firstOrFail();
                $appointment = $prescription->appointment;
                $invoices = common::getInvoices($request->input('payment.prescription.invoices'), $prescription);
            }

            $payment = new Payment();
            $payment->fill($request->input('payment'));

            if (!$payment->validate()) {
                $error = $payment->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                DB::transaction(function () use ($appointment, $employee, $payment, $prescription, $invoices) {
                    if ($prescription == null) {
                        $appointment->appointmentStatus = AppointmentStatus::PAID;
                        $appointment->save();
                    } else {
                        $prescription->paid = true;
                        $prescription->save();

                        $payment->prescription()->associate($prescription);

                        $appointment->save();
                        $appointment->invoices()->saveMany($invoices);
                    }
                    $payment->appointment()->associate($appointment);
                    $payment->employee()->associate($employee);

                    $payment->save();
                });

                if ($prescription != null) {
                    return response()->json([common::$SUCCESS]);
                } else {
                    $appointment->load('Invoices')->load('Doctor')->load('Patients')->load('scan')->load('xray')->load('lab');
                    return response()->json(compact('appointment'));
                }
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$APP_NOT_FOUND], common::$HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_SAVE_PAYMENT], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
