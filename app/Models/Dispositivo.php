<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    protected $table = 'dispositivos';

    protected $fillable = [
        'nome', 'tipo', 'modelo', 'status', 'ultima_comunicacao', 'localizacao', 'data_instalacao', 'usuario_id'
    ];

    public function leituras()
    {
        return $this->hasMany(LeituraSensor::class, 'dispositivo_id');
    }

    // Cria um atalho para pegar SEMPRE a leitura mais recente do Arduino
    public function ultimaLeitura()
    {
        return $this->hasOne(LeituraSensor::class, 'dispositivo_id')->latest('momento_leitura');
    }

    public function usuario()
{
    return $this->belongsTo(Usuario::class, 'usuario_id');
}
}