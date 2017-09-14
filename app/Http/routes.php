<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//This will return a PHP file that will hold all of our Angular content
Route::get('/', function () {
    return view('index');
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| This route group handles API routing.
| URLs begin with 'api' prefix
|
*/
Route::group(['prefix' => 'api/emp'], function () {
    Route::get('', 'EmployeeController@getAll');
    Route::post('', 'EmployeeController@create');
    Route::put('', 'EmployeeController@update');
    Route::delete('', 'EmployeeController@delete');
    Route::get('roles', 'EmployeeController@getAllEmployeeRoles');
    Route::get('count', 'EmployeeController@count');
    Route::post('status', 'EmployeeController@toggleStatus');
    Route::post('search', 'EmployeeController@search');
    Route::get('{id}', 'EmployeeController@get');

});

Route::group(['prefix' => 'api/auth'], function () {
    Route::post('login', 'AuthenticationController@authenticate');
    Route::get('user', 'AuthenticationController@getAuthenticatedUser');
    Route::post('validateToken', 'AuthenticationController@validateToken');
    Route::post('register', 'AuthenticationController@register');
});

Route::group(['prefix' => 'api/common'], function () {
    Route::get('title', 'CommonController@getAllTitles');
    Route::get('marital', 'CommonController@getAllMaritalStatuses');
    Route::get('gender', 'CommonController@getAllGenders');
});

Route::group(['prefix' => 'api/patient'], function () {
    Route::post('', 'PatientController@create');
    Route::put('', 'PatientController@update');
    Route::post('search', 'PatientController@search');
    Route::get('', 'PatientController@getAll');
    Route::get('count', 'PatientController@count');
    Route::get('{id}', 'PatientController@get');
    Route::delete('', 'PatientController@delete');
});

Route::group(['prefix' => 'api/doctor'], function () {
    Route::post('', 'DoctorController@create');
    Route::get('', 'DoctorController@getAll');
    Route::delete('', 'DoctorController@delete');
    Route::put('', 'DoctorController@update');
    Route::get('specialties', 'DoctorController@getAllDoctorSpecialties');
    Route::get('types', 'DoctorController@getAllDoctorTypes');
    Route::get('count', 'DoctorController@count');
    Route::post('search', 'DoctorController@search');
    Route::post('status', 'DoctorController@toggleStatus');
    Route::get('{id}', 'DoctorController@get');
    Route::get('type/{type}', 'DoctorController@getDoctorsByType');
});

Route::group(['prefix' => 'api/appointment'], function () {
    Route::get('nextNo/{appointmentDate}/{doctorId}/{appointmentType}', 'AppointmentController@getNextAppointmentNo');
    Route::get('scan/nextNo/{appointmentDate}/{scanId}', 'AppointmentController@getNextScanAppointmentNo');
    Route::get('xray/nextNo/{appointmentDate}/{xrayId}', 'AppointmentController@getNextXrayAppointmentNo');
    Route::get('type', 'AppointmentController@getTypes');
    Route::get('doctor/today', 'AppointmentController@getForDoctor');
    Route::get('patient/{id}', 'AppointmentController@getForPatient');
    Route::get('scan', 'AppointmentController@getForScan');
    Route::get('xray', 'AppointmentController@getForXray');
    Route::get('lab', 'AppointmentController@getForLab');
    Route::post('upload', 'AppointmentController@upload');
    Route::get('drugIssue/{appointmentId}', 'AppointmentController@getForDrugIssue');
    Route::get('scanReport/{appointmentId}', 'AppointmentController@getForScanReport');
    Route::get('xrayReport/{appointmentId}', 'AppointmentController@getForXrayReport');
    Route::get('labReport/{appointmentId}', 'AppointmentController@getForLabReport');
    Route::get('report/income/{startDate}/{endDate}', 'AppointmentController@getAppointmentsForIncomeReport');
    Route::post('', 'AppointmentController@create');
    Route::delete('', 'AppointmentController@cancel');
    Route::get('{startDate}/{endDate}/{appointmentType}/{doctorId?}', 'AppointmentController@getAppointmentsFromDate');
    Route::get('{id}', 'AppointmentController@get');
    Route::get('diagnose/{id}', 'AppointmentController@getForDiagnose');
    Route::post('close', 'AppointmentController@close');
    Route::get('count/month', 'AppointmentController@appointmentCountOfTheMonth');
    Route::get('count/today', 'AppointmentController@appointmentDistribution');
});

Route::group(['prefix' => 'api/feeType'], function () {
    Route::get('', 'FeeController@getFeeTypes');
});

Route::group(['prefix' => 'api/fee'], function () {
    Route::get('type/{type}', 'FeeController@getFeeFromFeeType');
    Route::get('{id}', 'FeeController@get');
    Route::post('', 'FeeController@create');
    Route::put('', 'FeeController@update');
    Route::delete('', 'FeeController@delete');
});

Route::group(['prefix' => 'api/company'], function () {
    Route::post('', 'CompanyController@create');
    Route::get('', 'CompanyController@getAll');
    Route::delete('', 'CompanyController@delete');
    Route::put('', 'CompanyController@update');
    Route::get('{id}', 'CompanyController@get');
    Route::get('count', 'CompanyController@count');
    Route::post('search', 'CompanyController@search');
});

Route::group(['prefix' => 'api/drug'], function () {
    Route::post('', 'DrugController@create');
    Route::post('stock', 'DrugController@updateStock');
    Route::get('', 'DrugController@getAll');
    Route::delete('', 'DrugController@delete');
    Route::put('', 'DrugController@update');
    Route::get('schedule', 'DrugController@getDrugSchedules');
    Route::get('{id}', 'DrugController@get');
});

Route::group(['prefix' => 'api/roster'], function () {
    Route::post('', 'ScheduleController@create');
    Route::get('', 'ScheduleController@getAll');
    Route::delete('', 'ScheduleController@delete');
    Route::get('{id}', 'ScheduleController@get');
});

Route::group(['prefix' => 'api/prescription'], function () {
    Route::post('', 'PrescriptionController@create');
    Route::get('', 'PrescriptionController@getAll');
    Route::delete('', 'PrescriptionController@delete');
    Route::put('', 'PrescriptionController@update');
    Route::get('{id}', 'PrescriptionController@get');
    Route::get('/drugIssue/{appointmentId}', 'PrescriptionController@getForDrugIssue');
    Route::post('/issue', 'PrescriptionController@issue');
    Route::get('/{appointmentId}/{id}', 'PrescriptionController@getForUpdate');
});

Route::group(['prefix' => 'api/payment'], function () {
    Route::post('', 'PaymentController@create');
});

Route::group(['prefix' => 'api/scan'], function () {
    Route::post('', 'ScanController@create');
    Route::get('', 'ScanController@getAll');
    Route::delete('', 'ScanController@delete');
    Route::put('', 'ScanController@update');
    Route::get('{id}', 'ScanController@get');
});

Route::group(['prefix' => 'api/xray'], function () {
    Route::post('', 'XrayController@create');
    Route::get('', 'XrayController@getAll');
    Route::delete('', 'XrayController@delete');
    Route::put('', 'XrayController@update');
    Route::get('{id}', 'XrayController@get');
});

Route::group(['prefix' => 'api/lab'], function () {
    Route::post('', 'LabController@create');
    Route::get('', 'LabController@getAll');
    Route::delete('', 'LabController@delete');
    Route::put('', 'LabController@update');
    Route::get('{id}', 'LabController@get');
});
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Catch all other routes and redirect them to angularJs.
| AngularJs will Handle routes then
|
*/

Route::any('/{any}', function () {
    return View('index');
})->where('any', '.*');