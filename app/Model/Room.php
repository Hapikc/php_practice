<?php

namespace Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $primaryKey = 'room_id'; // Указываем кастомный первичный ключ

    protected $fillable = [
        'name', 'type', 'department_id'
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class, 'room_id');
    }
}