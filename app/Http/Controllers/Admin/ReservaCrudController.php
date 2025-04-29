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

        $this->crud->addFields([
            [
                'name' => 'sala_id',
                'label' => 'Sala',
                'type' => 'select',
                'entity' => 'sala',
                'attribute' => 'nombre',
                'attributes' => ['required' => 'required']
            ],
            [
                'name' => 'fecha',
                'label' => 'Fecha de la reserva',
                'type' => 'date',
                'attributes' => [
                    'min' => date('Y-m-d'),
                    'required' => 'required'
                ]
            ],
            [
                'name' => 'hora_inicio',
                'label' => 'Hora de inicio',
                'type' => 'time',
                'attributes' => ['required' => 'required']
            ],
            [
                'name' => 'user_id',
                'type' => 'hidden',
                'value' => backpack_user()->id
            ],
            [
                'name' => 'estado_id',
                'type' => 'hidden',
                'value' => backpack_user()->hasRole('admin') ? null : 1
            ],
            [
                'name' => 'hora_fin',
                'type' => 'hidden'
            ]
        ]);

        // Configuración de redirección después de crear

    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        if (backpack_user()->hasRole('admin')) {
            CRUD::modifyField('estado_id', [
                'type' => 'select',
                'label' => 'Estado',
                'entity' => 'estado',
                'attribute' => 'nombre'
            ]);
        }
    }
}
