<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

/**
 * App\Company
 *
 * @property integer $id
 * @property string $displayId
 * @property string $name
 * @property string $address
 * @property string $telephoneNo
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection|Appointment[] $appointments
 * @method static Builder|Company whereId($value)
 * @method static Builder|Company whereDisplayId($value)
 * @method static Builder|Company whereName($value)
 * @method static Builder|Company whereAddress($value)
 * @method static Builder|Company whereTelephoneNo($value)
 * @method static Builder|Company whereCreatedAt($value)
 * @method static Builder|Company whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Company extends BaseModel
{
    /**
     * Is display id auto generated
     * */
    protected static $displayIdAutoGenerate = true;

    /**
     * Pattern of display id
     * */
    protected static $displayIdPattern = ['prefix' => 'COM', 'digits' => 3, 'firstNo' => 1];

    /**
     * mass assignment
     * define which attributes are mass assignable
     **/
    protected $fillable = ['displayId', 'name', 'address', 'telephoneNo'];

    /**
     * create validation rules
     **/
    protected $validationRules = [
        'name' => 'required|string|max:30',
        'address' => 'required|string|max:50',
        'telephoneNo' => 'required|numeric|digits:10'
    ];

    /**
     * Get the Appointment Records Associated with the Company.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function appointments()
    {
        return $this->hasMany('App\Appointment');
    }
}
