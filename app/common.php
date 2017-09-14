<?php
/**
 * Created by PhpStorm.
 * User: Madhawa Ariyarathna
 * Date: 8/14/2016
 * Time: 10:14 AM
 */

namespace App;


use Hash;

class common
{
    /**
     * Default Parameters
     * @var string
     */
    public static $DEFAULT_EMAIL = 'madhawa.sampath@gmail.com';
    public static $SCAN_REPORT_LOCATION = 'reports/scan';
    public static $XRAY_REPORT_LOCATION = 'reports/xray';
    public static $LAB_REPORT_LOCATION = 'reports/lab';

    /**
     * HTTP Statuses.
     * @var int
     */
    public static $HTTP_BAD_REQUEST = 400;
    public static $HTTP_NOT_AUTHORIZED = 401;
    public static $HTTP_NOT_FOUND = 404;
    public static $HTTP_INTERNAL_SERVER_ERROR = 500;
    public static $HTTP_SUCCESS = 200;

    /**
     * Common Messages.
     * @var string
     */
    public static $INVALID_PARAMETERS = 'Invalid Parameters';
    public static $SUCCESS = 'Success';
    public static $INVALID_SEARCH_BY = 'Invalid Search By Field';

    /**
     * User Messages.
     * @var string
     */
    public static $USER_NOT_FOUND = 'User Not Found';
    public static $USER_DISABLED = 'User Account Disabled';
    public static $UNABLE_TO_GET_USER = 'Unable to get Authenticated User';
    public static $UNABLE_TO_CREATE_TOKEN = 'Could Not Create Token';
    public static $TOKEN_ABSENT = 'Token Absent';
    public static $TOKEN_INVALID = 'Token Invalid';
    public static $UNABLE_TO_REFRESH_TOKEN = 'Token Cannot be Refreshed, Please LogIn again';
    public static $UNABLE_TO_GET_REGISTER_TOKEN = 'Unable to get Register Token';
    public static $REGISTER_TOKEN_NOT_FOUND = 'Register Token Not Found';

    /**
     * Appointment Messages.
     * @var string
     */
    public static $APP_UNABLE_TO_SAVE = 'Could Not Save Appointment Information';
    public static $APP_UNABLE_TO_GET = 'Could Not Retrieve Appointment Information';
    public static $APP_NOT_FOUND = 'Appointment Information Not Found';
    public static $APP_TYPE_UNABLE_TO_GET = 'Could Not Retrieve Appointment Type Information';
    public static $APP_UNABLE_TO_SAVE_PAYMENT = 'Could not make the Payment';
    public static $APP_UNABLE_TO_CANCEL = 'Could not cancel the Appointment';
    public static $APP_UNABLE_TO_CLOSE = 'Could not close the Appointment';
    public static $APP_UNABLE_TO_ADD_REPORT = 'Could not add report to the Appointment';

    /**
     * Employee Messages.
     * @var string
     */
    public static $EMP_UNABLE_TO_GET = 'Could Not Retrieve Employee Information';
    public static $EMP_NOT_FOUND = 'Employee Information Not Found';
    public static $UNABLE_TO_SAVE_EMP = 'Could Not Save Employee Information';
    public static $UNABLE_TO_UPDATE_EMP = 'Could Not Update Employee Information';
    public static $UNABLE_TO_DISABLE_EMP = 'Could Not Inactivate Employee';
    public static $UNABLE_TO_ENABLE_EMP = 'Could Not Activate Employee';
    public static $EMP_ROLE_NOT_FOUND = 'Employee Role Information Not Found';
    public static $EMP_ROLE_UNABLE_TO_GET = 'Could Not Retrieve Employee Role Information';
    public static $UNABLE_TO_DELETE_EMP = 'Could Not Delete Employee';

