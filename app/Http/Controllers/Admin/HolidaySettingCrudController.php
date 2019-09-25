<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\HolidaysettingRequest;
use App\Models\HolidaySetting;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HolidaySettingCrudController extends CrudController
{
    public function setup()
    {
        $this->crud->setModel('App\Models\HolidaySetting');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/holiday_setting');
        $this->crud->setEntityNameStrings(trans('fields.holiday_setting'), trans('fields.holiday_setting'));

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->removeButton('delete');

        $this->crud->addButtonFromView('line', 'delete-mandatory', 'delete-mandatory', 'beginning');

        $this->crud->setColumns([
            [
                'name'  => 'department_id',
                'label' => trans('fields.department'),
                'type'  => 'select',
                'entity' => 'department',
                'attribute' => "name",
                'model' => "App\Models\Department",
            ],
            [
                'name' => 'year',
                'label' => trans('fields.year'),
                'type' => 'select2_from_array',
                'options' => HolidaySetting::getYearOption(),
                'allows_null' => false,
            ],
            [
                'name' => 'default_holidays_per_employee',
                'type' => 'number',
                'label' => trans('fields.default_holidays_per_employee'),
            ],
            [   // ColorPicker
                'name' => 'expire_date',
                'label' => trans('fields.expire_date'),
                'type' => 'text',
            ],
        ]);

        // Fields
        $this->crud->addFields([
            [
                'name'  => 'department_id',
                'label' => trans('fields.department'),
                'type'  => 'select',
                'entity' => 'department',
                'attribute' => "name",
                'model' => "App\Models\Department",
            ],
            [
                'name' => 'year',
                'label' => trans('fields.year'),
                'type' => 'select2_from_array',
                'options' => HolidaySetting::getYearOption(),
                'allows_null' => false,
            ],
            [
                'name' => 'default_holidays_per_employee',
                'type' => 'number',
                'label' => trans('fields.default_holidays_per_employee'),
            ],
            [   // ColorPicker
                'name' => 'expire_date',
                'label' => trans('fields.expire_date'),
                'type' => 'date_picker',
                'date_picker_options' => [
                    'todayBtn' => 'linked',
                    'format' => 'yyyy-mm-dd',
                    'language' => backpack_user()->company->language->abbr
                ],
            ],
        ]);

        // add asterisk for fields that are required in EventRequest

        $this->crud->setRequiredFields(HolidaysettingRequest::class, 'create');
        $this->crud->setRequiredFields(HolidaysettingRequest::class, 'edit');
    }

    public function store(HolidaysettingRequest $request)
    {
        $year = $request->year;
        $expire_date = $request->expire_date;
        $expire_year = strtok($expire_date, '-');

        $department = $request->department_id;
        $department_holiday_setting = HolidaySetting::where('department_id', $department)->where('year', $year)->get();
        if (count($department_holiday_setting) > 0) {
            return redirect()->back()->withErrors(trans('fields.holiday_setting_already_crated_error'));
        }
        if (((int)$year + 1) != $expire_year) {
            return redirect()->back()->withErrors(trans('fields.expire_date_setting_error'));
        }

        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update(HolidaysettingRequest $request)
    {
        $year = $request->year;
        $expire_date = $request->expire_date;
        $expire_year = strtok($expire_date, '-');
        $department = $request->department_id;
        $department_holiday_setting = HolidaySetting::where('department_id', $department)->where('year', $year)->get();

        if (count($department_holiday_setting) > 0 && $request->id != $department_holiday_setting[0]->id) {
            return redirect()->back()->withErrors(trans('fields.holiday_setting_already_crated_error'));
        }

        if (((int)$year + 1) != $expire_year) {
                return redirect()->back()->withErrors(trans('fields.expire_date_setting_error'));
        }

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
