<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlanUpdateRequest as UpdateRequest;
use App\Http\Requests\PlanRequest as StoreRequest;
use Illuminate\Support\Facades\Validator;

class PlanCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Plan');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/plans');
        $this->crud->setEntityNameStrings(trans('fields.plan'), trans('fields.plans'));
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->enableExportButtons();

        $this->crud->setColumns([
            [
                'name' => 'currency_id',
                'label' => trans('fields.currency'),
                'type' => 'select',
                'entity' => 'currency', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Currency", // foreign key model
                'allows_null' => false,
            ],
            [
                'name' => 'free_month',
                'label' => trans('fields.free_month'),
                'type' => 'text'
            ]
        ]);


        $this->crud->addFields([
            [
                'name' => 'currency_id',
                'label' => trans('fields.currency'),
                'type' => 'select2',
                'entity' => 'currency', // the method that defines the relationship in your Model
                'attribute' => 'short_key', // foreign key attribute that is shown to user
                'model' => "App\Models\Currency", // foreign key model
                'allows_null' => false,
            ],
            [
                'name' => 'free_month',
                'label' => trans('fields.free_month'),
                'type' => 'text'
            ],
            [ // Table
                'name' => 'data',
                'label' => 'Plans',
                'type' => 'table',
                'entity_singular' => 'option', // used on the "Add X" button
                'columns' => [
//                    'name' => trans('fields.name'),
                    'min' => trans('fields.min'),
                    'max' => trans('fields.max'),
                    'price' => trans('fields.price'),
                ],
                'max' => 5, // maximum rows allowed in the table
                'min' => 3, // minimum rows allowed in the table,
//                'sortable' => false
            ],
        ]);

        // add asterisk for fields that are required in DepartmentRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
        $this->crud->denyAccess('delete');
    }

    public function store(StoreRequest $request)
    {
        $plan_data = json_decode($request->data, true);
        $rules = [
            '*.min' => 'required|numeric', //Must be a number and length of value is 8
            '*.max' => 'required|numeric',
            '*.price' => 'required|numeric'
        ];
        $validator = Validator::make($plan_data, $rules);

        if ($validator->passes()) {
            // your additional operations before save here


            $redirect_location = parent::storeCrud($request);
            // your additional operations after save here
            // use $this->data['entry'] or $this->crud->entry
            Plan::addPaypalPlan($request);

//            Plan::addStripPlan($request);

            return $redirect_location;
        }else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function update(UpdateRequest $request)
    {
        $plan_data = json_decode($request->data, true);
        $rules = [
            '*.min' => 'required|numeric', //Must be a number and length of value is 8
            '*.max' => 'required|numeric',
            '*.price' => 'required|numeric'
        ];
        $validator = Validator::make($plan_data, $rules);
        if ($validator->passes()) {
            // your additional operations before save here
            Plan::updatePaypalPlan($request);
            die();
            $redirect_location = parent::updateCrud($request);
            // your additional operations after save here
            // use $this->data['entry'] or $this->crud->entry
            Plan::updateStripPlan($request);

            return $redirect_location;
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }
}
