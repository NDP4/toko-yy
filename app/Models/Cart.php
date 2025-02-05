<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    public static function getCart()
    {
        if (Auth::check()) {
            return self::firstOrCreate([
                'user_id' => Auth::id()
            ]);
        }

        $sessionId = session()->get('cart_id');
        if (!$sessionId) {
            $sessionId = Str::uuid();
            session()->put('cart_id', $sessionId);
        }

        return self::firstOrCreate([
            'session_id' => $sessionId
        ]);
    }
}
