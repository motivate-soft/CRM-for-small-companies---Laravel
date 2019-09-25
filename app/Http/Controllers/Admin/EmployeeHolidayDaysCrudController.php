<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeHolidayDays;
use App\Models\HolidaySetting;
use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;

class EmployeeHolidayDaysCrudController extends CrudController
{
    public function setup()
    {
        $this->crud->setModel('App\Models\EmployeeHolidayDays');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/employee_holiday_days');
        $this->crud->setEntityNameStrings(trans('fields.employee_holiday_days'), trans('fields.employee_holiday_days'));
        $this->crud->addButtonFromModelFunction('line', 'detail', 'show_detail');
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->removeButton('delete');

        $this->crud->addButtonFromView('line', 'delete-mandatory', 'delete-mandatory', 'beginning');
        if(!request('year')){
            $this->crud->addClause('where', 'year', 'LIKE', "2019");
        }

        // filter
        $this->crud->addFilter([ // simple filter
            'type' => 'year',
            'name' => 'year',
            'label'=> 'Year'
        ],
            [
                '2018' => '2018',
                '2019' => '2019',
            ],
            function($value) { // if the filter is active
                if($value == 'All'){
                    $this->crud->addClause('where', 'year', 'LIKE', "%%");
                }else{
                    $this->crud->addClause('where', 'year', 'LIKE', "$value");
                }

            } );
        $this->crud->allowAccess('show');
//        $this->crud->enableDetailsRow();

        $this->crud->setColumns([
            [
                'name' => 'year',
                'label' => trans('fields.year'),
                'type' => 'text',
                'orderable' => false,
            ],
            [
                'name' => 'spend_holidays',
                'type' => 'number',
                'label' => trans('fields.spent_holidays_of_year'),
                'orderable' => false,
            ],
            [
                'type' => 'closure',
                'label' => trans('fields.number_holidays'),
                'function' => function($item) {
                    $holiday_days = $item->user->holiday_days;
                    return $holiday_days;
                }
            ]
        ]);
        $this->crud->customOrderBy(['name']);
        if (backpack_user()->role == User::ROLE_COMPANY) {
//            $this->crud->query = $this->crud->query->leftJoin('users', 'employee_holiday_days.user_id', '=', 'users.id')
//                ->orderBy('users.name', 'ASC');

            $this->crud->addColumn([
                'label' => trans('fields.employee'),
                'type' => "select",
                'name' => 'user_id', // the column that contains the ID of that connected entity;
                'entity' => 'user', // the method that defines the relationship in your Model
                'attribute' => "name", // foreign key attribute that is shown to user
                'sort' => 'asc',
                'orderable' => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    $query->leftJoin('users', 'users.id', '=', 'employee_holiday_days.user_id')
                        ->orderBy('users.name', $columnDirection)->select('employee_holiday_days.*');
                }
            ])->beforeColumn('year');
        }

        $this->crud->addFields([
            [
            'name' => 'holidays',
            'label' => trans('fields.number_holidays'),
            'type'  => 'number'
            ],
            [
                'name' => 'spend_holidays',
                'type' => 'number',
                'label' => trans('fields.spent_holidays_of_year'),
            ],
        ]);

        if (backpack_user()->role == User::ROLE_EMPLOYEE) {
            $this->crud->denyAccess(['delete']);
            $this->crud->removeAllButtonsFromStack('line');
        }
        $this->crud->denyAccess(['delete']);
//        $this->crud->denyAccess(['update']);
        $this->crud->denyAccess(['create']);

    }

    public function update(Request $request)
    {
        $employee_id = $request->id;
        $user_id = EmployeeHolidayDays::find($request->id)->user->id;
        $user_model_fqn = config('backpack.base.user_model_fqn');

        $user = $user_model_fqn::find($user_id);

        $user->holiday_days = $request->holidays;

        $user->save();

        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
