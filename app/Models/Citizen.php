<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'prefix',
        'gender',
        'date_of_birth',
        'marital_status',
        'account_number',
        'telephone_number',
        'country_of_citizenship',
        'customer_type',
        'status',
        'Ghana_card_number',
        'created_by',
        'user_id',
        'id_type',
        'id_number',
        'business_name',
        'email',
        'date_of_commencement',
        'security_question',
        'security_answer',
        'tin_number',
    ];

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'customer_type');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'customer_name');
    }

    public function businesses()
    {
        return $this->hasMany(Business::class, 'citizen_account_number');
    }
}
