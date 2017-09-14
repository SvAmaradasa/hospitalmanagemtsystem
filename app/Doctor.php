<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

/**
 * App\Doctor
 *
 * @property integer $id
 * @property string $displayId
 * @property string $title
 * @property string $firstName
 * @property string $middleName
 * @property string $lastName
 * @property string $gender
 * @property string $maritalStatus
 * @property string $address
 * @property string $city
 * @property string $birthday
 * @property string $nic
 * @property string $telephoneNo
 * @property string $mobileNo
 * @property string $email
 * @property string $degree
 * @property string $doctorType
 * @property integer $doctor_specialty_id
 * @property string $hospital
 * @property float $fees
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read DoctorSpecialty $doctorSpecialty
 * @property-read Collection|Appointment[] $appointments
 * @method static Builder|Doctor whereId($value)
 * @method static Builder|Doctor whereDisplayId($value)
 * @method static Builder|Doctor whereTitle($value)
 * @method static Builder|Doctor whereFirstName($value)
 * @method static Builder|Doctor whereMiddleName($value)
 * @method static Builder|Doctor whereLastName($value)
 * @method static Builder|Doctor whereGender($value)
 * @method static Builder|Doctor whereMaritalStatus($value)
 * @method static Builder|Doctor whereAddress($value)
 * @method static Builder|Doctor whereCity($value)
 * @method static Builder|Doctor whereBirthday($value)
 * @method static Builder|Doctor whereNic($value)
 * @method static Builder|Doctor whereTelephoneNo($value)
 * @method static Builder|Doctor whereMobileNo($value)
 * @method static Builder|Doctor whereEmail($value)
 * @method static Builder|Doctor whereDegree($value)
 * @method static Builder|Doctor whereDoctorType($value)
 * @method static Builder|Doctor whereDoctorSpecialtyId($value)
 * @method static Builder|Doctor whereHospital($value)
 * @method static Builder|Doctor whereFees($value)
 * @method static Builder|Doctor whereCreatedAt($value)
 * @method static Builder|Doctor whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Prescription[] $prescriptions
 */
class Doctor extends BaseModel
{
    /**
     * Is display id auto generated
     * */
    protected static $displayIdAutoGenerate = true;

    /**
     * Pattern of display id
     * */
    protected static $displayIdPattern = ['prefix' => 'DR', 'digits' => 4, 'firstNo' => 1];

    /**
     * mass assignment
     * define which attributes are mass assignable
     **/
    protected $fillable = ['displayId', 'title', 'firstName', 'middleName', 'lastName', 'gender', 'address', 'maritalStatus', 'city', 'birthday', 'nic', 'telephoneNo',
        'mobileNo', 'email', 'degree', 'doctorType', 'hospital', 'fees'];

    /**
     * Validation Rules
     * @var array
     */
    protected $validationRules = [
        'title' => 'required|in:Mr,Mrs,Ms,Miss,Rev,Dr',
        'firstName' => 'required|string|max:30',
        'middleName' => 'required|string||max:30',
        'lastName' => 'required|string|max:30',
        'gender' => 'required|in:Male,Female,Other',
        'maritalStatus' => 'required|in:Single,Married,Divorced,Widowed',
        'address' => 'required|string|max:50',
        'city' => 'required|string|max:20',
        'birthday' => 'required|date_format:"Y-m-d"|before:today',
        'nic' => 'required|string|max:12|min:10|unique:doctors,nic',
        'telephoneNo' => 'string|size:10',
        'mobileNo' => 'string|size:10',
        'email' => 'required|email|max:40|unique:doctors,email',
        'degree' => 'string|max:50',
        'doctorType' => 'required|in:OPD,Channelling',
        'doctorSpecialtyId' => 'integer|exists:doctor_specialties,id',
        'hospital' => 'string|max:50',
        'fees' => 'required|numeric',
    ];

    /**
     * Automatically Loading Relationships.
     * @var array
     */
    protected $with = ['DoctorSpecialty'];

    /**
     * Set rules for update validation.
     */
    public function setUpdateRules()
    {
        $this->validationRules['nic'] = $this->validationRules['nic'] . ',' . $this->id;
        $this->validationRules['email'] = $this->validationRules['email'] . ',' . $this->id;
    }

    /**
     * Get the User account.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User');
    }

    /**
     * Get the doctor specialty Record Associated with doctor.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctorSpecialty()
    {
        return $this->belongsTo('App\DoctorSpecialty');
    }

    /**
     * Get Appointments Associated with the Doctor.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appointments()
    {
        return $this->hasMany('App\Appointment');
    }

    /**
     * Get Prescriptions Associated with the Doctor.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prescriptions()
    {
        return $this->hasMany('App\Prescription');
    }
}
