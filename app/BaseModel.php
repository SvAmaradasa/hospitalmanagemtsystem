<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * App\BaseModel
 *
 * @mixin \Eloquent
 */
class BaseModel extends Model
{

    /**
     * Is display id auto generated
     * */
    protected static $displayIdAutoGenerate = false;

    /**
     * Pattern of display id
     * */
    protected static $displayIdPattern = [
        'prefix' => '',
        'digits' => 0,
        'firstNo' => 1
    ];

    /**
     *Exclude attributes from Model's JSON form
     **/
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Validation Rules
     * @var array
     */
    protected $validationRules = [];

    /**
     * Validation errors
     * */
    private $validationErrors;

    /**
     * Set new display id when record is creating
     * */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model::$displayIdAutoGenerate == true) {
                static::generateDisplayId($model);
            }
        });
    }

    /**
     * Generate display id
     * @param $model
     */
    private static function generateDisplayId($model)
    {
        $lastModel = $model::orderBy('id', 'DESC')->get()->first();
        $newModelDisplayId = $model::$displayIdPattern['prefix'] . '-' . sprintf("%0" . $model::$displayIdPattern['digits'] . "d", $model::$displayIdPattern['firstNo']);

        if ($lastModel != null) {

            $lastModelDisplayId = substr($lastModel->displayId, strlen($model::$displayIdPattern['prefix']) + 1, $model::$displayIdPattern['digits']);
            $newModelDisplayId = intval($lastModelDisplayId) + 1;
            $newModelDisplayId = $model::$displayIdPattern['prefix'] . '-' . sprintf("%0" . $model::$displayIdPattern['digits'] . "d", $newModelDisplayId);
        }

        $model->displayId = $newModelDisplayId;
    }

    /**
     * get validation errors
     * */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    /**
     * Validate model
     * @return bool
     */
    public function validate()
    {
        // make a new validator object
        $validator = Validator::make($this->toArray(), $this->validationRules);;

        // check for failure
        if ($validator->fails()) {
            // set errors and return false
            $this->validationErrors = $validator->errors()->all();
            return false;
        }

        // validation pass
        return true;
    }

    /**
     * Convert properties to camel case
     * */
    public function toArray()
    {
        $return = null;
        $array = parent::toArray();

        foreach ($array as $key => $value) {
            $return[camel_case($key)] = $value;
        }

        return $return;
    }
}
