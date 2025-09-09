<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCompany extends Model
{
    //
    protected $fillable = [
        'user_id',
        'company_location_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function companyLocation()
    {
        return $this->belongsTo(CompanyLocation::class);
    }
}
