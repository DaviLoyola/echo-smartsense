<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeituraSensor extends Model
{
    protected $table = 'leituras_sensores';
    public $timestamps = false;

    protected $fillable = [
        'dispositivo_id', 'corrente_a', 'tensao_v', 'potencia_kw', 'vibracao', 
        'fluxo_agua', 'volume_total', 'temperatura_motor', 'momento_leitura'
    ];

    // Uma leitura pertence a um único dispositivo
    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id');
    }
}