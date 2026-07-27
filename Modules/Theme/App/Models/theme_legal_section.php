<?php

namespace Modules\Theme\App\Models;

use Illuminate\Database\Eloquent\Model;

class theme_legal_section extends Model
{
    protected $table = 'theme_legal_sections';

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
