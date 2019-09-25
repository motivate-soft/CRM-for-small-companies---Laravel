<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\PaymentTransaction');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/transaction');
        $this->crud->setEntityNameStrings(trans('fields.transaction'), trans('fields.transactions'));
        $this->crud->addButtonFromModelFunction('line', 'invoice', 'view_invoice', 'beginning'); // add a button whose HTML is returned by a method in the CRUD model

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->enableExportButtons();
//        $this->crud->allowAccess('show');

        $this->crud->orderBy('created_at', 'desc');

        $this->crud->setColumns([
            [
                'name'  => 'invoice_number',
                'label' => trans('fields.invoice_number'),
                'type'  => 'text',
            ],
            [
                'name'  => 'payment_type',
                'label' => trans('fields.payment_type'),
                'type'  => 'text',
            ],
            [
                'name'  => 'amount',
                'label' => trans('fields.amount'),
                'type'  => 'text',
            ],
            [
                'name'  => 'currency',
                'label' => trans('fields.currency'),
                'type'  => 'text',
            ],
            [
                'name'  => 'created_at',
                'label' => trans('fields.date'),
                'type'  => 'date',
            ],
        ]);

        if (backpack_user()->role == User::ROLE_ADMIN) {
            $this->crud->addColumn([
                'name' => 'company_id',
                'label' => trans('fields.company'),
                'type' => 'select',
                'entity' => 'company', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Company", // foreign key model
            ])->afterColumn('invoice_number');
        }

        if (backpack_user()->role == User::ROLE_COMPANY) {
            $this->crud->denyAccess('delete');
        }

        $this->crud->denyAccess('create');
        $this->crud->denyAccess('update');
    }
}
