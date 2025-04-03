<?php

namespace Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $primaryKey = 'department_id';
    protected $fillable = [
        'name', 'type'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'department_id');
    }

    public function phones()
    {
        return $this->hasManyThrough(Phone::class, Room::class);
    }
}