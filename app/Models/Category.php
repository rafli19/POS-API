<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Automatically append image_url to JSON responses
    protected $appends = ['image_url'];

    // ==================== ACCESSOR ====================
    /**
     * Get the full URL for the category image
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        return url('storage/' . $this->image);
    }

    // ==================== RELATIONSHIPS ====================
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // ==================== SCOPES ====================
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}