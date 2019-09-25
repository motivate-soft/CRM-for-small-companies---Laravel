<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ActionsOptionRequest as StoreRequest;
use App\Http\Requests\ActionsOptionRequest as UpdateRequest;

/**
 * Class EmployeeActionOptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ActionsOptionCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\ActionsOption');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/actions-options');
        $this->crud->setEntityNameStrings(trans('fields.action_option'), trans('fields.action_options'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */


        $this->crud->removeButton('delete');

        $this->crud->addButtonFromView('line', 'delete-mandatory', 'delete-mandatory', 'end');


        $this->crud->setColumns([
            [
                // 1-n relationship
                'label' => trans('fields.event'),
                'type' => "select",
                'name' => 'event_id', // the column that contains the ID of that connected entity;
                'entity' => 'event', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Event", // foreign key model
            ],
            [
                // 1-n relationship
                'label' => trans('fields.device'),
                'type' => "select",
                'name' => 'device_id', // the column that contains the ID of that connected entity;
                'entity' => 'device', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'model' => "App\Models\Device", // foreign key model
            ],
            [   // Enum
                'name' => 'type',
                'label' => trans('fields.type'),
                'type' => 'enum'
            ],
            [
                'name' => 'key',
                'label' => trans('fields.key'),
                'type' => 'number',
            ],
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
        ]);

        // Fields
        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
            [
                'name' => 'key',
                'label' => trans('fields.key'),
                'type' => 'number',
            ],
            [  // Select2
                'label' => trans('fields.event'),
                'type' => 'select2',
                'name' => 'event_id', // the db column for the foreign key
                'entity' => 'event', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Event", // foreign key model
            ],
            [   // Enum
                'name' => 'type',
                'label' => trans('fields.type'),
                'type' => 'enum'
            ],
            [  // Select2
                'label' => trans('fields.device'),
                'type' => 'select2',
                'name' => 'device_id', // the db column for the foreign key
                'entity' => 'device', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Device", // foreign key model
            ],
            [
                'name'  => 'company_id',
                'type'  => 'hidden',
                'value' => backpack_user()->company->id
            ],
        ]);

        // add asterisk for fields that are required in EmployeeActionOptionRequest
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

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');
        $this->crud->setOperation('delete');

        if(!$this->crud->model->find($id)->mandatory) {
            // get entry ID from Request (makes sure its the last ID for nested resources)
            $id = $this->crud->getCurrentEntryId() ?? $id;

            return $this->crud->delete($id);
        }
    }

}
