<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetReplacement extends Model
{
    protected $fillable = [
        'original_asset_id',
        'replacement_asset_id',
        'reason',
        'date_replaced',
    ];

    public function originalAsset()
    {
        return $this->belongsTo(Asset::class , 'original_asset_id');
    }

    public function replacementAsset()
    {
        return $this->belongsTo(Asset::class , 'replacement_asset_id');
    }
}
