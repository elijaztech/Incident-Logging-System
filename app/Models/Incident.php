<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{   
    protected $table = 'incidents';
    protected $primaryKey = 'ticketid'; // primary key
    public $incrementing = true;
    protected $keyType = 'int'; // keys are strings

    protected $fillable = [
        'location',
        'department',
        'description',
        'status',
        'ttr',
        'rating',
        'ratingdetails',
        'compensationtype',
        'compensationdetails',
        'compensationvalue',
        'image_path',
        'user_id'
    ];

    public function user()
        {
            return $this->belongsTo(User::class, 'user_id');
        }

    
}
