<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    // tabela de catalogo das motobombas
    protected $table = 'dispositivos';

    // campos liberados para atribuicao em massa
    protected $fillable = [
        'nome',
        'tipo',
        'modelo',
        'status',
        'ultima_comunicacao',
        'api_key',
        'localizacao',
        'data_instalacao',
        'usuario_id'
    ];

    // leituras enviadas por este dispositivo
    public function leituras()
    {
        return $this->hasMany(LeituraSensor::class, 'dispositivo_id');
    }

    // atalho para buscar a ultima leitura registrada
    public function ultimaLeitura()
    {
        return $this->hasOne(LeituraSensor::class, 'dispositivo_id')->latest('momento_leitura');
    }

    // usuario proprietario da motobomba
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // limites operacionais configurados para esta bomba
    public function limites()
    {
        return $this->hasOne(LimitesOperacionais::class, 'dispositivo_id');
    }

    // historico de eventos e decisoes de corte
    public function eventos()
    {
        return $this->hasMany(EventoDecisao::class, 'dispositivo_id');
    }
}