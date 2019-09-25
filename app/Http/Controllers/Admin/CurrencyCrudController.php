<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CurrencyRequest as UpdateRequest;
use App\Http\Requests\CurrencyRequest as StoreRequest;



class CurrencyCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Currency');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/currencies');
        $this->crud->setEntityNameStrings(trans('fields.currency'), trans('fields.currencies'));
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->enableExportButtons();

        $this->crud->setColumns([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'short_key',
                'label' => trans('fields.short_key'),
                'type'  => 'text',
            ],
            [
                'name'  => 'symbol',
                'label' => trans('fields.symbol'),
                'type'  => 'text',
            ],
        ]);


        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'short_key',
                'label' => trans('fields.short_key'),
                'type'  => 'text',
            ],
            [
                'name'  => 'symbol',
                'label' => trans('fields.symbol'),
                'type'  => 'text',
            ],
        ]);

        // add asterisk for fields that are required in DepartmentRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');

    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
