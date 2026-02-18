<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportTask extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'total_rows',
        'processed_rows',
        'status',
        'errors',
    ];

    protected $casts = [
        'errors' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
