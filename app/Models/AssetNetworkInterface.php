<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetNetworkInterface extends Model
{
    use HasFactory;

    protected $primaryKey = 'asset_id';
    public $incrementing = false;

    protected $fillable = [
        'asset_id',
        'ip_address',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
