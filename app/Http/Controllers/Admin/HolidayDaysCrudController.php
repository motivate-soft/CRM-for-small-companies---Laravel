<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\HolidayDaysRequest as StoreRequest;
use App\Http\Requests\HolidayDaysRequest as UpdateRequest;

/**
 * Class HolidayDaysCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class HolidayDaysCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\User');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/holidaydays');
        $this->crud->setEntityNameStrings(trans('fields.holiday_days'), trans('fields.holiday_days'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
//        $this->crud->setFromDb();
        $this->crud->setColumns([
            [
                'name'  => 'holiday_days',
                'label' => trans('fields.holiday_days'),
            ],
            [
                'label' => trans('fields.working_days'),
                'type' => "select_multiple",
                'name' => 'workingDays', // the method that defines the relationship in your Model
                'entity' => 'workingDays', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\WorkingDays", // foreign key model
            ]
        ]);

        $this->crud->addFields([
            [
                'name'  => 'holiday_days',
                'label' => trans('fields.holiday_days'),
                'type' => 'number'
            ],
            [
                'label'     => trans('fields.working_days'),
                'type'      => 'working_day',
                'name'      => 'workingDays',
                'entity'    => 'workingDays',
                'attribute' => 'id',
                'model'     => "App\Models\WorkingDays",
                'pivot'     => true,
            ],
        ]);
        // add asterisk for fields that are required in HolidayDaysRequest
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
