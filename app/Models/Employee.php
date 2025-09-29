<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = [
        'employee_code',
        'full_name',
        'email',
        'phone',
        'department_id',
        'position',
        'hire_date',
        'status'
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class)->where('status', 'active');
    }

    public function assignedAssets(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'assigned_by');
    }

    public function assetHistories(): HasMany
    {
        return $this->hasMany(AssetHistory::class, 'performed_by');
    }

    public function reportedIncidents(): HasMany
    {
        return $this->hasMany(IncidentReport::class, 'reported_by');
    }

    public function resolvedIncidents(): HasMany
    {
        return $this->hasMany(IncidentReport::class, 'resolved_by');
    }
}
