<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\DepartmentRequest as StoreRequest;
use App\Http\Requests\DepartmentRequest as UpdateRequest;

/**
 * Class DepartmentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class DepartmentCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Department');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/departments');
        $this->crud->setEntityNameStrings(trans('fields.department'), trans('fields.departments'));
        $this->crud->addButtonFromModelFunction('line', 'calendar', 'get_calendar_button', 'end'); // add a button whose HTML is returned by a method in the CRUD model

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
                // n-n relationship (with pivot table)
                'label' => trans('fields.employees'),
                'type' => "select_multiple",
                'name' => 'employees', // the method that defines the relationship in your Model
                'entity' => 'employees', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Employee", // foreign key model
            ],
//            [
//                // n-n relationship (with pivot table)
//                'label' => trans('fields.holidays'),
//                'type' => "select_multiple",
//                'name' => 'holidays', // the method that defines the relationship in your Model
//                'entity' => 'holidays', // the method that defines the relationship in your Model
//                'attribute' => "name", // foreign key attribute that is shown to user
//                'model' => "App\Models\WorkingPlaceHoliday", // foreign key model
//            ],
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
//            [
//                'label' => trans('fields.employees'),
//                'type' => 'select2_multiple',
//                'name' => 'employees', // the method that defines the relationship in your Model
//                'entity' => 'employees', // the method that defines the relationship in your Model
//                'attribute' => 'name', // foreign key attribute that is shown to user
//                'model' => "App\Models\Employee", // foreign key model
//                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
//                'select_all' => true, // show Select All and Clear buttons?
//            ],
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
//            [
//                'label'     => 'Working Days',
//                'type'      => 'checklist',
//                'name'      => 'workingdays',
//                'entity'    => 'workingDays',
//                'attribute' => 'name',
//                'model'     => "App\Models\WorkingDays",
//                'pivot'     => true,
//            ],
            [
                'name'  => 'company_id',
                'type'  => 'hidden',
                'value' => backpack_user()->company->id
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
