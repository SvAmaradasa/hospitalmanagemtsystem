<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;


/**
 * App\Invoice
 *
 * @property integer $id
 * @property integer $appointment_id
 * @property integer $fee_id
 * @property float $feeValue
 * @property integer $qty
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Appointment $appointment
 * @property-read Fee $fee
 * @property-read Drug $drugs
 * @method static Builder|Invoice whereId($value)
 * @method static Builder|Invoice whereAppointmentId($value)
 * @method static Builder|Invoice whereFeeId($value)
 * @method static Builder|Invoice whereFeeValue($value)
 * @method static Builder|Invoice whereQty($value)
 * @method static Builder|Invoice whereCreatedAt($value)
 * @method static Builder|Invoice whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property integer $drug_id
 * @method static \Illuminate\Database\Query\Builder|\App\Invoice whereDrugId($value)
 * @property integer $prescription_id
 * @property-read \App\Prescription $prescription
 * @property-read \App\Drug $drug
 * @method static \Illuminate\Database\Query\Builder|\App\Invoice wherePrescriptionId($value)
 */
class Invoice extends BaseModel
{
    /**
     * mass assignment
     * define which attributes are mass assignable
     **/
    protected $fillable = ['feeValue', 'qty'];

    /**
     * create validation rules
     **/
    protected $validationRules = [
        'appointmentId' => 'required|integer|exists:appointments,id',
        'feeId' => 'required|integer|exists:fees,id',
        'feeValue' => 'required|numeric|max:99999.99|min:0.10',
        'qty' => 'required|integer|max:32767|min:1'
    ];

    /**
     * update validation rules
     **/
    protected $updateRules = [
        'appointmentId' => 'required|integer|exists:appointments,id',
        'feeId' => 'required|integer|exists:fees,id',
        'feeValue' => 'required|numeric|max:99999.99|min:0.10',
        'qty' => 'required|integer|max:32767|min:1'
    ];

    /**
     * Automatically Loading Relationships.
     * @var array
     */
    protected $with = ['Fee', 'Drug'];

    /**
     * Get Associated Appointment.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function appointment()
    {
        return $this->belongsTo('App\Appointment');
    }

    /**
     * Get associated Fee.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fee()
    {
        return $this->belongsTo('App\Fee');
    }

    /**
     * Get Associate Drugs.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function drugs()
    {
        return $this->belongsTo('App\Drug');
    }

    /**
     * Get associated Prescription.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prescription()
    {
        return $this->belongsTo('App\Prescription');
    }

    /**
     * Get associated Drug.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function drug()
    {
        return $this->belongsTo('App\Drug');
    }
}
