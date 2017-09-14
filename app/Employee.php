<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

/**
 * App\Employee
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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read Collection|Appointment[] $appointments
 * @property-read Collection|Payment[] $payments
 * @property-read Doctor $doctor
 * @method static Builder|Employee whereId($value)
 * @method static Builder|Employee whereDisplayId($value)
 * @method static Builder|Employee whereTitle($value)
 * @method static Builder|Employee whereFirstName($value)
 * @method static Builder|Employee whereMiddleName($value)
 * @method static Builder|Employee whereLastName($value)
 * @method static Builder|Employee whereGender($value)
 * @method static Builder|Employee whereMaritalStatus($value)
 * @method static Builder|Employee whereAddress($value)
 * @method static Builder|Employee whereCity($value)
 * @method static Builder|Employee whereBirthday($value)
 * @method static Builder|Employee whereNic($value)
 * @method static Builder|Employee whereTelephoneNo($value)
 * @method static Builder|Employee whereMobileNo($value)
 * @method static Builder|Employee whereEmail($value)
 * @method static Builder|Employee whereCreatedAt($value)
 * @method static Builder|Employee whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Employee extends BaseModel
{
    /**
     * Is display id auto generated
     * */
    protected static $displayIdAutoGenerate = true;

    /**
     * Pattern of display id
     * */
    protected static $displayIdPattern = ['prefix' => 'EMP', 'digits' => 3, 'firstNo' => 1];

    /**
     * mass assignment
     * define which attributes are mass assignable
     **/
    protected $fillable = ['displayId', 'title', 'firstName', 'middleName', 'lastName', 'gender', 'address', 'maritalStatus', 'city', 'birthday', 'nic', 'telephoneNo',
        'mobileNo', 'email'];

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
        'nic' => 'required|string|max:12|min:10|unique:employees,nic',
        'telephoneNo' => 'string|size:10',
        'mobileNo' => 'string|size:10',
        'email' => 'required|email|max:40|unique:employees,email'
    ];

    /**
     * Set rules for update validation.
     */
    public function setUpdateRules()
    {
        $this->validationRules['nic'] = $this->validationRules['nic'] . ',' . $this->id;
        $this->validationRules['email'] = $this->validationRules['email'] . ',' . $this->id;
    }

    /**
     * Get the User Record Associated with Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\User');
    }

    /**
     * Get Appointments associated with the Employee.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appointments()
    {
        return $this->hasMany('App\Appointment');
    }

    /**
     * Get associated Payments.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }

    /**
     * Get the Doctor Record Associated with Employee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function doctor()
    {
        return $this->hasOne('App\Doctor');
    }

    /**
     * Get the associated Schedules.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules()
    {
        return $this->hasMany('App\Schedule');
    }
}
