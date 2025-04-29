<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use CrudTrait;
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'reservas';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'user_id',      // Relación con el usuario
        'sala_id',      // Relación con la sala
        'estado_id',    // Relación con el estado
        'fecha_reserva', // Fecha de la reserva
        'hora_inicio',   // Hora de inicio
        'hora_fin',      // Hora de fin
    ];
    // protected $hidden = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function exportButton()
    {
        return '<a class="btn btn-sm btn-primary" href="'.route('reserva.export').'">Exportar Excel</a>';
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class); // Relación con el modelo User
    }

    // Relación con la sala
    public function sala()
    {
        return $this->belongsTo(Sala::class); // Relación con el modelo Sala
    }

    // Relación con el estado
    public function estado()
    {
        return $this->belongsTo(Estado::class); // Relación con el modelo Estado
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