    /**
     * Doctor Messages.
     * @var string
     */
    public static $DOC_UNABLE_TO_GET = 'Could Not Retrieve doctor Information';
    public static $DOC_NOT_FOUND = 'Doctor Information Not Found';
    public static $UNABLE_TO_SAVE_DOC = 'Could Not Save Doctor Information';
    public static $UNABLE_TO_UPDATE_DOC = 'Could Not Update Doctor Information';
    public static $UNABLE_TO_DELETE_DOC = 'Could Not Delete Doctor';
    public static $UNABLE_TO_DISABLE_DOC = 'Could Not Inactivate Doctor';
    public static $UNABLE_TO_ENABLE_DOC = 'Could Not Activate Doctor';

    /**
     * Patient Messages.
     * @var string
     */
    public static $PATIENT_UNABLE_TO_GET = 'Could Not Retrieve Patient Information';
    public static $PATIENT_NOT_FOUND = 'Patient Information Not Found';
    public static $UNABLE_TO_SAVE_PATIENT = 'Could Not Save Patient Information';
    public static $UNABLE_TO_UPDATE_PATIENT = 'Could Not Update Patient Information';
    public static $UNABLE_TO_DELETE_PATIENT = 'Could Not Delete Patient';

    /**
     * Company Messages.
     * @var string
     */
    public static $UNABLE_TO_SAVE_COMPANY = 'Could Not Save Company Information';
    public static $COMPANY_NOT_FOUND = 'Company Information Not Found';
    public static $COMPANY_UNABLE_TO_GET = 'Could Not Retrieve Company Information';
    public static $UNABLE_TO_UPDATE_COMPANY = 'Could Not Update Company Information';
    public static $UNABLE_TO_DELETE_COMPANY = 'Could Not Delete Company';

    /**
     * Fee Messages.
     * @var string
     */
    public static $FEE_TYPE_UNABLE_TO_GET = 'Could Not Retrieve Fee Type Information';
    public static $INVALID_FEE_TYPE = 'Invalid Fee Type';
    public static $FEE_UNABLE_TO_GET = 'Could Not Retrieve Fee Information';
    public static $UNABLE_TO_SAVE_FEE = 'Could Not Save Fee Information';
    public static $UNABLE_TO_UPDATE_FEE = 'Could Not Update Fee Information';
    public static $FEE_NOT_FOUND = 'Fee Information Not Found';
    public static $UNABLE_TO_DELETE_FEE = 'Could Not Delete Fee';

    /**
     * Drug Messages.
     * @var string
     */
    public static $UNABLE_TO_SAVE_DRUG = 'Could Not Save Drug Information';
    public static $DRUG_NOT_FOUND = 'Drug Information Not Found';
    public static $DRUG_UNABLE_TO_GET = 'Could Not Retrieve Drug Information';
    public static $UNABLE_TO_DELETE_DRUG = 'Could Not Delete Drug';
    public static $UNABLE_TO_UPDATE_DRUG = 'Could Not Update Drug Information';
    public static $DRUG_SCHEDULE_UNABLE_TO_GET = 'Could Not Retrieve Drug Schedule Information';

    /**
     * Prescription Messages.
     * @var string
     */
    public static $UNABLE_TO_SAVE_PRESCRIPTION = 'Could Not Save Prescription Information';
    public static $PRESCRIPTION_NOT_FOUND = 'Prescription Information Not Found';
    public static $PRESCRIPTION_UNABLE_TO_GET = 'Could Not Retrieve Prescription Information';
    public static $UNABLE_TO_DELETE_PRESCRIPTION = 'Could Not Delete Prescription';
    public static $UNABLE_TO_UPDATE_PRESCRIPTION = 'Could Not Update Prescription Information';

    /**
     * Scan Messages.
     * @var string
     */
    public static $UNABLE_TO_SAVE_SCAN = 'Could Not Save Scan Information';
    public static $SCAN_NOT_FOUND = 'Scan Information Not Found';
    public static $SCAN_UNABLE_TO_GET = 'Could Not Retrieve Scan Information';
    public static $UNABLE_TO_DELETE_SCAN = 'Could Not Delete Scan';
    public static $UNABLE_TO_UPDATE_SCAN = 'Could Not Update Scan Information';

