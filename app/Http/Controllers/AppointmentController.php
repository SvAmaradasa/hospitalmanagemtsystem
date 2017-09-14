<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\common;
use App\Company;
use App\Doctor;
use App\Employee;
use App\Enum\AppointmentStatus;
use App\Enum\AppointmentType;
use App\Enum\DoctorType;
use App\Enum\UserRole;
use App\Lab;
use App\Patient;
use App\Scan;
use App\Xray;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use JWTAuth;
use Mockery\CountValidator\Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AppointmentController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth', ['except' => ['upload']]);
    }

    /**
     * Create Appointment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
            } else if ($request->input('appointment') == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            } else if (!AppointmentType::isValidValue($request->input('appointment.appointmentType'))) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $employee = Employee::find($user->employee_id);
            $patients = $this::getPatients($request->input('appointment.patients'));
            $doctor = $request->input('appointment.doctor') != null ? Doctor::find($request->input('appointment.doctor.id')) : null;
            $company = $request->input('appointment.company') != null ? Company::find($request->input('appointment.company.id')) : null;
            $scan = $request->input('appointment.scan') != null ? Scan::find($request->input('appointment.scan.id')) : null;
            $xray = $request->input('appointment.xray') != null ? Xray::find($request->input('appointment.xray.id')) : null;
            $lab = $request->input('appointment.lab') != null ? Lab::find($request->input('appointment.lab.id')) : null;
            $invoices = common::getInvoices($request->input('appointment.invoices'));

            $appointment = new Appointment();
            $appointment->fill($request->input('appointment'));

            if ($company == null) {
                $appointment->appointmentStatus = AppointmentStatus::BRAND_NEW;
            } else {
                $appointment->appointmentStatus = AppointmentStatus::PAID;
            }

            if (!$appointment->validate()) {
                $error = $appointment->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                DB::transaction(function () use ($appointment, $patients, $employee, $doctor, $company, $invoices, $scan, $xray, $lab) {
                    $appointment->employee()->associate($employee);
                    $appointment->doctor()->associate($doctor);
                    $appointment->company()->associate($company);
                    $appointment->scan()->associate($scan);
                    $appointment->xray()->associate($xray);
                    $appointment->lab()->associate($lab);

                    $appointment->save();

                    $appointment->patients()->attach($patients);
                    $appointment->invoices()->saveMany($invoices);

                });

                $appointment->load('Invoices')->load('Patients');

                if ($appointment->appointmentType == AppointmentType::CHANNELLING) {
                    foreach ($appointment->patients as $patient) {
                        if ($patient->mobileNo == null) {
                            continue;
                        }

                        $text = "Ayubowan " . $patient->title . " " . $patient->firstName . " " . $patient->lastName . ". Your Appointment No. " . $appointment->appointmentNo
                            . " with Dr. " . $appointment->doctor->firstName . " " . $appointment->doctor->lastName . " on " . $appointment->date . " at 16.30 is confirmed. Biyagama Private Hospital";
                        common::sms($text, $patient->mobileNo);
                    }
                } else if ($appointment->appointmentType == AppointmentType::SCAN) {
                    $patient = $appointment->patients[0];
                    if ($patient->mobileNo != null) {
                        $text = "Ayubowan " . $patient->title . " " . $patient->firstName . " " . $patient->lastName . ". Your Appointment No. " . $appointment->appointmentNo
                            . " for " . $scan->name . " on " . $appointment->date . " is confirmed. Biyagama Private Hospital";
                        common::sms($text, $patient->mobileNo);
                    }
                }

                return response()->json(compact('appointment'));
            }
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_SAVE], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Patients from id array.
     * @param $patients
     * @return mixed
     */
    private function getPatients($patients)
    {
        $patientIds = [];

        foreach ($patients as $patient) {
            array_push($patientIds, $patient['id']);
        }
        return Patient::whereIn('id', $patientIds)->get();
    }

    /**
     * Get Next Appointment No.
     * @param $appointmentDate
     * @param $doctorId
     * @param $appointmentType
     * @return \Illuminate\Http\JsonResponse
     * @internal param Request $request
     */
    public function getNextAppointmentNo($appointmentDate, $doctorId, $appointmentType)
    {
        try {
            if ($appointmentDate == null || $doctorId == null || $appointmentType == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            } else if (!AppointmentType::isValidValue($appointmentType)) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $appointmentNo = Appointment::where('date', $appointmentDate)->where('appointmentType', $appointmentType)->where('doctor_id', $doctorId)->count() + 1;

            return response()->json(compact('appointmentNo'));
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Next Scan Appointment No
     * @param $appointmentDate
     * @param $scanId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNextScanAppointmentNo($appointmentDate, $scanId)
    {
        try {
            if ($appointmentDate == null || $scanId == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $appointmentNo = Appointment::where('date', $appointmentDate)->where('scan_id', $scanId)->count() + 1;

            return response()->json(compact('appointmentNo'));
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Next X-Ray Appointment No
     * @param $appointmentDate
     * @param $xrayId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNextXrayAppointmentNo($appointmentDate, $xrayId)
    {
        try {
            if ($appointmentDate == null || $xrayId == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $appointmentNo = Appointment::where('date', $appointmentDate)->whereNotNull('xray_id')->count() + 1;

            return response()->json(compact('appointmentNo'));
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointments from date and type.
     * @param $startDate
     * @param $endDate
     * @param $appointmentType
     * @param $doctorId
     * @return \Illuminate\Http\JsonResponse
     * @internal param $date
     */
    public function getAppointmentsFromDate($startDate, $endDate, $appointmentType, $doctorId = null)
    {
        if ($startDate == null || $appointmentType == null || !AppointmentType::isValidValue($appointmentType)) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            if ($doctorId == null) {
                $appointments = Appointment::whereBetween('date', [new Carbon($startDate), new Carbon($endDate)])->where('appointmentType', $appointmentType)
                    ->with('patients')->with('doctor')->with('invoices')->with('company')->get();
            } else {
                $appointments = Appointment::whereBetween('date', [new Carbon($startDate), new Carbon($endDate)])->where('appointmentType', $appointmentType)
                    ->where('doctor_id', $doctorId)->with('patients')->with('doctor')->with('invoices')->with('company')->get();
            }

            if ($appointments == null) {
                return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('appointments'));
            }
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointment Types
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTypes()
    {
        try {
            $appointmentTypes = AppointmentType::getAll();

            return response()->json(compact('appointmentTypes'));

        } catch (Exception $e) {
            return response()->json([common::$APP_TYPE_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Cancel Appointment.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request)
    {
        $appointmentId = $request->input('appointmentId');

        if ($appointmentId == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        $appointment = Appointment::find($appointmentId);

        if ($appointment->appointmentStatus != AppointmentStatus::BRAND_NEW && $appointment->appointmentStatus != AppointmentStatus::PAID) {
            return response()->json([common::$APP_UNABLE_TO_CANCEL], common::$HTTP_BAD_REQUEST);
        }

        try {

            if ($appointment->appointmentStatus == AppointmentStatus::BRAND_NEW) {
                $appointment->appointmentStatus = AppointmentStatus::CANCELED;
            } else {
                $appointment->appointmentStatus = AppointmentStatus::REFUNDED;
            }

            $appointment->save();

            return response()->json([common::$SUCCESS]);

        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_CANCEL], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointment.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        try {
            if (ctype_digit($id)) {
                $appointment = Appointment::findOrFail($id)->with('patients')->with('doctor')->with('invoices')->with('company')->with('scan')->with('xray')->get();
            } else {
                $appointment = Appointment::where('displayId', $id)->with('patients')->with('doctor')->with('invoices')->with('company')->with('scan')->with('xray')->firstOrFail();
            }

            return response()->json(compact('appointment'));
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointment for Scan Report.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForScanReport($id)
    {
        try {
            if (ctype_digit($id)) {
                $appointment = Appointment::where('id', $id)->whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                    ->whereNotNull('scan_id')
                    ->with('patients')->with('scan')
                    ->firstOrFail();
            } else {
                $appointment = Appointment::where('displayId', $id)->whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                    ->whereNotNull('scan_id')
                    ->with('patients')->with('scan')
                    ->firstOrFail();
            }

            return response()->json(compact('appointment'));
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointment for X-Ray Report.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForXrayReport($id)
    {
        try {
            if (ctype_digit($id)) {
                $appointment = Appointment::where('id', $id)->whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                    ->whereNotNull('xray_id')
                    ->with('patients')->with('xray')
                    ->firstOrFail();
            } else {
                $appointment = Appointment::where('displayId', $id)->whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                    ->whereNotNull('xray_id')
                    ->with('patients')->with('xray')
                    ->firstOrFail();
            }

            return response()->json(compact('appointment'));
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointment for Lab Report.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForLabReport($id)
    {
        try {
            if (ctype_digit($id)) {
                $appointment = Appointment::where('id', $id)->whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                    ->whereNotNull('lab_id')
                    ->with('patients')->with('lab')
                    ->firstOrFail();
            } else {
                $appointment = Appointment::where('displayId', $id)->whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                    ->whereNotNull('lab_id')
                    ->with('patients')->with('lab')
                    ->firstOrFail();
            }

            return response()->json(compact('appointment'));
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointment for Diagnose.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForDiagnose($id)
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
        } else if ($user->userRole != UserRole::DOCTOR) {
            return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
        }

        try {
            if ($user->doctor->doctorType == DoctorType::OPD) {
                if (ctype_digit($id)) {
                    $appointment = Appointment::where('id', $id)
                        ->whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                        ->where('appointmentType', AppointmentType::OPD)
                        ->with('patients')->with('doctor')
                        ->firstOrFail();
                } else {
                    $appointment = Appointment::where('displayId', $id)
                        ->whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                        ->where('appointmentType', AppointmentType::OPD)
                        ->with('patients')->with('doctor')
                        ->firstOrFail();
                }
            } else {
                if (ctype_digit($id)) {
                    $appointment = Appointment::where('id', $id)
                        ->whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                        ->with('patients')->with('doctor')
                        ->firstOrFail();
                } else {
                    $appointment = Appointment::where('displayId', $id)
                        ->whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                        ->with('patients')->with('doctor')
                        ->firstOrFail();
                }
            }

            return response()->json(compact('appointment'));
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointment for Drug Issue.
     * @param $appointmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForDrugIssue($appointmentId)
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
        }

        try {
            if (ctype_digit($appointmentId)) {
                $appointment = Appointment::where('id', $appointmentId)
                    ->where('appointmentStatus', AppointmentStatus::IN_PROGRESS)
                    ->whereIn('appointmentType', [AppointmentType::OPD, AppointmentType::CHANNELLING])
                    ->with('prescriptions')
                    ->firstOrFail();
            } else {
                $appointment = Appointment::where('displayId', $appointmentId)
                    ->where('appointmentStatus', AppointmentStatus::IN_PROGRESS)
                    ->whereIn('appointmentType', [AppointmentType::OPD, AppointmentType::CHANNELLING])
                    ->with('prescriptions')
                    ->firstOrFail();
            }

            return response()->json(compact('appointment'));

        } catch (ModelNotFoundException $e) {
            return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointments for Doctor.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForDoctor()
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
        } else if ($user->userRole != UserRole::DOCTOR) {
            return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
        }

        $today = date("Ymd");

        try {
            if ($user->doctor->doctorType == DoctorType::OPD) {
                $appointments = Appointment::where('date', $today)
                    ->where('appointmentType', AppointmentType::OPD)
                    ->whereIn('appointmentStatus', [AppointmentStatus::PAID])
                    ->with('patients')->with('prescriptions')->with('company')
                    ->get();
            } else {
                $appointments = Appointment::where('date', $today)
                    ->where('appointmentType', AppointmentType::CHANNELLING)->where('doctor_id', $user->doctor->id)
                    ->whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                    ->with('patients')->with('doctor')->with('prescriptions')->with('company')
                    ->get();
            }

            if ($appointments == null) {
                return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            }

            return response()->json(compact('appointments'));
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointments for Patient.
     * @param $patientId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForPatient($patientId)
    {
        if ($patientId == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $appointments = Appointment::where('appointmentStatus', AppointmentStatus::CLOSE)
                ->with(['patients' => function ($query) use ($patientId) {
                    $query->where('id', $patientId);
                }])
                ->with('doctor')
                ->with(['prescriptions' => function ($query) use ($patientId) {
                    $query->where('patient_id', $patientId);
                }])
                ->with('scan')->with('xray')
                ->orderBy('date', 'desc')
                ->get();

            if ($appointments == null) {
                return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            }

            return response()->json(compact('appointments'));
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointments for Scan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForScan()
    {
        try {
            $appointments = Appointment::whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                ->whereNotNull('scan_id')
                ->with('patients')->with('scan')
                ->get();

            if ($appointments == null) {
                return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            }

            return response()->json(compact('appointments'));
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointments for Scan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForXray()
    {
        try {
            $appointments = Appointment::whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                ->whereNotNull('xray_id')
                ->with('patients')->with('xray')
                ->get();

            if ($appointments == null) {
                return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            }

            return response()->json(compact('appointments'));
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointments for Lab
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForLab()
    {
        try {
            $appointments = Appointment::whereIn('appointmentStatus', [AppointmentStatus::PAID, AppointmentStatus::IN_PROGRESS])
                ->whereNotNull('lab_id')
                ->with('patients')->with('lab')
                ->get();

            if ($appointments == null) {
                return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            }

            return response()->json(compact('appointments'));
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Close Appointment.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function close(Request $request)
    {
        $appointmentId = $request->input('appointmentId');

        if ($appointmentId == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        $appointment = Appointment::find($appointmentId);

        if ($appointment->appointmentStatus != AppointmentStatus::IN_PROGRESS) {
            return response()->json([common::$APP_UNABLE_TO_CLOSE], common::$HTTP_BAD_REQUEST);
        }

        try {
            $appointment->appointmentStatus = AppointmentStatus::CLOSE;
            $appointment->save();

            return response()->json([common::$SUCCESS]);

        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_CANCEL], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointment distribution of the month.
     * @return \Illuminate\Http\JsonResponse
     */
    public function appointmentCountOfTheMonth()
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
        }

        try {
            $appointments = Appointment::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get([DB::raw('DATE(created_at) as date'), DB::raw('count(id) as count')]);

            if ($appointments == null) {
                return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            }

            return response()->json(compact('appointments'));
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get the appointment distribution of the current date.
     * @return \Illuminate\Http\JsonResponse
     */
    public function appointmentDistribution()
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
            return response()->json([common::$USER_NOT_FOUND], common::$HTTP_NOT_AUTHORIZED);
        }

        try {
            $appointments = Appointment::where('date', Carbon::today()->toDateString())
                ->groupBy('appointmentType')
                ->get(['appointmentType', DB::raw('count(id) as count')]);

            if ($appointments == null) {
                return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            }

            return response()->json(compact('appointments'));
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Appointments for Daily Income Report
     * @param $startDate
     * @param $endDate
     * @return \Illuminate\Http\JsonResponse
     * @internal param $date
     */
    public function getAppointmentsForIncomeReport($startDate, $endDate)
    {
        if ($startDate == null || $endDate == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $appointments = Appointment::whereBetween('created_at', [new Carbon($startDate), new Carbon($endDate)])->whereIn('appointmentStatus', ['Paid', 'In Progress', 'Close'])
                ->with('patients')->with('doctor')->with('invoices')->with('company')->get();

            if ($appointments == null) {
                return response()->json([common::$APP_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('appointments'));
            }
        } catch (Exception $e) {
            return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Report Upload
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {

            $appointmentId = $request->input('appointmentId');

            if ($appointmentId == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $appointment = Appointment::find($appointmentId);

            if (!($appointment->appointmentStatus == AppointmentStatus::PAID || $appointment->appointmentStatus == AppointmentStatus::IN_PROGRESS)) {
                return response()->json([common::$APP_UNABLE_TO_ADD_REPORT], common::$HTTP_BAD_REQUEST);
            }

            $fileName = $appointment->displayId . '.' . $request->file('file')->extension();

            try {
                if ($appointment->appointmentType == AppointmentType::SCAN) {

                    $request->file('file')->move(common::$SCAN_REPORT_LOCATION, $fileName);
                    $appointment->report = common::$SCAN_REPORT_LOCATION . '/' . $fileName;

                } else if ($appointment->appointmentType == AppointmentType::X_RAY) {

                    $request->file('file')->move(common::$XRAY_REPORT_LOCATION, $fileName);
                    $appointment->report = common::$XRAY_REPORT_LOCATION . '/' . $fileName;

                } else if ($appointment->appointmentType == AppointmentType::LAB) {

                    $request->file('file')->move(common::$LAB_REPORT_LOCATION, $fileName);
                    $appointment->report = common::$LAB_REPORT_LOCATION . '/' . $fileName;
                }

                $appointment->appointmentStatus = AppointmentStatus::IN_PROGRESS;
                $appointment->save();

            } catch (FileException $e) {
                return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
            } catch (Exception $e) {
                return response()->json([common::$APP_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        return response()->json(['success' => true], 200);
    }
}
