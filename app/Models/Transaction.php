<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_name',
        'member_number',
        'subtotal',
        'discount',
        'tax',
        'other_fee',
        'total_amount',
        'paid_amount',
        'change_amount',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal'      => 'float',
        'discount'      => 'float',
        'tax'           => 'float',
        'other_fee'     => 'float',
        'total_amount'  => 'float',
        'paid_amount'   => 'float',
        'change_amount' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
