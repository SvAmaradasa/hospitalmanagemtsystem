<?php

namespace App;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

/**
 * App\Prescription
 *
 * @property integer $id
 * @property integer $appointment_id
 * @property integer $doctor_id
 * @property integer $patient_id
 * @property string $symptoms
 * @property string $diagnosis
 * @property boolean $issued
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection|Drug[] $drugs
 * @property-read Appointment $appointment
 * @property-read Doctor $doctor
 * @property-read Patient $patient
 * @method static Builder|Prescription whereId($value)
 * @method static Builder|Prescription whereAppointmentId($value)
 * @method static Builder|Prescription whereDoctorId($value)
 * @method static Builder|Prescription wherePatientId($value)
 * @method static Builder|Prescription whereSymptoms($value)
 * @method static Builder|Prescription whereDiagnosis($value)
 * @method static Builder|Prescription whereCreatedAt($value)
 * @method static Builder|Prescription whereUpdatedAt($value)
 * @method static Builder|Prescription whereIssued($value)
 * @method static Builder|Prescription wherePaid($value)
 * @mixin Eloquent
 * @property boolean $paid
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Invoice[] $invoices
 * @property-read \App\Payment $payment
 */
class Prescription extends BaseModel
{

    /**
     * mass assignment
     * define which attributes are mass assignable
     **/
    protected $fillable = ['symptoms', 'diagnosis', 'issued', 'paid'];

    /**
     * create validation rules
     **/
    protected $validationRules = [
        'symptoms' => 'required|string',
        'diagnosis' => 'required|string'
    ];

    /**
     * Automatically Loading Relationships.
     * @var array
     */
    protected $with = ['Drugs', 'Doctor', 'patient', 'invoices'];

    /**
     * Get Drugs associated.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function drugs()
    {
        return $this->belongsToMany('App\Drug', 'prescription_details', 'prescription_id', 'drug_id')->withPivot('days', 'schedule', 'note');
    }

    /**
     * Get Appointment associated.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function appointment()
    {
        return $this->belongsTo('App\Appointment');
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
     * Get the Patient Associated.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function patient()
    {
        return $this->belongsTo('App\Patient');
    }

    /**
     * Get associated Invoices.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    /**
     * Get associated Payment.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment()
    {
        return $this->hasOne('App\Payment');
    }
}
