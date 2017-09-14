<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;

/**
 * App\Payment
 *
 * @property integer $id
 * @property integer $invoice_id
 * @property float $paidAmount
 * @property string $paymentMethod
 * @property string $cardNo
 * @property string $reference
 * @property integer $appointment_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Appointment $appointment
 * @method static Builder|Payment whereId($value)
 * @method static Builder|Payment wherePaidAmount($value)
 * @method static Builder|Payment wherePaymentMethod($value)
 * @method static Builder|Payment whereCardNo($value)
 * @method static Builder|Payment whereReference($value)
 * @method static Builder|Payment whereCreatedAt($value)
 * @method static Builder|Payment whereUpdatedAt($value)
 * @method static Builder|Payment whereAppointmentId($value)
 * @mixin \Eloquent
 * @property integer $employee_id
 * @property-read Employee $employee
 * @method static Builder|Payment whereEmployeeId($value)
 * @property integer $prescription_id
 * @property-read \App\Prescription $prescription
 * @method static \Illuminate\Database\Query\Builder|\App\Payment wherePrescriptionId($value)
 */
class Payment extends BaseModel
{
    /**
     * define which attributes are mass assignable
     * @var array
     */
    protected $fillable = ['paidAmount', 'paymentMethod', 'cardNo', 'reference'];

    /**
     * create validation rules
     **/
    protected $validationRules = [
        'appointmentId' => 'integer|exists:appointments,id',
        'paidAmount' => 'required|numeric|max:999999999999.99|min:0.10',
        'paymentMethod' => 'required|in:Cash,Visa,Master,Amex',
        'cardNo' => 'numeric|digits:16',
        'reference' => 'numeric|digits:6'
    ];

    /**
     * Get the associated Appointment.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function appointment()
    {
        return $this->belongsTo('App\Appointment');
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
     * Get associated Prescription.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prescription()
    {
        return $this->belongsTo('App\Prescription');
    }
}
