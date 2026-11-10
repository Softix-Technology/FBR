<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Buyer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'ntn_cnic',
        'business_name',
        'address',
        'mobile_number',
        'registration_type',
        'province',
        'cid'
    ];

    /**
     * Get the user that owns the buyer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to search buyers by NTN/CNIC.
     */
    public function scopeSearchByNtnCnic($query, $search)
    {
        return $query->where('ntn_cnic', 'like', $search . '%');
    }

    /**
     * Scope a query to search buyers by business name.
     */
    public function scopeSearchByBusinessName($query, $search)
    {
        return $query->where('business_name', 'like', '%' . $search . '%');
    }

    /**
     * Scope a query to search buyers by any field.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('ntn_cnic', 'like', $search . '%')
              ->orWhere('business_name', 'like', '%' . $search . '%');
        });
    }

    /**
     * Get or create a buyer for the given user and data.
     */
    public static function createOrUpdate(int $userId, array $buyerData): self
    {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'ntn_cnic' => $buyerData['ntn_cnic'],
            ],
            [
                'business_name' => $buyerData['business_name'],
                'address' => $buyerData['address'],
                'registration_type' => $buyerData['registration_type'],
                'province' => $buyerData['province'],
            ]
        );
    }
}
