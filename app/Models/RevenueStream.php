<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueStream extends Model
{
    use HasFactory;

    protected $table = 'revenue_streams';

    protected $guarded = [
        'id',
    ];

    public function revenueStreamType()
    {
        return $this->belongsTo(RevenueStreamType::class, 'revenue_stream_type_id');
    }

    public function zoneType()
    {
        return $this->belongsTo(Zone::class, 'zone_type_id');
    }

    public function propertyClasses()
    {
        return $this->belongsTo(PropertyClasses::class, 'property_classes_id');
    }

    public function mainItem()
    {
        return $this->belongsTo(MainItem::class, 'main_item_id');
    }

    public function subItem()
    {
        return $this->belongsTo(SubItem::class, 'sub_item_id');
    }

    public function itemDescription()
    {
        return $this->belongsTo(ItemDescription::class, 'description_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
