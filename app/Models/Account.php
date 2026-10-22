<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property float $balance
 * @property float $minimum_balance
 * @property bool $low_balance_notified
 */
class Account extends Model
{
    use Notifiable;

    protected $casts = [
        'balance' => 'float',
        'minimum_balance' => 'float',
        'low_balance_notified' => 'boolean',
    ];

    protected $fillable = ['user_id', 'account_number', 'balance', 'minimum_balance', 'low_balance_notified'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
