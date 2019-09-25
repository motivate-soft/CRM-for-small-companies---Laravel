<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ScheduleRequest as StoreRequest;
use App\Http\Requests\ScheduleRequest as UpdateRequest;

/**
 * Class ScheduleCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ScheduleCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Schedule');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/schedules');
        $this->crud->setEntityNameStrings(trans('fields.schedule'), trans('fields.schedules'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->addClause('notDeviceSchedules');

        $this->crud->setCreateContentClass('col-md-10 col-md-offset-1');
        $this->crud->setEditContentClass('col-md-10 col-md-offset-1');

        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => trans('fields.name'),
                'type' => 'text',
            ],
            [
                // 1-n relationship
                'label' => trans('fields.department'), // Table column heading
                'type' => "select",
                'name' => 'department_id', // the column that contains the ID of that connected entity;
                'entity' => 'department', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Department", // foreign key model
            ],
            [
                'name' => 'created_at',
                'label' => trans('fields.created_at'),
                'type' => 'date',
            ],
        ]);

        // Fields
        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => trans('fields.name'),
                'type' => 'text',
            ],
            [
                'label' => trans('fields.department'),
                'type' => 'select',
                'name' => 'department_id', // the db column for the foreign key
                'entity' => 'department', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Department", // foreign key model
            ],
            [
                'name' => 'data',
                'label' => trans('fields.schedule'),
                'type' => 'schedule',
            ]
        ]);

        // add asterisk for fields that are required in ScheduleRequest
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
