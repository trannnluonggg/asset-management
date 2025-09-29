<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetHistory extends Model
{
    protected $table = 'asset_history';
    
    protected $fillable = [
        'asset_id',
        'action_type',
        'action_date',
        'performed_by',
        'old_value',
        'new_value',
        'notes'
    ];

    protected $casts = [
        'action_date' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'performed_by');
    }
}
