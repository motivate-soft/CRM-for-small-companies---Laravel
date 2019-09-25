<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Department;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected $data = [];

    protected $filters = [];

    public function __construct(Request $request)
    {
        $this->middleware(backpack_middleware());

        $this->setFilters($request);
    }

    public function setFilters(Request $request)
    {
        $this->filters = [
            'employee' => $request->get('employee'),
            'workcenter' => $request->get('workcenter'),
            'schedule' => $request->get('schedule'),
            'date_range' => $request->get('date_range', \Carbon\Carbon::now()->subMonth()->format('m/d/Y') . ' - ' . \Carbon\Carbon::now()->format('m/d/Y')),
            'date' => $request->get('date', \Carbon\Carbon::now()->format('m/Y')),
            'year' => $request->get('year', \Carbon\Carbon::now()->format('Y')),
        ];
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function getGeneralList()
    {
        $filters = $this->getFilters();

        $collection = Action::workcenter($filters['workcenter'])
            ->employee($filters['employee'])
            ->dateRange($filters['date_range'])
            ->get();

        $total_time = Action::getTotalTimeByEmployees($collection);

        $table = $collection->groupBy([
            'employee_id',
            function($item) {
                return Carbon::parse($item->datetime)->format('m/d/Y');
            }
        ]);

        $this->data = [
            'title' => trans('fields.general_listing'),
            'filters' => $filters,
            'total_time' => $total_time,
            'table' => $table,
        ];

        return view('reports.general_listing', $this->data);
    }

    public function getHoursByEmployee()
    {
        $filters = $this->getFilters();

        $collection = Action::workcenter($filters['workcenter'])
            ->employee($filters['employee'])
            ->dateRange($filters['date_range'])
            ->get();

        $total_time = Action::getTotalTimeByEmployees($collection);

        $table = $collection->groupBy([
            'employee_id',
        ]);

        $this->data = [
            'title' => trans('fields.hours_by_employee'),
            'filters' => $filters,
            'total_time' => $total_time,
            'table' => $table,
        ];

        return view('reports.hours_by_employee', $this->data);
    }

    public function getMonthlyDecimalHours()
    {
        $filters = $this->getFilters();

        $collection = Action::workcenter($filters['workcenter'])
            ->employee($filters['employee'])
            ->date($filters['date'])
            ->get();

        $total_time = Action::getTotalTimeByEmployees($collection);

        $table = $collection->groupBy([
            'employee_id',
            function($item) {
                return Carbon::parse($item->datetime)->format('j');
            }
        ]);

        $this->data = [
            'title' => trans('fields.monthly_decimal_hours'),
            'filters' => $filters,
            'total_time' => $total_time,
            'table' => $table,
        ];

        return view('reports.monthly_decimal_hours', $this->data);
    }

    public function getMonthlySummary()
    {
        $filters = $this->getFilters();

        $collection = Action::workcenter($filters['workcenter'])
            ->employee($filters['employee'])
            ->year($filters['year'])
            ->get();

        $total_time = Action::getTotalTimeByEmployees($collection);

        $table = $collection->groupBy([
            'employee_id',
            function($item) {
                return Carbon::parse($item->datetime)->format('F');
            }
        ]);

        $this->data = [
            'title' => trans('fields.monthly_summary'),
            'filters' => $filters,
            'total_time' => $total_time,
            'table' => $table,
        ];

        return view('reports.monthly_summary', $this->data);
    }

    public function getDetailedHours()
    {
        $filters = $this->getFilters();

        $schedule = Schedule::find($filters['schedule']) ?? Schedule::first() ?? null;

        $filters['schedule'] = $schedule ? $schedule->id : null;

        $collection = Action::schedule($filters['schedule'])
            ->employee($filters['employee'])
            ->date($filters['date'])
            ->get();

        $total_time = Action::getTotalTimeByEmployees($collection);

        $table = $collection->groupBy([
            'employee_id',
            function($item) {
                return Carbon::parse($item->datetime)->format('j');
            }
        ]);

        $this->data = [
            'title' => trans('fields.extra_hours'),
            'filters' => $filters,
            'total_time' => $total_time,
            'table' => $table,
            'schedule' => $schedule,
            'working_times' => $schedule ? $schedule->working_times : null
        ];

        return view('reports.detailed_hours', $this->data);
    }

    public function getHoursByDepartment()
    {
        $filters = $this->getFilters();

        $collection = Action::workcenter($filters['workcenter'])
            ->employee($filters['employee'])
            ->dateRange($filters['date_range'])
            ->get();

        $total_time = Action::getTotalTimeByEmployees($collection);

        $table = [];
        Department::all()->each(function ($department) use ($collection, &$table) {
            $table[$department->id] = Action::join('department_has_employees', 'department_has_employees.employee_id', '=', 'actions.employee_id')
                ->select('actions.*')
                ->where('department_has_employees.department_id', $department->id)
                ->whereIn('actions.id', $collection->pluck('id')->all())
                ->get();
        });

        foreach ($table as $key => &$item) {
            if($item->count()) {
                $item = Action::getTotalTimeByEmployees(collect($item));
            } else {
                unset($table[$key]);
            }
        }

        $this->data = [
            'title' => trans('fields.hours_by_department'),
            'filters' => $filters,
            'total_time' => $total_time,
            'table' => $table,
        ];

        return view('reports.hours_by_department', $this->data);
    }

    public function getHoursByDepartmentEmployee()
    {
        $filters = $this->getFilters();

        $collection = Action::workcenter($filters['workcenter'])
            ->employee($filters['employee'])
            ->dateRange($filters['date_range'])
            ->get();

        $total_time = Action::getTotalTimeByEmployees($collection);

        $table = [];
        Department::all()->each(function ($department) use ($collection, &$table) {
            $table[$department->id] = Action::join('department_has_employees', 'department_has_employees.employee_id', '=', 'actions.employee_id')
                ->select('actions.*')
                ->where('department_has_employees.department_id', $department->id)
                ->whereIn('actions.id', $collection->pluck('id')->all())
                ->get()->groupBy([
                    'employee_id',
                ]);
        });

        $this->data = [
            'title' => trans('fields.hours_by_department_employee'),
            'filters' => $filters,
            'total_time' => $total_time,
            'table' => $table,
        ];

        return view('reports.hours_by_department_employee', $this->data);
    }

    public function getPunctuality()
    {
        $filters = $this->getFilters();

        $schedule = Schedule::find($filters['schedule']) ?? Schedule::first() ?? null;

        $filters['schedule'] = $schedule ? $schedule->id : null;

        $collection = Action::schedule($filters['schedule'])
            ->employee($filters['employee'])
            ->date($filters['date'])
            ->get();

        $table = $collection->groupBy([
            'employee_id',
            function($item) {
                return Carbon::parse($item->datetime)->format('j');
            }
        ]);

        $this->data = [
            'title' => trans('fields.punctuality'),
            'filters' => $filters,
            'total_time' => false,
            'table' => $table,
        ];

        return view('reports.punctuality', $this->data);
    }
}
