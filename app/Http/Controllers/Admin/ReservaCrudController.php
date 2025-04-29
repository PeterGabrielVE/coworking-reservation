<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReservaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReservasExport;

/**
 * Class ReservaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReservaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
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

    // Denegar creación, edición y borrado
    $this->crud->denyAccess(['create', 'update', 'delete']);

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
        CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ReservaRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
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
    }

    public function exportExcel()
    {
        return Excel::download(new ReservasExport, 'reservas_coworking.xlsx');
    }
}
