<?php

namespace App\Http\Controllers\Admin;

use App\Models\App;
use App\Models\FcmToken;
use App\Http\Requests\NotificationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationCrudController extends CrudController
{
    public function setup()
    {
        $this->crud->setModel('App\Models\Notification');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/notification');
        $this->crud->setEntityNameStrings(trans('fields.notification'), trans('fields.notification'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->removeButton('delete');

        $this->crud->addButtonFromView('line', 'delete-mandatory', 'delete-mandatory', 'beginning');

        $this->crud->setColumns([
            [
                'label' => trans('fields.employee'), // Table column heading
                'type' => 'closure',
               // 'name' => 'token_id', // the column that contains the ID of that connected entity;
                'function' => function($item) {
//                    return $token = FcmToken::find($item->id)->id;
                    $app_id = FcmToken::where('id',$item->token_id)->first()->app_id;
                    return $employee = App::find($app_id)->employee->email;
                }
            ],
            [
                'name' => 'status',
                'label' => trans('fields.status'),
                'type' => 'select_from_array',
                'options' => ['received' => trans('fields.received'), 'dismissed' => trans('fields.dismissed'), 'read' => trans('fields.read')],
                'allows_null' => true,
            ],
            [
                'name' => 'created_at',
                'label' => trans('fields.created_at'),
                'type' => 'text',
            ],
        ]);
        $this->crud->denyAccess(['update']);
        $this->crud->denyAccess(['create']);
    }
}
