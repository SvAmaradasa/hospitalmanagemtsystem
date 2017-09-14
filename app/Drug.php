<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

/**
 * App\Drug
 *
 * @property integer $id
 * @property string $displayId
 * @property string $name
 * @property string $weight
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property integer $qty
 * @property boolean $availableForAll
 * @property float $price
 * @property-read Collection|Prescription[] $prescriptions
 * @property-read Collection|Invoice[] $invoices
 * @method static Builder|Drug whereId($value)
 * @method static Builder|Drug whereDisplayId($value)
 * @method static Builder|Drug whereName($value)
 * @method static Builder|Drug whereWeight($value)
 * @method static Builder|Drug whereCreatedAt($value)
 * @method static Builder|Drug whereUpdatedAt($value)
 * @method static Builder|Drug whereQty($value)
 * @method static Builder|Drug whereAvailableForAll($value)
 * @method static Builder|Drug wherePrice($value)
 * @mixin \Eloquent
 */
class Drug extends BaseModel
{
    /**
     * Is display id auto generated
     * */
    protected static $displayIdAutoGenerate = true;

    /**
     * Pattern of display id
     * */
    protected static $displayIdPattern = ['prefix' => 'DRG', 'digits' => 6, 'firstNo' => 1];
    /**
     * mass assignment
     * define which attributes are mass assignable
     **/
    protected $fillable = ['displayId', 'name', 'weight', 'availableForAll', 'price'];

    /**
     * create validation rules
     **/
    protected $validationRules = [
        'name' => 'required|string|max:100',
        'weight' => 'required|string|max:6',
        'availableForAll' => 'required|boolean'
    ];

    public function prescriptions()
    {
        return $this->belongsToMany('App\Prescription', 'prescription_details', 'drug_id', 'prescription_id');
    }

    /**
     * Get Associated Invoices
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }
}
