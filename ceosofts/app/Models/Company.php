<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'address',
        'phone',
        'mobile',
        'fax',
        'email',
        'website',
        'logo',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
        'tiktok',
        'facebook',
        'line',
        'tax_id',
        'contact_person'
    ];
}
