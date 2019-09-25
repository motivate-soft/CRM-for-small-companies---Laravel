<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest as UpdateRequest;
use App\Http\Requests\SubscriptionRequest as StoreRequest;


class SubscriptionCrudController extends CrudController
{
    public function setup()
    {
        $this->crud->setModel('App\Models\Subscription');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/subscriptions');
        $this->crud->setEntityNameStrings(trans('fields.subscription'), trans('fields.subscriptions'));

        $this->crud->addButtonFromModelFunction('line', 'view_invoice', 'get_invoice', 'end'); // add a button whose HTML is returned by a method in the CRUD model

        $this->crud->enableExportButtons();

        $this->crud->setColumns([
            [
                'name' => 'company_id',
                'label' => trans('fields.company'),
                'type' => 'select',
                'entity' => 'company', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Company", // foreign key model
                'allows_null' => false,
            ],
            [
                'name' => 'plan_id',
                'label' => trans('fields.plan'),
                'type' => 'select',
                'entity' => 'plan', // the method that defines the relationship in your Model
                'attribute' => 'currency_id', // foreign key attribute that is shown to user
                'model' => "App\Models\Plan", // foreign key model
                'allows_null' => false,
            ],
            [
                'name' => 'subscription_date',
                'label' => trans('fields.subscription_date'),
                'type' => 'date'
            ]
        ]);

        // add asterisk for fields that are required in DepartmentRequest
        $this->crud->denyAccess('create');
        $this->crud->denyAccess('edit');
        $this->crud->denyAccess('delete');
    }

    public function store(StoreRequest $request)
    {
        // your additional operations before save here
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
