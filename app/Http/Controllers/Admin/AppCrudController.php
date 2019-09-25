<?php

namespace App\Http\Controllers\Admin;

use App\Models\App;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\AppRequest as StoreRequest;
use App\Http\Requests\AppRequest as UpdateRequest;
use Carbon\Carbon;
use function foo\func;
use League\Flysystem\Config;

/**
 * Class AppCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class AppCrudController extends CrudController
{
//    protected $app_link = config('backpack_config.app_base_link');

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\App');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/apps');
        $this->crud->setEntityNameStrings(trans('fields.app'), trans('fields.apps'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->enableExportButtons();

        $this->crud->setColumns([
            [
                'name'  => 'app_id',
                'label' => trans('fields.app_id'),
                'type'  => 'text',
            ],
            [
                'name'  => 'description',
                'label' => trans('fields.description'),
                'type'  => 'text',
            ],
            [
                'label' => trans('fields.employee'), // Table column heading
                'type' => "select",
                'name' => 'employee_id', // the column that contains the ID of that connected entity;
                'entity' => 'employee', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Employee", // foreign key model
            ],
            [
                'label' => trans('fields.code'), // Table column heading
                'type' => "text",
                'name' => 'temporal_code', // the column that contains the ID of that connected entity;
//                'entity' => 'employee', // the method that defines the relationship in your Model
//                'attribute' => "name", // foreign key attribute that is shown to user
//                'model' => "App\Models\Employee", // foreign key model
            ],
            [
                'label' => trans('fields.link'),
                'name' => '',
                'type' => 'closure',
                'function' => function($entry) {
                    return config('backpack_config.android_app_base_link') . $entry->temporal_code . '<br>' . config('backpack_config.ios_app_base_link') . $entry->temporal_code;
                }
            ],
            [
                'label' => trans('fields.expire_date'),
                'name' => 'created_at',
                'type' => 'closure',
                'function' => function($entry) {
                    $created_date = new Carbon($entry->created_at);
                    $end_date = date('Y-m-d', strtotime($created_date .' +3 day'));
                    return $end_date;
                }
            ],
            [
                'label' => trans('fields.status'),
                'type' => 'closure',
                'function' => function($entry) {
                    $created_date = new Carbon($entry->created_at);
                    $now_date = Carbon::now();
                    $difference = $created_date->diff($now_date)->days;

                    if ($entry->app_id) {
                        return trans('fields.approved');
                    } else {
                        if ($difference > config('backpack_config.app_link_expire_date'))  {
                            return trans('fields.expired');
                        } else {
                            return trans('fields.pending');
                        }
                    }
                }
            ]
        ]);



        // Fields
        $this->crud->addFields([
//            [
//                'name'  => 'app_id',
//                'label' => trans('fields.app_id'),
//                'type'  => 'text',
//            ],
            [
                'name'  => 'description',
                'label' => trans('fields.description'),
                'type'  => 'textarea',
            ],
            [
                'label' => trans('fields.employee'),
                'type' => 'select2',
                'name' => 'employee_id', // the db column for the foreign key
                'entity' => 'employee', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Employee", // foreign key model
            ],
        ]);

        // add asterisk for fields that are required in AppRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
        $request->request->set('temporal_code', rand(1111, 9999));
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
