<?php

namespace App\Http\Controllers\Admin;

use App\Models\Device;
use App\Models\Soap;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\DeviceRequest as StoreRequest;
use App\Http\Requests\DeviceRequest as UpdateRequest;
use Illuminate\Http\Request;

/**
 * Class DeviceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class DeviceCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Device');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/devices');
        $this->crud->setEntityNameStrings(trans('fields.device'), trans('fields.devices'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->removeButton('delete');

        $this->crud->addButtonFromView('line', 'get-device-data', 'get-device-data', 'end');
        $this->crud->addButtonFromView('line', 'delete-mandatory', 'delete-mandatory', 'end');

        $this->crud->setColumns([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'device_id',
                'label' => trans('fields.device_id'),
                'type'  => 'text',
            ],
            [
                'name'  => 'sub_device_id',
                'label' => trans('fields.sub_device_id'),
                'type'  => 'text',
            ],
            [
                'name'  => 'ip',
                'label' => trans('fields.ip'),
                'type'  => 'text',
            ],
            [
                'name'  => 'port',
                'label' => trans('fields.port'),
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
                'name'  => 'device_id',
                'label' => trans('fields.device_id'),
                'type'  => 'text',
                'attributes' => $this->crud->getCurrentEntry() && $this->crud->getCurrentEntry()->mandatory ? ['readonly' => 'readonly'] : [],
            ],
            [
                'name'  => 'sub_device_id',
                'label' => trans('fields.sub_device_id'),
                'type'  => 'text',
            ],
            [
                'name'  => 'ip',
                'label' => trans('fields.ip'),
                'type'  => 'text',
            ],
            [
                'name'  => 'port',
                'label' => trans('fields.port'),
                'type'  => 'text',
            ],
            [
                'name'  => 'company_id',
                'type'  => 'hidden',
                'value' => backpack_user()->company->id
            ],
        ]);

        // add asterisk for fields that are required in DeviceRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        $this->deviceSave($request);

        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $this->deviceSave($request);

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

        $device = $this->crud->model->find($id);

        if(!$device->mandatory) {

            $soap = new Soap();

            $soap->deviceDelete([
                "clientId" => backpack_user()->company->client_id,
                "deviceId" => $device->device_id,
                "subDeviceId" => $device->sub_device_id,
            ]);

            return $this->crud->delete($device->id);
        }
    }

    private function deviceSave($request)
    {
        $soap = new Soap();

        $soap->deviceSave([
            "clientId" => backpack_user()->company->client_id,
            "deviceId" => $request->device_id,
            "subDeviceId" => $request->sub_device_id,
            "deviceName" => $request->name,
            "ip" => $request->ip,
            "port" => $request->port,
            "deviceType" => 0,
            "deviceVersion" => 4,
        ]);
    }

    public function getData($id)
    {
        $device = Device::findOrFail($id);

        $soap = new Soap();

        $params = [
            "clientid" => backpack_user()->company->client_id,
            "deviceid" => $device->device_id,
            "subdeviceid" => $device->sub_device_id,
        ];

        $soap->logsGetNew($params);
        $soap->userGetNew($params);
    }
}
