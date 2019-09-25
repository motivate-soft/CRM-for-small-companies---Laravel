<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ActionRequest as StoreRequest;
use App\Http\Requests\ActionRequest as UpdateRequest;
use App\Models\Employee;
use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Carbon\Carbon;

// VALIDATION: change the requests to match your own file names if you need form validation

/**
 * Class TimeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ActionCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Action');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/actions');
        $this->crud->setEntityNameStrings(trans('fields.action'), trans('fields.actions'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $this->crud->denyAccess(['create', 'update', 'delete']);
            $this->crud->removeAllButtons();

            $this->crud->setListContentClass('col-md-8 col-md-offset-2');
        }

        $this->crud->setDefaultPageLength(50);

        $this->crud->orderBy('datetime', 'DESC');

        $this->crud->setColumns([
            [
                'name' => 'datetime',
                'label' => trans('fields.datetime'),
                'type' => 'datetime',
            ],
            [
                // 1-n relationship
                'label' => trans('fields.option'),
                'type' => "select",
                'name' => 'option_id', // the column that contains the ID of that connected entity;
                'entity' => 'option', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\ActionsOption", // foreign key model
            ],
            [
                'name' => 'gps',
                'label' => trans('GPS/IP'),
                // 'type' => 'image',
                // 'prefix' => 'images/gps.png?ip',
                'type' => "model_function",
                'function_name' => 'getMap',
            ],
            [
                'name' => 'device',
                'label' => trans('fields.device'),
                'type' => 'text',
            ],
        ]);

        if (backpack_user()->role == User::ROLE_COMPANY) {
            $this->crud->addColumn([  // Select2
                'label' => trans('fields.employee'),
                'type' => "select",
                'name' => 'employee_id', // the column that contains the ID of that connected entity;
                'entity' => 'employee', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Employee", // foreign key model
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('employee', function ($q) use ($column, $searchTerm) {
                        $q->whereHas('user', function ($q) use ($column, $searchTerm) {
                            $q->where('name', 'like', '%' . $searchTerm . '%');
                        });
                    });
                }
            ]);
        }

        // Fields
        $this->crud->addFields([
            [   // DateTime
                'name' => 'datetime',
                'label' => trans('fields.datetime'),
                'type' => 'datetime_picker',
                'allows_null' => false,
                'default' => Carbon::now(),
            ],
            [  // Select2
                'label' => trans('fields.option'),
                'type' => 'select2',
                'name' => 'option_id', // the db column for the foreign key
                'entity' => 'option', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\ActionsOption", // foreign key model
            ],
        ]);

        if (backpack_user()->role == User::ROLE_COMPANY) {
            $this->crud->addField([  // Select2
                'label' => trans('fields.employee'),
                'type' => 'select2',
                'name' => 'employee_id', // the db column for the foreign key
                'entity' => 'employee', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Employee", // foreign key model
            ]);
        }

        $this->crud->addFilter([ // date filter
            'type' => 'date_range',
            'name' => 'datetime',
            'label' => trans('fields.date')
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'datetime', '>=', $dates->from);
                $this->crud->addClause('where', 'datetime', '<=', $dates->to . ' 23:59:59');
            });

        if (backpack_user()->role == User::ROLE_COMPANY) {
            $this->crud->addFilter([ // select2_ajax filter
                'name' => 'employee_id',
                'type' => 'select2',
                'label' => trans('fields.employee'),
            ],
                function () {
                    return Employee::with('user')->get()->pluck('name', 'id')->toArray();
                },
                function ($value) { // if the filter is active
                    $this->crud->addClause('where', 'employee_id', $value);
                });
        }

        // add asterisk for fields that are required in TimeRequest
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
