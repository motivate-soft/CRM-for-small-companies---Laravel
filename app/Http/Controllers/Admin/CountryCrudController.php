<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CountryRequest as StoreRequest;
use App\Http\Requests\CountryRequest as UpdateRequest;

/**
 * Class CountryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CountryCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Country');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/countries');
        $this->crud->setEntityNameStrings('country', 'countries');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->setColumns([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
            ],
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
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'holiday_days',
                'label' => trans('fields.holiday_days'),
                'type' => 'number'
            ],
            [
                'label'     => 'Working Days',
                'type'      => 'checklist',
                'name'      => 'workingdays',
                'entity'    => 'workingDays',
                'attribute' => 'name',
                'model'     => "App\Models\WorkingDays",
                'pivot'     => true,
            ],
        ]);
        // add asterisk for fields that are required in CountryRequest
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
