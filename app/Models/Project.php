<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditLog()
    {
        return $this->hasMany(AuditLog::class,'project_id');
    }

    public function approval()
    {
        return $this->hasMany(Approval::class,'project_id');
    }
}
