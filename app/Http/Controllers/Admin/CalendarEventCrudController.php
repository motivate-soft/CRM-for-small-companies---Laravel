<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CalendarEventRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class CalendarEventCrudController extends CrudController
{
    public function setup()
    {
        $this->crud->setModel('App\Models\CalendarEvent');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/calendar_event');
        $this->crud->setEntityNameStrings(trans('fields.event'), trans('fields.events'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

//        $this->crud->removeButton('delete');

        $this->crud->addButtonFromView('line', 'delete-mandatory', 'delete-mandatory', 'beginning');

        $this->crud->setColumns([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'start_date',
                'label' => trans('fields.start_date'),
                'type'  => 'text',
            ],
            [
                'name'  => 'end_date',
                'label' => trans('fields.end_date'),
                'type'  => 'text',
            ],
            [
                // n-n relationship (with pivot table)
                'label' => trans('fields.work_centers'),
                'type' => "select_multiple",
                'name' => 'workcenters', // the method that defines the relationship in your Model
                'entity' => 'workcenters', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Workcenter", // foreign key model
            ],
            [
                // n-n relationship (with pivot table)
                'label' => trans('fields.departments'),
                'type' => "select_multiple",
                'name' => 'departments', // the method that defines the relationship in your Model
                'entity' => 'departments', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Department", // foreign key model
            ],
        ]);

        // if(backpack_user()->role == User::ROLE_COMPANY) {
        // $this->crud->denyAccess(['create']);
        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
            [
                'name' => 'date', // a unique name for this field
                'start_name' => 'start_date', // the db column that holds the start_date
                'end_name' => 'end_date', // the db column that holds the end_date
                'label' => trans('fields.date'),
                'type' => 'date_range',
                // OPTIONALS
                'start_default' => Carbon::now()->subWeek()->toDateString(), // default value for start_date
                'end_default' => Carbon::now()->toDateString(), // default value for end_date
                'date_range_options' => [ // options sent to daterangepicker.js
                    'timePicker' => false,
                    'locale' => ['format' => 'DD/MM/YYYY', 'firstDay' => 1],
                    'alwaysShowCalendars' => true,
                    'autoUpdateInput' => true,
                ]
            ],
            [
                'name'  => 'comment',
                'label' => trans('fields.comment'),
                'type'  => 'textarea',
            ],
            [
                'label' => trans('fields.work_centers'),
                'type' => 'select2_multiple',
                'name' => 'workcenters', // the method that defines the relationship in your Model
                'entity' => 'workcenters', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Workcenter", // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                'select_all' => true, // show Select All and Clear buttons?
            ],
            [
                'label' => trans('fields.departments'),
                'type' => 'select2_multiple',
                'name' => 'departments', // the method that defines the relationship in your Model
                'entity' => 'departments', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Department", // foreign key model
                'pivot' => true, // on create&update, do you need to add/delete pivot table entries?
                'select_all' => true, // show Select All and Clear buttons?
            ],
        ]);

        // add asterisk for fields that are required in EventRequest
        $this->crud->setRequiredFields(CalendarEventRequest::class, 'create');
        $this->crud->setRequiredFields(CalendarEventRequest::class, 'edit');
    }

    public function store(Request $request)
    {
        $request->request->set('company_id', backpack_user()->company->id);
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(Request $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
