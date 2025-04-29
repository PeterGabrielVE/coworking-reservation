<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReservaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReservasExport;

class ReservaCrudController extends CrudController
{
    use ListOperation, CreateOperation, UpdateOperation, DeleteOperation, ShowOperation;

    public function setup()
    {
        $this->crud->setModel(\App\Models\Reserva::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/reserva');
        $this->crud->setEntityNameStrings('reserva', 'reservas');

        // Configuración de permisos básica
        $this->setupAccessPermissions();

        // Configura columnas comunes
        $this->setupCommonColumns();

        $this->filterReservasByUser();
    }

    protected function setupListOperation()
    {
        $this->setupCommonColumns();

        // Ocultar columna de cliente si no es admin
        if (!backpack_user()->hasRole('admin')) {
            $this->crud->removeColumn('user.name');
        }
    }

    protected function filterReservasByUser()
    {
        if (backpack_user() && !backpack_user()->hasRole('admin')) {
            $this->crud->addClause('where', 'user_id', backpack_user()->id);
        }
    }


    protected function setupAccessPermissions()
    {
        // Permisos básicos para todos los usuarios
        $this->crud->allowAccess(['list', 'show']);

        // Permisos condicionales
        if (backpack_user()->hasRole('admin')) {
            $this->crud->addButtonFromModelFunction('top', 'export', 'exportButton', 'end');
            $this->crud->denyAccess(['create', 'delete', 'export']);
            $this->crud->allowAccess(['update']);
        } else {
            $this->crud->allowAccess('create');
            $this->crud->denyAccess(['update', 'delete']);
        }
    }

    protected function setupCommonColumns()
    {
        $this->crud->addColumns([
            [
                'name' => 'user.name',
                'label' => 'Cliente',
                'type' => 'relationship',
                'entity' => 'user',
                'attribute' => 'name',
                'visible' => backpack_user()->hasRole('admin')
            ],
            [
                'name' => 'sala.nombre',
                'label' => 'Sala',
                'type' => 'relationship',
                'entity' => 'sala',
                'attribute' => 'nombre'
            ],
            [
                'name' => 'fecha',
                'label' => 'Fecha',
                'type' => 'date'
            ],
            [
                'name' => 'hora_inicio',
                'label' => 'Hora inicio',
                'type' => 'time'
            ],
            [
                'name' => 'estado_id',
                'label' => 'Estado',
                'type' => 'select',
                'entity' => 'estado',
                'attribute' => 'nombre'
            ]
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ReservaRequest::class);

        $this->addSalaField();
        $this->addFechaField();
        $this->addHoraInicioField();
        $this->addUserField();
        $this->addEstadoField();
        $this->addHoraFinField();

        // Configuración de redirección después de crear

    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(ReservaRequest::class);

        $this->addBaseFields();

        if (backpack_user()->hasRole('admin')) {
            CRUD::addField([
                'name' => 'estado_id',
                'label' => 'Estado',
                'type' => 'select',
                'entity' => 'estado',
                'attribute' => 'nombre'
            ]);
        }
    }

    private function addBaseFields()
    {
        CRUD::addFields([
            [
                'name' => 'sala_id',
                'type' => 'select',
                'label' => 'Sala',
                'entity' => 'sala',
                'attribute' => 'nombre',
                'attributes' => ['disabled' => 'disabled'],
                'wrapper' => [
                    'class' => 'form-group col-md-12'
                ],
            ],
            [
                'name' => 'fecha',
                'label' => 'Fecha',
                'type' => 'date',
                'attributes' => ['disabled' => 'disabled'],
            ],
            [
                'name' => 'hora_inicio',
                'label' => 'Hora inicio',
                'type' => 'time',
                'attributes' => ['disabled' => 'disabled'],
            ]
        ]);
    }

    protected function addSalaField()
    {
        CRUD::addField([
            'name' => 'sala_id',
            'label' => 'Sala',
            'type' => 'select',
            'entity' => 'sala',
            'attribute' => 'nombre',
            'model' => 'App\Models\Sala',
            'attributes' => ['required' => 'required'],
        ]);
    }

    protected function addFechaField()
    {
        CRUD::addField([
            'name' => 'fecha',
            'label' => 'Fecha de la reserva',
            'type' => 'date',
            'attributes' => [
                'min' => date('Y-m-d'),
                'required' => 'required',
            ],
        ]);
    }

    protected function addHoraInicioField()
    {
        CRUD::addField([
            'name' => 'hora_inicio',
            'label' => 'Hora de inicio',
            'type' => 'time',
            'required' => 'required',
        ]);
    }

    protected function addUserField()
    {
        CRUD::addField([
            'name' => 'user_id',
            'type' => 'hidden',
            'value' => backpack_user()->id,
        ]);
    }

    protected function addEstadoField()
    {
        if (!backpack_user()->hasRole('admin')) {
            CRUD::addField([
                'name' => 'estado_id',
                'type' => 'hidden',
                'value' => 1, // Pendiente
            ]);
        } else {
            CRUD::addField([
                'name' => 'estado_id',
                'label' => 'Estado',
                'type' => 'select',
                'entity' => 'estado',
                'attribute' => 'nombre',
                'model' => 'App\Models\Estado',
            ]);
        }
    }

    protected function addHoraFinField()
    {
        CRUD::addField([
            'name' => 'hora_fin',
            'type' => 'hidden',
        ]);
    }
    public function exportExcel()
    {
        return Excel::download(new ReservasExport, 'reservas_coworking.xlsx');
    }
}
