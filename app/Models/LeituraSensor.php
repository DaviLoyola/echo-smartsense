<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeituraSensor extends Model
{
    // tabela de serie temporal de telemetria
    protected $table = 'leituras_sensores';

    // desabilita timestamps padrao do laravel
    public $timestamps = false;

    // campos liberados para atribuicao em massa
    protected $fillable = [
        'dispositivo_id',
        'corrente_fase_a',
        'corrente_fase_b',
        'corrente_fase_c',
        'fluxo_agua',
        'vibracao',
        'temperatura_motor',
        'tensao_fase_a',
        'tensao_fase_b',
        'tensao_fase_c',
        'status_dps',
        'pressao_linear',
        'pressao_diferencial',
        'momento_leitura'
    ];

    // relacionamento com o dispositivo dono da leitura
    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id');
    }
}