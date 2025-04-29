<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReservaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReservasExport;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
/**
 * Class ReservaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReservaCrudController extends CrudController
{
    // Alias the store method from CreateOperation
    use CreateOperation {
        CreateOperation::store as backpackStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
{
    $this->crud->setModel(\App\Models\Reserva::class);
    $this->crud->setRoute(config('backpack.base.route_prefix') . '/reserva');
    $this->crud->setEntityNameStrings('reserva', 'reservas');

    $this->crud->denyAccess('delete');
    if (!backpack_user()->hasRole('admin')) {
        // Denegar acciones a los clientes y otros usuarios que no sean admin
        $this->crud->denyAccess(['update', 'delete']);
    } else {
        // Asegúrate de permitir la creación solo para usuarios clientes si no son administradores
        $this->crud->allowAccess('create');
    }
    // Configura columnas (index y show)
    $this->crud->addColumn([
        'name'      => 'user_id',
        'label'     => 'Cliente',
        'type'      => 'relationship',
        'entity'    => 'user',
        'attribute' => 'name',
    ]);
    $this->crud->addColumn([
        'name'      => 'sala_id',
        'label'     => 'Sala',
        'type'      => 'relationship',
        'entity'    => 'sala',
        'attribute' => 'nombre',
    ]);
    $this->crud->addColumn([
        'name'  => 'fecha',
        'label' => 'Fecha',
        'type'  => 'date',
    ]);
    $this->crud->addColumn([
        'name'  => 'hora_inicio',
        'label' => 'Hora inicio',
        'type'  => 'time',
    ]);
    $this->crud->addColumn([
        'name'      => 'estado_id',
        'label'     => 'Estado',
        'type'      => 'select',
        'entity'    => 'estado',
        'model'     => "App\Models\Estado",
        'attribute' => 'nombre',
    ]);



    // Si quieres una acción personalizada para cambiar el estado:
    $this->crud->allowAccess('updateEstado');
    $this->crud->addButtonFromModelFunction('top', 'export', 'exportButton', 'end');
}

    // Acción para actualizar el estado de la reserva
    public function updateEstado($id)
    {
        $reserva = Reserva::find($id);

        // Solo permite cambiar el estado si la reserva existe
        if (!$reserva) {
            return back()->withErrors(['error' => 'Reserva no encontrada']);
        }

        $estado_id = request()->get('estado_id');  // Obtener el estado del request

        // Verificar que el estado sea válido
        if (!Estado::find($estado_id)) {
            return back()->withErrors(['error' => 'Estado no válido']);
        }

        // Actualizar el estado de la reserva
        $reserva->estado_id = $estado_id;
        $reserva->save();

        return redirect()->route('backpack.crud.reserva.index')->with('success', 'Estado de la reserva actualizado con éxito.');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->setColumns([]);

        if (backpack_user()->hasRole('admin')) {
            $this->crud->removeButton('create');
        }


        if (backpack_user()->hasRole('cliente')) {
            $this->crud->removeButton('export');
        }

        if (backpack_user()->hasRole('admin')) {
            CRUD::addColumn([
                'name'      => 'user.name',
                'label'     => 'Cliente',
                'type'      => 'relationship',
            ]);
        }

        // Columna Sala
        CRUD::addColumn([
            'name'      => 'sala.nombre',
            'label'     => 'Sala',
            'type'      => 'relationship',
        ]);

        // Columna Fecha
        CRUD::addColumn([
            'name'  => 'fecha',
            'label' => 'Fecha',
            'type'  => 'date',
        ]);

        // Columna Hora de inicio
        CRUD::addColumn([
            'name'  => 'hora_inicio',
            'label' => 'Hora inicio',
            'type'  => 'time',
        ]);

        // Columna Estado
        CRUD::addColumn([
            'name'      => 'estado_id',
            'label'     => 'Estado',          // Etiqueta a mostrar
            'type'      => 'select',          // Tipo de campo select
            'entity'    => 'estado',          // Relación con el modelo Estado
            'model'     => 'App\Models\Estado',  // Modelo de estado
            'attribute' => 'nombre',          // Mostrar el nombre del estado
        ]);

        // Mostrar la columna Cliente solo si el usuario tiene el rol 'admin'

    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {

        try {
            CRUD::setValidation(ReservaRequest::class);

            // Campo sala
            CRUD::addField([
                'name'        => 'sala_id',
                'label'       => 'Sala',
                'type'        => 'select',
                'entity'      => 'sala',
                'attribute'   => 'nombre',
                'model'       => 'App\Models\Sala',
                'attributes' => [
                    'required' => 'required',  // Agregar validación de campo requerido
                ],
            ]);

            // Campo fecha
            CRUD::addField([
                'name'       => 'fecha',
                'label'      => 'Fecha de la reserva',
                'type'       => 'date',
                'attributes' => [
                    'min' => date('Y-m-d'),
                    'required' => 'required',
                ],
            ]);

            // Campo hora de inicio
            CRUD::addField([
                'name'  => 'hora_inicio',
                'label' => 'Hora de inicio',
                'type'  => 'time',
                'required' => 'required',
            ]);


            CRUD::addField([
                'name'  => 'user_id',
                'type'  => 'hidden',
                'value' => backpack_user()->id,  // o auth()->id()
            ]);
            // Estado (oculto para cliente, opcional)
            if (!backpack_user()->hasRole('admin')) {
                CRUD::addField([
                    'name'  => 'estado_id',
                    'type'  => 'hidden',
                    'value' => 1, // pendiente
                ]);
            } else {
                CRUD::addField([
                    'name'      => 'estado_id',
                    'label'     => 'Estado',
                    'type'      => 'select',
                    'entity'    => 'estado',
                    'attribute' => 'nombre',
                    'model'     => 'App\Models\Estado',
                ]);
            }

            CRUD::addField([
                'name'  => 'hora_fin',
                'type'  => 'hidden',
            ]);

        } catch (Exception $e) {
            $errorMessage = $reserva->errors()->first();

            \Alert::error($errorMessage)->flash();
            return back()->withInput();
        }

    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

           // Si el usuario es un administrador, puede cambiar el estado
    if (backpack_user()->hasRole('admin')) {
        CRUD::addField([  // Campo para cambiar el estado de la reserva
            'name'      => 'estado_id',
            'label'     => 'Estado',
            'type'      => 'select',
            'entity'    => 'estado',
            'model'     => 'App\Models\Estado',
            'attribute' => 'nombre',
        ]);
    } else {
        // Si no es admin, ocultamos el campo estado
        CRUD::addField([  // Campo de estado oculto para el cliente
            'name'  => 'estado_id',
            'type'  => 'hidden',
            'value' => 1,  // Asignamos el estado por defecto a "pendiente" o el que consideres
        ]);
    }

    // Solo el administrador podrá modificar estos campos, los clientes no
    CRUD::addField([  // Sala
        'name'  => 'sala_id',
        'type'  => 'select',
        'label' => 'Sala',
        'attributes' => ['disabled' => 'disabled'],  // Solo lectura para todos los usuarios
        'value' => 'Sala Actual',  // O se puede usar la sala actual con un valor dinámico
    ]);

    CRUD::addField([  // Hora de inicio
        'name'  => 'hora_inicio',
        'label' => 'Hora de inicio',
        'type'  => 'time',
        'attributes' => ['disabled' => 'disabled'],  // Solo lectura
    ]);

    CRUD::addField([  // Fecha de la reserva
        'name'  => 'fecha',
        'label' => 'Fecha de la reserva',
        'type'  => 'date',
        'attributes' => ['disabled' => 'disabled'],  // Solo lectura
    ]);
    }

    public function exportExcel()
    {
        return Excel::download(new ReservasExport, 'reservas_coworking.xlsx');
    }
}
