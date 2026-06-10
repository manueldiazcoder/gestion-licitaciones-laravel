<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'provider',
        'provider_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ─────────────────────────────────────────
    //  Métodos de Roles
    // ─────────────────────────────────────────

    /**
     * ¿Este usuario es administrador?
     */
    public function esAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * ¿Este usuario es visor (lectura)?
     */
    public function esVisor(): bool
    {
        return $this->role === 'visor';
    }

    /**
     * Retorna la etiqueta HTML del rol (para badges).
     */
    public function getRolBadge(): string
    {
        $label = $this->getRolFormateado();
        $class = $this->esAdmin() ? 'bg-danger' : 'bg-secondary';

        return "<span class=\"badge {$class}\">{$label}</span>";
    }

    /**
     * Retorna el nombre del rol en castellano.
     */
    public function getRolFormateado(): string
    {
        return match ($this->role) {
            'admin' => 'Administrador',
            'visor' => 'Visor',
            default => ucfirst($this->role),
        };
    }

    // ─────────────────────────────────────────
    //  Password Reset
    // ─────────────────────────────────────────

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\MailResetPasswordNotification($token));
    }
}
