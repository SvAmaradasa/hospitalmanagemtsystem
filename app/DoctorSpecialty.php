<?php

namespace App;

use Illuminate\Database\Query\Builder;

/**
 * App\DoctorSpecialty
 *
 * @property integer $id
 * @property string $specialty
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Doctor[] $doctors
 * @method static Builder|DoctorSpecialty whereId($value)
 * @method static Builder|DoctorSpecialty whereSpecialty($value)
 * @method static Builder|DoctorSpecialty whereCreatedAt($value)
 * @method static Builder|DoctorSpecialty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DoctorSpecialty extends BaseModel
{
    /**
     * mass assignment
     * define which attributes are mass assignable
     **/
    protected $fillable = ['specialty'];

    /**
     * Get the doctor Records Associated with doctor specialty
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function doctors()
    {
        return $this->hasMany('App\Doctor');
    }
}
