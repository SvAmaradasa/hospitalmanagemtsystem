<?php

namespace App;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;


/**
 * App\Appointment
 *
 * @property integer $id
 * @property string $displayId
 * @property integer $appointmentNo
 * @property string $date
 * @property string $appointmentStatus
 * @property integer $doctor_id
 * @property string $appointmentType
 * @property integer $company_id
 * @property integer $employee_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Doctor $doctor
 * @property-read Collection|Invoice[] $invoices
 * @property-read Company $company
 * @property-read Collection|Patient[] $patients
 * @property-read Employee $employee
 * @property-read Payment $payment
 * @method static Builder|Appointment whereId($value)
 * @method static Builder|Appointment whereDisplayId($value)
 * @method static Builder|Appointment whereAppointmentNo($value)
 * @method static Builder|Appointment whereDate($value)
 * @method static Builder|Appointment whereAppointmentStatus($value)
 * @method static Builder|Appointment whereDoctorId($value)
 * @method static Builder|Appointment whereAppointmentType($value)
 * @method static Builder|Appointment whereCompanyId($value)
 * @method static Builder|Appointment whereEmployeeId($value)
 * @method static Builder|Appointment whereCreatedAt($value)
 * @method static Builder|Appointment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Prescription[] $prescriptions
 */
class Appointment extends BaseModel
{
    /**
     * Is display id auto generated
     * */
    protected static $displayIdAutoGenerate = true;

    /**
     * Pattern of display id
     * */
    protected static $displayIdPattern = ['prefix' => 'APP', 'digits' => 18, 'firstNo' => 1];

    /**
     * define which attributes are mass assignable
     * @var array
     */
    protected $fillable = ['displayId', 'appointmentNo', 'date', 'appointmentStatus', 'appointmentType'];

    /**
     * create validation rules
     **/
    protected $validationRules = [
        'appointmentType' => 'required|in:OPD,Channelling,Lab,Scan,X-Ray',
        'appointmentStatus' => 'required|in:New,Paid',
        'companyId' => 'integer|exists:company,id'
    ];

    /**
     * Set Date and Appointment number.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->appointmentType == 'OPD') {
                $date = new DateTime();
                $model->date = $date->format('Y-m-d');
            }

            if ($model->doctorId) {
                $model->appointmentNo = $model::where('date', $model->date)->where('appointmentType', $model->appointmentType)->where('doctorId', $model->doctorId)->count() + 1;
            } else {
                $model->appointmentNo = $model::where('date', $model->date)->where('appointmentType', $model->appointmentType)->count() + 1;
            }
        });
    }

    /**
     * Get the Doctor Associated with the Appointment.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctor()
    {
        return $this->belongsTo('App\Doctor');
    }

    /**
     * Get the Invoice Associated with the Appointment.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    /**
     * Get the Company Associated with the Appointment.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    /**
     * Get patients Associated with the Appointment.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function patients()
    {
        return $this->belongsToMany('App\Patient', 'appointment_details', 'appointment_id', 'patient_id');
    }

    /**
     * Get Employee associated with the Appointment.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo('App\Employee');
    }

    /**
     * Get associated Payment.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment()
    {
        return $this->hasOne('App\Payment');
    }

    /**
     * Get associated Prescriptions.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prescriptions()
    {
        return $this->hasMany('App\Prescription');
    }

    /**
     * Get associated Scan
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function scan()
    {
        return $this->belongsTo('App\Scan');
    }

    /**
     * Get associated Xray
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function xray()
    {
        return $this->belongsTo('App\Xray');
    }

    public function lab()
    {
        return $this->belongsTo('App\Lab');
    }
}
