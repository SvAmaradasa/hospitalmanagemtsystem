<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;

/**
 * App\Fee
 *
 * @property integer $id
 * @property string $feeType
 * @property string $description
 * @property float $fee
 * @property boolean $isVariable
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Invoice $invoice
 * @method static Builder|Fee whereId($value)
 * @method static Builder|Fee whereFeeType($value)
 * @method static Builder|Fee whereDescription($value)
 * @method static Builder|Fee whereFee($value)
 * @method static Builder|Fee whereIsVariable($value)
 * @method static Builder|Fee whereCreatedAt($value)
 * @method static Builder|Fee whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Fee extends BaseModel
{
    /**
     * mass assignment
     * define which attributes are mass assignable
     **/
    protected $fillable = ['feeType', 'description', 'fee', 'isVariable'];

    /**
     * create validation rules
     **/
    protected $validationRules = [
        'feeType' => 'required|in:OPD,Channelling,Scan,X-Ray,Lab',
        'description' => 'required|string|max:100',
        'fee' => 'numeric|max:99999.99|min:0.10',
    ];

    /**
     * Get the Invoice Associated with the Fee.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo('App\Invoice');
    }
}
