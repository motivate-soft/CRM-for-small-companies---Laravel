<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\WorkcenterRequest as StoreRequest;
use App\Http\Requests\WorkcenterRequest as UpdateRequest;

/**
 * Class WorkcenterCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class WorkcenterCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Workcenter');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/workcenters');
        $this->crud->setEntityNameStrings(trans('fields.work_center'), trans('fields.work_centers'));
        $this->crud->addButtonFromModelFunction('line', 'calendar', 'get_calendar_button', 'end'); // add a button whose HTML is returned by a method in the CRUD model
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->enableExportButtons();

        $this->crud->allowAccess('show');

        $this->crud->setColumns([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'country',
                'label' => trans('fields.country'),
                'type'  => 'text',
            ],
            [
                'name'  => 'state',
                'label' => trans('fields.state'),
                'type'  => 'text',
            ],
            [
                'name'  => 'city',
                'label' => trans('fields.city'),
                'type'  => 'text',
            ],
            [
                'name'  => 'zip_code',
                'label' => trans('fields.zip_code'),
                'type'  => 'text',
            ],
            [
                'name'  => 'address',
                'label' => trans('fields.address'),
                'type'  => 'text',
            ],
            [
                'name'  => 'location',
                'label' => trans('fields.location'),
                'type'  => 'text',
                'hint'  => trans('fields.location_hint'),
            ],
            [
                'name'  => 'wifi',
                'label' => trans('fields.wifi'),
                'type'  => 'text',
            ],
            [
                'name'  => 'cell',
                'label' => trans('fields.cell'),
                'type'  => 'text',
            ],
            [
                // n-n relationship (with pivot table)
                'label' => trans('fields.holidays'),
                'type' => "select_multiple",
                'name' => 'holidays', // the method that defines the relationship in your Model
                'entity' => 'holidays', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\WorkingPlaceHoliday", // foreign key model
            ],
            [
                // n-n relationship (with pivot table)
                'label' => trans('fields.events'),
                'type' => "select_multiple",
                'name' => 'events', // the method that defines the relationship in your Model
                'entity' => 'events', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\CalendarEvent", // foreign key model
            ],
        ]);

        // Fields
        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'country',
                'label' => trans('fields.country'),
                'type'  => 'text',
            ],
            [
                'name'  => 'state',
                'label' => trans('fields.state'),
                'type'  => 'text',
            ],
            [
                'name'  => 'city',
                'label' => trans('fields.city'),
                'type'  => 'text',
            ],
            [
                'name'  => 'zip_code',
                'label' => trans('fields.zip_code'),
                'type'  => 'text',
            ],
            [
                'name'  => 'address',
                'label' => trans('fields.address'),
                'type'  => 'address_algolia',
            ],
            [
                'name'  => 'location',
                'label' => trans('fields.location'),
                'type'  => 'text',
                'hint'  => trans('fields.location_hint'),
            ],
            [
                'name'  => 'wifi',
                'label' => trans('fields.wifi'),
                'type'  => 'text',
            ],
            [
                'name'  => 'cell',
                'label' => trans('fields.cell'),
                'type'  => 'text',
            ],
            [
                // n-n relationship (with pivot table)
                'label' => trans('fields.holidays'),
                'type' => "select2_multiple",
                'name' => 'holidays', // the method that defines the relationship in your Model
                'entity' => 'holidays', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\WorkingPlaceHoliday", // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                'select_all' => true, // show Select All and Clear buttons?

            ],
            [
                // n-n relationship (with pivot table)
                'label' => trans('fields.events'),
                'type' => "select2_multiple",
                'name' => 'events', // the method that defines the relationship in your Model
                'entity' => 'events', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\CalendarEvent", // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                'select_all' => true, // show Select All and Clear buttons?
            ],
            [
                'name'  => 'company_id',
                'type'  => 'hidden',
                'value' => backpack_user()->company->id
            ],
        ]);

        // add asterisk for fields that are required in WorkcenterRequest
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
