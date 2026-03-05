<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketplaceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'short_description',
        'category',         // ecommerce, blog, saas, landing, portfolio, etc.
        'type',             // template | plugin | module
        'price',            // 0 = free
        'thumbnail',
        'preview_url',
        'download_count',
        'rating',
        'rating_count',
        'tags',
        'framework_version',
        'is_approved',
        'is_featured',
        'files_path',       // stored zip path
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'rating'      => 'decimal:1',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'tags'        => 'array',
    ];

    public function author()  { return $this->belongsTo(User::class, 'user_id'); }
    public function reviews() { return $this->hasMany(MarketplaceReview::class); }

    public function isFree(): bool   { return $this->price == 0; }
    public function isPaid(): bool   { return $this->price > 0; }

    public function scopeApproved($q) { return $q->where('is_approved', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }
    public function scopeFree($q)     { return $q->where('price', 0); }
    public function scopePaid($q)     { return $q->where('price', '>', 0); }
}
