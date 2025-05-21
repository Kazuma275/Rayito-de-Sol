<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    use HasFactory;

    protected $table = 'services_translations';

    protected $fillable = [
        'service_id',
        'language_code',
        'service_name',
        'service_description',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
