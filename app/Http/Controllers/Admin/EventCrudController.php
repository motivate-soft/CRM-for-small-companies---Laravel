<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\EventRequest as StoreRequest;
use App\Http\Requests\EventRequest as UpdateRequest;

/**
 * Class EventCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class EventCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Event');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/events');
        $this->crud->setEntityNameStrings(trans('fields.event'), trans('fields.events'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->removeButton('delete');

        $this->crud->addButtonFromView('line', 'delete-mandatory', 'delete-mandatory', 'beginning');

        $this->crud->setColumns([
            [
                'name'  => 'event_id',
                'label' => trans('fields.event_id'),
                'type'  => 'text',
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
                'name'  => 'event_id',
                'label' => trans('fields.event_id'),
                'type'  => 'text',
                'attributes' => $this->crud->getCurrentEntry() && $this->crud->getCurrentEntry()->mandatory ? ['readonly' => 'readonly'] : [],
            ],
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'company_id',
                'type'  => 'hidden',
                'value' => backpack_user()->company->id
            ],
        ]);

        // add asterisk for fields that are required in EventRequest
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
