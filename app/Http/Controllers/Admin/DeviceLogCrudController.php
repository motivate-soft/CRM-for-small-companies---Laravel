<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

/**
 * Class DeviceLogCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class DeviceLogCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\DeviceLog');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/devices/logs');
        $this->crud->setEntityNameStrings(trans('fields.log'), trans('fields.logs'));

        $this->crud->denyAccess(['create', 'update', 'delete']);
        $this->crud->removeAllButtons();

        $this->crud->setListContentClass('col-md-8 col-md-offset-2');

        $this->crud->setDefaultPageLength(50);

        $this->crud->orderBy('created_at', 'DESC');

        $this->crud->setColumns([
            [
                'name'  => 'created_at',
                'label' => trans('fields.datetime'),
                'type'  => 'datetime',
                'format' => 'j F Y H:i:s',
            ],
            [
                'name'  => 'description',
                'label' => trans('fields.description'),
                'type'  => 'text',
                'limit' => 255
            ],
            [
                'name'  => 'transaction_id',
                'label' => trans('fields.transaction_id'),
                'type'  => 'text',
                'limit' => 255
            ],
        ]);


    }

}
