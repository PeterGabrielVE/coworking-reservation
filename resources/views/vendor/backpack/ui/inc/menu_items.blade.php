{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item">
    <a class="nav-link" href="{{ backpack_url('dashboard') }}">
        <i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}
    </a>
</li>

@if(backpack_user()->hasRole('admin'))
    <x-backpack::menu-item title="Salas" icon="la la-building" :link="backpack_url('sala')" />
@endif

@if(backpack_user()->hasRole('admin') || backpack_user()->hasRole('cliente'))
    <x-backpack::menu-item title="Reservas" icon="la la-calendar" :link="backpack_url('reserva')" />
@endif
