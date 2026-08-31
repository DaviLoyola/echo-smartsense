<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome', 'email', 'senha_hash', 'telefone', 'role', 'status', 'ultimo_login', 'notificacoes', 'permanent'
    ];

    protected $hidden = [
        'senha_hash',
    ];

    protected $casts = [
        'permanent' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->senha_hash;
    }
    
    public function dispositivos()
{
    return $this->hasMany(Dispositivo::class, 'usuario_id');
}
}