<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoDecisao extends Model
{
    // tabela de log de auditoria de eventos e cortes
    protected $table = 'eventos_decisoes';

    // desabilita timestamps padrao do laravel
    public $timestamps = false;

    // campos liberados para atribuicao em massa
    protected $fillable = [
        'dispositivo_id',
        'tipo_evento',
        'grandeza_acionada',
        'valor_medido',
        'valor_limite',
        'acao_tomada',
        'motivo',
        'momento_evento'
    ];

    // dispositivo que gerou o evento
    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id');
    }
}
