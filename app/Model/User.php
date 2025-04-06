<?php

namespace Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Src\Auth\IdentityInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Model implements IdentityInterface
{
    use HasFactory;

    public $timestamps = false;

    protected static function booted()
    {

    }

    //Выборка пользователя по первичному ключу
    public function findIdentity(int $id)
    {
        return self::where('id', $id)->first();
    }

    //Возврат первичного ключа
    public function getId(): int
    {
        return $this->id;
    }

    //Возврат аутентифицированного пользователя
    public function attemptIdentity(array $credentials)
    {
        return self::where(['login' => $credentials['login'],
            'password' => md5($credentials['password'])])->first();
    }

    protected $fillable = [
        'name', 'surname', 'patronymic', 'birth_date', 'login', 'password',
         'role_id'
    ];


    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function phones()
    {
        return $this->hasMany(Phone::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    public function isSysadmin(): bool
    {
        return $this->role_id === 2;
    }

    public function isAdminOrSysadmin(): bool
    {
        return in_array($this->role_id, [1, 2]);
    }

    public function isRegularUser(): bool
    {
        return $this->role_id === 3;
    }
}
