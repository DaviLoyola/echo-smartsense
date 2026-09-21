<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LimitesOperacionais extends Model
{
    // tabela de limites operacionais das bombas
    protected $table = 'limites_operacionais';

    // desabilita timestamps padrao do laravel
    public $timestamps = false;

    // campos liberados para atribuicao em massa
    protected $fillable = [
        'dispositivo_id',
        'corrente_alerta',
        'corrente_corte_alta',
        'corrente_corte_seco',
        'tensao_alerta_baixa',
        'tensao_alerta_alta',
        'tensao_corte_baixa',
        'tensao_corte_alta',
        'fluxo_alerta_baixo',
        'fluxo_corte_zero',
        'vibracao_alerta',
        'vibracao_corte',
        'temperatura_alerta',
        'temperatura_corte',
        'pressao_linear_alerta',
        'pressao_linear_corte',
        'pressao_diferencial_alerta',
        'pressao_diferencial_corte'
    ];

    // dispositivo associado a estes limites
    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id');
    }
}
