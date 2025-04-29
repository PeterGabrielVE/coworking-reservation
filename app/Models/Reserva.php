<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sala;
use App\Models\User;

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
        'fecha', // Fecha de la reserva
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reserva) {
            if (is_null($reserva->hora_inicio) || $reserva->hora_inicio === '') {
                $horaActual = \Carbon\Carbon::now()->format('H:i');
                $reserva->hora_inicio = $horaActual;
                //throw new \Exception('La hora de inicio no puede ser nula o vacía.');
            }

            // Si hora_inicio está definida, calcular hora_fin
            $reserva->hora_fin = \Carbon\Carbon::parse($reserva->hora_inicio)->addHour()->format('H:i');

            // Verificar si existe alguna reserva que se solape
            $overlappingReserva = \App\Models\Reserva::where('sala_id', $reserva->sala_id)
                ->whereDate('fecha', $reserva->fecha)
                ->where(function ($query) use ($reserva) {
                    $query->whereBetween('hora_inicio', [$reserva->hora_inicio, $reserva->hora_fin])
                        ->orWhereBetween('hora_fin', [$reserva->hora_inicio, $reserva->hora_fin])
                        ->orWhere(function ($query) use ($reserva) {
                            $query->where('hora_inicio', '<=', $reserva->hora_inicio)
                                ->where('hora_fin', '>=', $reserva->hora_fin);
                        });
                })
                ->exists();

            if ($overlappingReserva) {
                \Alert::error('Ya existe una reserva en esta sala para este horario.')->flash();
                return false;
                //throw new \Exception('Ya existe una reserva en esta sala para este horario.');
            }
        });
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
