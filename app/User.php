<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $userRole
 * @property boolean $disabled
 * @property integer $employee_id
 * @property integer $doctor_id
 * @property string $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Employee $employee
 * @property-read Doctor $doctor
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereUsername($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereUserRole($value)
 * @method static Builder|User whereDisabled($value)
 * @method static Builder|User whereEmployeeId($value)
 * @method static Builder|User whereDoctorId($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /**
     * Validation Rules
     * @var array
     */
    public static $createRules = [
        'username' => 'required|string|unique:users,username|max:20|min:5',
        'userRole' => 'required|in:Administrator,Manager,Accountant,Nurse,Receptionist,Pharmacist,Doctor',
    ];

    /**
     * mass assignment
     * define which attributes are mass assignable
     */
    protected $fillable = ['username', 'password', 'remember_token', 'userRole'];

    /**
     *Exclude attributes from Model's JSON form
     */
    protected $hidden = ['password', 'created_at', 'updated_at', 'remember_token'];

    /**
     * Get the Employee Record Associated with User.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo('App\Employee');
    }

    /**
     * Get the Doctor.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function doctor()
    {
        return $this->belongsTo('App\Doctor');
    }
}
