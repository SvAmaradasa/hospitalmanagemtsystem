<?php

namespace App;

class Xray extends BaseModel
{
    /**
     * Is display id auto generated
     * */
    protected static $displayIdAutoGenerate = true;

    /**
     * Pattern of display id
     * */
    protected static $displayIdPattern = ['prefix' => 'XRY', 'digits' => 6, 'firstNo' => 1];
    /**
     * mass assignment
     * define which attributes are mass assignable
     **/
    protected $fillable = ['displayId', 'name', 'hospitalFee'];

    /**
     * create validation rules
     **/
    protected $validationRules = [
        'name' => 'required|string|max:100',
        'hospitalFee' => 'required|numeric'
    ];

    /**
     * Get associated Appointment
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function appointments()
    {
        return $this->hasMany('App\Appointment');
    }
}
