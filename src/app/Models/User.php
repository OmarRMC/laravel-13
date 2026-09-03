<?php

namespace App\Models;

#use App\Models\User;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function tieneRol(string|array $roles): bool
    {
        return $this->loadMissing('roles')
            ->roles
            ->whereIn('nombre', (array) $roles)
            ->isNotEmpty();
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
        );
    }

    //Relaciones

    /** 1:1 el perfil ampliado del usuario. */
    public function perfil(): HasOne
    {
        return $this->hasOne(Perfil::class);
    }

    /** 1:N los eventos que este usuario organiza. */
    public function eventosOrganizados(): HasMany
    {
        return $this->hasMany(Evento::class);
    }

    /** N:M los eventos a los que se inscribio (pivote `evento_user`, con datos propios). */
    public function inscripciones(): BelongsToMany
    {
        return $this->belongsToMany(Evento::class)
            ->withPivot(['codigo', 'estado', 'asistio'])
            ->withTimestamps();
    }

    /** N:M los roles del usuario (pivote `role_user`, sin datos propios). */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }
}