    /**
     * Lab Messages.
     * @var string
     */
    public static $UNABLE_TO_SAVE_LAB = 'Could Not Save Lab Information';
    public static $LAB_NOT_FOUND = 'Lab Information Not Found';
    public static $LAB_UNABLE_TO_GET = 'Could Not Retrieve Lab Information';
    public static $UNABLE_TO_DELETE_LAB = 'Could Not Delete Lab';
    public static $UNABLE_TO_UPDATE_LAB = 'Could Not Update Lab Information';

    /**
     * Doctor Search by fields.
     * @var array
     */
    public static $DOCTOR_SEARCH_BY_FIELDS = ["displayId", "firstName", "lastName", "mobileNo"];

    /**
     * Patient Search by fields.
     * @var array
     */
    public static $PATIENT_SEARCH_BY_FIELDS = ["displayId", "firstName", "lastName", "mobileNo"];

    /**
     * Company Search by fields.
     * @var array
     */
    public static $COMPANY_SEARCH_BY_FIELDS = ["displayId", "name", "address", "telephoneNo"];

    /**
     * Drug Schedules
     * @var array
     */
    public static $DRUG_SCHEDULE = [
        'Once a Day',
        'Twice a Day',
        'Three Times Per Day',
        'Every 6 Hours',
        'At Night',
        'At Morning'
    ];

    /**
     * Encrypt Password.
     * @param $password
     * @return mixed
     */
    public static function encryptPassword($password)
    {
        return Hash::make($password);
    }

    /**
     * Generates Unique Id.
     * @return string
     */
    public static function getConfirmationId()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * Send SMS.
     * @param $text .
     * @param $to
     */
    public static function sms($text, $to)
    {
        $content = "https://www.smsglobal.com/http-api.php?user="
            . rawurlencode("fpt1tlmu") . "&password="
            . rawurlencode("wWMo2Ee4") . "&from="
            . rawurlencode("HMS") . "&to="
            . rawurlencode("94" . ltrim($to, '0')) . "&text="
            . rawurlencode($text) . "&action=sendsms";

        $smsglobal_response = file_get_contents($content);
    }

    /**
     * Get Invoices from Fees (JSON)
     * @param $invoicesData
     * @param null $prescription
     * @return array
     */
    public static function getInvoices($invoicesData, $prescription = null)
    {
        $invoices = [];

        if (common::is_multi_array($invoicesData)) {

            foreach ($invoicesData as $invoice) {
                array_push($invoices, static::getInvoice($invoice, $prescription));
            }
        } else {
            array_push($invoices, static::getInvoice($invoicesData, $prescription));
        }

        return $invoices;
    }

    /**
     * Check for multidimensional array.
     * @param $arr
     * @return bool
     */
    public static function is_multi_array($arr)
    {
        rsort($arr);
        return isset($arr[0]) && is_array($arr[0]);
    }

    /**
     * Get Invoice from Fee Json.
     * @param $invoiceData
     * @param null $prescription
     * @return Invoice
     */
    private static function getInvoice($invoiceData, $prescription = null)
    {
        $invoice = new Invoice();
        $invoice->qty = $invoiceData['qty'];

        if (isset($invoiceData['fee']['id'])) {
            $fee = Fee::find($invoiceData['fee']['id']);
            $invoice->fee()->associate($fee);

            if ($fee->isVariable) {
                $invoice->feeValue = $invoiceData['feeValue'];
            } else {
                $invoice->feeValue = $fee->fee;
            }
        } else {
            $drug = Drug::find($invoiceData['drug']['id']);
            $invoice->drug()->associate($drug);
            $invoice->feeValue = $invoiceData['feeValue'];
        }

        $invoice->prescription()->associate($prescription);

        return $invoice;
    }
}