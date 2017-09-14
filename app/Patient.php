<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

/**
 * App\Patient
 *
 * @property integer $id
 * @property string $displayId
 * @property string $title
 * @property string $firstName
 * @property string $middleName
 * @property string $lastName
 * @property string $gender
 * @property string $birthday
 * @property string $maritalStatus
 * @property string $address
 * @property string $city
 * @property string $nic
 * @property string $telephoneNo
 * @property string $mobileNo
 * @property string $email
 * @property string $emergencyContact
 * @property string $emergencyContactPhone
 * @property string $emergencyContactRelation
 * @property string $employer
 * @property string $occupation
 * @property string $employerPhone
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection|Appointment[] $appointments
 * @method static Builder|Patient whereId($value)
 * @method static Builder|Patient whereDisplayId($value)
 * @method static Builder|Patient whereTitle($value)
 * @method static Builder|Patient whereFirstName($value)
 * @method static Builder|Patient whereMiddleName($value)
 * @method static Builder|Patient whereLastName($value)
 * @method static Builder|Patient whereGender($value)
 * @method static Builder|Patient whereBirthday($value)
 * @method static Builder|Patient whereMaritalStatus($value)
 * @method static Builder|Patient whereAddress($value)
 * @method static Builder|Patient whereCity($value)
 * @method static Builder|Patient whereNic($value)
 * @method static Builder|Patient whereTelephoneNo($value)
 * @method static Builder|Patient whereMobileNo($value)
 * @method static Builder|Patient whereEmail($value)
 * @method static Builder|Patient whereEmergencyContact($value)
 * @method static Builder|Patient whereEmergencyContactPhone($value)
 * @method static Builder|Patient whereEmergencyContactRelation($value)
 * @method static Builder|Patient whereEmployer($value)
 * @method static Builder|Patient whereOccupation($value)
 * @method static Builder|Patient whereEmployerPhone($value)
 * @method static Builder|Patient whereCreatedAt($value)
 * @method static Builder|Patient whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Prescription[] $prescriptions
 */
class Patient extends BaseModel
{
    /**
     * Is display id auto generated
     * */
    protected static $displayIdAutoGenerate = true;

    /**
     * Pattern of display id
     * */
    protected static $displayIdPattern = ['prefix' => 'PT', 'digits' => 8, 'firstNo' => 1];

    /**
     * mass assignment
     * define which attributes are mass assignable
     **/
    protected $fillable = ['displayId', 'title', 'firstName', 'middleName', 'lastName', 'gender', 'birthday', 'maritalStatus', 'address', 'city',
        'nic', 'telephoneNo', 'mobileNo', 'email', 'emergencyContact', 'emergencyContactPhone', 'emergencyContactRelation', 'employer', 'occupation', 'employerPhone'];

    /**
     * create validation rules
     **/
    protected $validationRules = [
        'title' => 'required|in:Mr,Mrs,Ms,Miss,Rev,Dr',
        'firstName' => 'required|string|max:30',
        'middleName' => 'string|max:30',
        'lastName' => 'required|string|max:30',
        'gender' => 'required|in:Male,Female,Other',
        'birthday' => 'required|date_format:"Y-m-d"|before:today',
        'maritalStatus' => 'in:Single,Married,Divorced,Widowed',
        'address' => 'string|max:50',
        'city' => 'string|max:20',
        'nic' => 'string|max:12|min:10|unique:patients,nic',
        'telephoneNo' => 'string|size:10',
        'mobileNo' => 'string|size:10',
        'email' => 'email|max:40',
        'emergencyContact' => 'string|max:50',
        'emergencyContactPhone' => 'string|size:10',
        'emergencyContactRelation' => 'string|max:30',
        'employer' => 'string|max:50',
        'occupation' => 'string|max:30',
        'employerPhone' => 'string|size:10',
    ];

    /**
     * Set rules for update validation.
     */
    public function setUpdateRules()
    {
        $this->validationRules['nic'] = $this->validationRules['nic'] . ',' . $this->id;
    }

    /**
     * Get Appointments associated with the Patient.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function appointments()
    {
        return $this->belongsToMany('App\Appointment', 'appointment_details', 'patient_id', 'appointment_id');
    }

    /**
     * Get Prescriptions associated.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prescriptions()
    {
        return $this->belongsToMany('App\Prescription');
    }
}
