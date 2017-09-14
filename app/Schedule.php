<?php

namespace App;

class Schedule extends BaseModel
{
    /**
     * mass assignment
     * define which attributes are mass assignable
     **/
    protected $fillable = ['from', 'to', 'shift'];

    /**
     * create validation rules
     **/
    protected $validationRules = [
        'from' => 'required|date',
        'to' => 'required|date',
        'shift' => 'required|string|max:10'
    ];

    /**
     * Get Associated Employee.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo('App\Employee');
    }
}
