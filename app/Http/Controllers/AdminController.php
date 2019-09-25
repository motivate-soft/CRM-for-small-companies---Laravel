<?php

namespace App\Http\Controllers;
use App\Models\Action;
use App\Models\Department;
use App\Models\EmployeeEvent;
use App\Models\EventType;
use App\Models\Expense;
use App\Models\MedicalDay;
use App\Models\OverTime;
use App\Models\Workcenter;
use App\Models\WorkingPlaceHoliday;
use App\User;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EventMandatoryType;
use App\Models\Absence;
use App\Models\Incident;
use App\Models\Holiday;
use Carbon\Carbon;

class AdminController extends Controller
{
    protected $data = []; // the information we send to the view

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $this->data['title'] = trans('backpack::base.dashboard'); // set the page title
        if (backpack_user()->role == User::ROLE_ADMIN) {
            // total_companies,
            $total_companies = Company::count();
            $this->data['total_companies'] = $total_companies;
            // new companies
            $date = Carbon::now();
            $date->subDays(30); // 30 days ago
            $new_companies = User::where('role', User::ROLE_COMPANY)->whereDate('created_at', '>', $date->toDateTimeString())->get();
            $this->data['new_companies'] = $new_companies;
            // recent companies used service
            $date = Carbon::now();
            $date->subDays(3); // 3 days ago
            $recent_used_companies = User::where('role', User::ROLE_COMPANY)->whereDate('last_login_at', '>', $date->toDateTimeString())->count();
            $this->data['recent_used_companies'] = $recent_used_companies;
            // recent employees used service
            $date = Carbon::now();
            $date->subDays(3); // 3 days ago
            $recent_used_employees = User::where('role', User::ROLE_EMPLOYEE)->whereDate('last_login_at', '>', $date->toDateTimeString())->count();
            $this->data['recent_used_employees'] = $recent_used_employees;
            // companies
            $companies = User::where('role', User::ROLE_COMPANY)->get();
            $this->data['companies'] = $companies;
            return view('backpack::dashboard', $this->data);
        }elseif (backpack_user()->role == User::ROLE_COMPANY){
            $company_id = backpack_user()->company->id;
            $employees_on_vacation = $this->getEmployeesOnVacation();
            $employees_on_holiday = $this->getEmployeesOnHoliday();

            $last_employees = $this->getLastPeopleWorking();
            if(count($this->getNotworkingEmployee())>10){
                $not_working_employees =  array_slice($this->getNotworkingEmployee(), 0,10);
            }else{
                $not_working_employees =  $this->getNotworkingEmployee();
            }

            $data['last_employees'] = $last_employees;
            $data['not_working_employees'] = $not_working_employees;
            $data['employees_on_holiday'] = $employees_on_vacation + $employees_on_holiday;

            // calendar part
            $holidays = $this->getCompanyHolidayCalendar($company_id);
            $events = $this->getCompanyEventCalendar($company_id);
            $data['language'] = Company::find($company_id)->language->abbr;
            $data['holidays'] = $holidays;
            $data['events'] = $events;

            $employees_week_calendar = $this->getCompanyEmployeeCalendar();

            return view('backpack::dashboard', $employees_week_calendar, $data);
        }elseif (backpack_user()->role == User::ROLE_EMPLOYEE){
            $this->data['role'] = 'EMPLOYEE';
            $employee_id = backpack_user()->employee->id;

            $week_calendar = $this->getEmployeeCalendar($employee_id);

            $this->data['lastCommunication'] = $this->getLastCommunication($employee_id);
            $this->data['index'] = 1;
            return view('backpack::dashboard',$week_calendar, $this->data);
        }
    }
    // get calendar of holiday in company
    public function getCompanyHolidayCalendar($id){
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $sunday = Carbon::now()->startOfWeek()->toDateString();
        $saturday = Carbon::now()->endOfWeek()->toDateString();

        //get workcenter holidays
        $workcenters = Company::find($id)->workcenters;
        $workcenter_holidays = collect();
        $workcenter_holidays = $workcenter_holidays->merge($workcenters->map(function ($workcenter) use ($sunday, $saturday){
            return $workcenter->holidays->where('end_date', '>=', $sunday)->where('start_date', '<=', $saturday);
        }));
        $workcenter_holidays_collection = collect();
        foreach ($workcenter_holidays as $item) {
            $workcenter_holidays_collection = $workcenter_holidays_collection->merge($item);
        }
        //get department holidays
        $departments = Company::find($id)->departments;
        $department_holidays = collect();
        $department_holidays = $department_holidays->merge($departments->map(function ($department) use ($sunday, $saturday) {
            return $department->holidays->where('end_date', '>=', $sunday)->where('start_date', '<=', $saturday);
        }));
        $department_holidays_collection = collect();
        foreach ($department_holidays as $item) {
            $department_holidays_collection = $department_holidays_collection->merge($item);
        }
        //merge workcenter holidays and epartment holidays
        $department_holidays_collection = $department_holidays_collection->merge($workcenter_holidays_collection);
        return $department_holidays_collection->unique('name');
    }
    // get calendar of event in company
    public function getCompanyEventCalendar($id){
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $sunday = Carbon::now()->startOfWeek()->toDateString();
        $saturday = Carbon::now()->endOfWeek()->toDateString();

        //get workcenter events
        $workcenters = Company::find($id)->workcenters;
        $workcenter_events = collect();
        $workcenter_events = $workcenter_events->merge($workcenters->map(function ($workcenter) use ($sunday, $saturday){
            return $workcenter->events->where('end_date', '>=', $sunday)->where('start_date', '<=', $saturday);
        }));
        $workcenter_events_collection = collect();
        foreach ($workcenter_events as $item) {
            $workcenter_events_collection = $workcenter_events_collection->merge($item);
        }
        //get department events
        $departments = Company::find($id)->departments;
        $department_events = collect();
        $department_events = $department_events->merge($departments->map(function ($department) use ($sunday, $saturday) {
            return $department->events->where('end_date', '>=', $sunday)->where('start_date', '<=', $saturday);
        }));
        $department_events_collection = collect();
        foreach ($department_events as $item) {
            $department_events_collection = $department_events_collection->merge($item);
        }
        //merge workcenter holidays and epartment holidays
        $department_events_collection = $department_events_collection->merge($workcenter_events_collection);
        return $department_events_collection->unique('name');
    }
    // get calendar of all employees in company
    public function getCompanyEmployeeCalendar(){

        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $sunday = Carbon::now()->startOfWeek()->toDateString();
        $saturday = Carbon::now()->endOfWeek()->toDateString();

        $holiday_type = EventMandatoryType::where('type', 'holiday')->first();
        $absence_type = EventMandatoryType::where('type', 'absence')->first();
        $expense_type = EventMandatoryType::where('type', 'expense')->first();
        $medical_type = EventMandatoryType::where('type', 'medical_day')->first();

        // !!!
        if ($holiday_type && $holiday_type->has_confirmation) {
            $employee_holidays = Holiday::where('status', 'approved')
                ->where('cancel_state', '!=', 'approved')
                ->where('end_date', '>=', $sunday)
                ->where('start_date', '<=', $saturday)
                ->get();
        } else {
            $employee_holidays = Holiday::where('end_date', '>=', $sunday)
                ->where('start_date', '<=', $saturday)
                ->get();
        }

        if ($absence_type && $absence_type->has_confirmation) {
            $employee_absence = Absence::where('status', 'approved')
                ->where('end_date', '>=', $sunday)
                ->where('start_date', '<=', $saturday)
                ->get();
        } else {
            $employee_absence = Absence::where('end_date', '>=', $sunday)
                ->where('start_date', '<=', $saturday)
                ->get();
        }

        if ($expense_type && $expense_type->has_confirmation) {
            $employee_expense = Expense::where('status', 'approved')
                ->where('date', '>=', $sunday)
                ->where('date', '<=', $saturday)
                ->get();
        } else {
            $employee_expense = Expense::where('date', '>=', $sunday)
                ->where('date', '<=', $saturday)
                ->get();
        }

        if ($medical_type && $medical_type->has_confirmation) {
            $employee_medical = MedicalDay::where('status', 'approved')
                ->where('date', '>=', $sunday)
                ->where('date', '<=', $saturday)
                ->get();
        } else {
            $employee_medical = MedicalDay::where('date', '>=', $sunday)
                ->where('date', '<=', $saturday)
                ->get();
        }


        $employee_others = EmployeeEvent::where('status', 'approved')
            ->where('end_date', '>=', $sunday)
            ->where('start_date', '<=', $saturday)
            ->get();

        $data['title'] = trans('fields.calendar');

        /* Employee Event */
        $data['employee_holidays'] = $employee_holidays;
        $data['employee_absense'] = $employee_absence;
        $data['employee_medical'] = $employee_medical;
        $data['employee_expense'] = $employee_expense;
        $data['employee_others'] = $employee_others;

        $data['event_mandatory_types'] = EventMandatoryType::all();

        $data['event_types'] = EventType::all();

        return $data;
    }
    // get calendar in employee
    public function getEmployeeCalendar($id)
    {
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $sunday = Carbon::now()->startOfWeek()->toDateString();
        $saturday = Carbon::now()->endOfWeek()->toDateString();

        $workcenter = Employee::find($id)->workcenter;
        $departments = Employee::find($id)->department;

        $array_holidays = collect();
        $array_events = collect();

        if ($workcenter) {
            $array_holidays = $workcenter->holidays->filter(function($array_holiday) use ($sunday, $saturday){
                return $array_holiday->end_date >= $sunday && $array_holiday->start_date <= $saturday;
            });
            $array_events = $workcenter->events->filter(function($array_event) use ($sunday, $saturday){
                return $array_event->end_date >= $sunday && $array_event->start_date <= $saturday;
            });
        }
//        return $array_holidays;
        if ($departments) {

            $d_holidays = $departments->holidays->filter(function($d_holiday) use ($sunday, $saturday){
                return $d_holiday->end_date >= $sunday && $d_holiday->start_date <= $saturday;
            });

            $array_holidays = $array_holidays->merge($d_holidays);
//            return $d_holidays;
            $d_events = $departments->events->filter(function($d_event) use ($sunday, $saturday){
                return $d_event->end_date >= $sunday && $d_event->start_date <= $saturday;
            });
            $array_events = $array_events->merge($d_events);
        }

        $holiday_type = EventMandatoryType::where('type', 'holiday')->where('company_id', backpack_user()->employee->company->id)->first();
        $absence_type = EventMandatoryType::where('type', 'absence')->where('company_id', backpack_user()->employee->company->id)->first();
        $expense_type = EventMandatoryType::where('type', 'expense')->where('company_id', backpack_user()->employee->company->id)->first();
        $medical_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', backpack_user()->employee->company->id)->first();

        // !!!
        if ($holiday_type && $holiday_type->has_confirmation) {
            $employee_holidays = Holiday::where('status', 'approved')
                                        ->where('cancel_state', '!=', 'approved')
                                        ->where('end_date', '>=', $sunday)
                                        ->where('start_date', '<=', $saturday)
                                        ->get();
        } else {
            $employee_holidays = Holiday::where('end_date', '>=', $sunday)
                                        ->where('start_date', '<=', $saturday)
                                        ->get();
        }

        if ($absence_type && $absence_type->has_confirmation) {
            $employee_absence = Absence::where('status', 'approved')
                                        ->where('end_date', '>=', $sunday)
                                        ->where('start_date', '<=', $saturday)
                                        ->get();
        } else {
            $employee_absence = Absence::where('end_date', '>=', $sunday)
                                        ->where('start_date', '<=', $saturday)
                                        ->get();
        }

        if ($expense_type && $expense_type->has_confirmation) {
            $employee_expense = Expense::where('status', 'approved')
                                        ->where('date', '>=', $sunday)
                                        ->where('date', '<=', $saturday)
                                        ->get();
        } else {
            $employee_expense = Expense::where('date', '>=', $sunday)
                                        ->where('date', '<=', $saturday)
                                        ->get();
        }

        if ($medical_type && $medical_type->has_confirmation) {
            $employee_medical = MedicalDay::where('status', 'approved')
                                        ->where('date', '>=', $sunday)
                                        ->where('date', '<=', $saturday)
                                        ->get();
        } else {
            $employee_medical = MedicalDay::where('date', '>=', $sunday)
                                        ->where('date', '<=', $saturday)
                                        ->get();
        }


        $employee_others = EmployeeEvent::where('employee_id', $id)->where('status', 'approved')
                                        ->where('end_date', '>=', $sunday)
                                        ->where('start_date', '<=', $saturday)
                                        ->get();

        $data['title'] = trans('fields.calendar');

        /* Calendar Event */
        $data['holidays'] = $array_holidays;
        $data['events'] = $array_events;

        /* Employee Event */
        $data['employee_holidays'] = $employee_holidays;
        $data['employee_absense'] = $employee_absence;
        $data['employee_medical'] = $employee_medical;
        $data['employee_expense'] = $employee_expense;
        $data['employee_others'] = $employee_others;

        $data['event_mandatory_types'] = EventMandatoryType::all();

//        return $data['event_mandatory_types'];
        $data['event_types'] = EventType::all();
        $data['language'] = Employee::find($id)->company->language->abbr;

        return $data;
    }

    public function getLastCommunication($employee_id){

        $company_id = Employee::find($employee_id)->company->id;

        $holiday_type = EventMandatoryType::where('type', 'holiday')->where('company_id', $company_id)->first();
        $absence_type = EventMandatoryType::where('type', 'absence')->where('company_id', $company_id)->first();
        $expense_type = EventMandatoryType::where('type', 'expense')->where('company_id', $company_id)->first();
        $medical_type = EventMandatoryType::where('type', 'medical_day')->where('company_id', $company_id)->first();
        $incident_type = EventMandatoryType::where('type', 'incident')->where('company_id', $company_id)->first();



        $holidays = Holiday::where('employee_id', $employee_id)->get();
        $absence = Absence::where('employee_id', $employee_id)->get();
        $expense = Expense::where('employee_id', $employee_id)->get();
        $medical = MedicalDay::where('employee_id', $employee_id)->get();
        $incident = Incident::where('employee_id', $employee_id)->get();


//        return date('d/m/Y', strtotime($holidays[0]->start_date));
        $collection = collect();
        $collection = $collection->merge($holidays->map(function ($item) use ($holiday_type){
            $item['type'] = 'holiday';
            unset($item['event_type_id']);


//            $item['end_date'] = date('Y-m-d',strtotime($item['end_date']));
//            $item['datetime'] = date('Y-m-d',strtotime($item['created_at']));
//            $item['from_date'] = date('Y-m-d',strtotime($item['start_date']));

            if ($holiday_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
                }
            } else {
                unset($item['status']);
                unset($item['reject_message']);
            }

            if (!$holiday_type->has_file) {
                unset($item['doc']);
            }
            if (!$holiday_type->has_comment) {
                unset($item['comment']);
            }
            if (!$holiday_type->has_amount) {
                unset($item['amount']);
            }
            return $item;
        }));

        $collection = $collection->merge($absence->map(function ($item) use ($absence_type){
            $item['type'] = 'absence';
//            $item['from_date'] = date('d/m/Y',strtotime($item->start_date));
//            $item['end_date'] = date('d/m/Y',strtotime($item->end_date));
//            $item['datetime'] = date('d/m/Y h:i:s',strtotime($item->created_at));
            unset($item['event_type_id']);

            if ($absence_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
                }
            } else {
                unset($item['status']);
                unset($item['reject_message']);
            }

            if (!$absence_type->has_file) {
                unset($item['doc']);
            }
            if (!$absence_type->has_comment) {
                unset($item['comment']);
            }
            if (!$absence_type->has_amount) {
                unset($item['amount']);
            }

            return $item;
        }));

        $collection = $collection->merge($expense->map(function ($item) use ($expense_type){
            $item['type'] = 'expense';
//            $item['date'] = date('d/m/Y',strtotime($item->date));
//            $item['datetime'] = date('d/m/Y h:i:s',strtotime($item->created_at));
            unset($item['event_type_id']);

            if ($expense_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
                }
            } else {
                unset($item['status']);
                unset($item['reject_message']);
            }

            if (!$expense_type->has_file) {
                unset($item['photo']);
            }
            if (!$expense_type->has_comment) {
                unset($item['comment']);
            }
            if (!$expense_type->has_amount) {
                unset($item['amount']);
            }

            return $item;
        }));

        $collection = $collection->merge($medical->map(function ($item) use ($medical_type){
            $item['type'] = 'leave';
//            $item['type'] = 'expense';
//            $item['date'] = date('d/m/Y',strtotime($item->date));
//            $item['datetime'] = date('d/m/Y h:i:s',strtotime($item->created_at));
            unset($item['event_type_id']);

            if ($medical_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
                }
            } else {
                unset($item['status']);
                unset($item['reject_message']);
            }

            if (!$medical_type->has_file) {
                unset($item['photo']);
            }
            if (!$medical_type->has_comment) {
                unset($item['comment']);
            }
            if (!$medical_type->has_amount) {
                unset($item['amount']);
            }
            return $item;
        }));

        $collection = $collection->merge($incident->map(function ($item) use ($incident_type){
            $item['type'] = 'incident';
//            $item['datetime'] = date('d/m/Y h:i:s',strtotime($item->created_at));
            unset($item['event_type_id']);

            if ($incident_type->has_confirmation) {
                if ($item['status'] != 'rejected') {
                    unset($item['reject_message']);
                }
            } else {
                unset($item['status']);
                unset($item['reject_message']);
            }

            if (!$incident_type->has_file) {
                unset($item['photo']);
            }
            if (!$incident_type->has_comment) {
                unset($item['comment']);
            }
            if (!$incident_type->has_amount) {
                unset($item['amount']);
            }

            return $item;
        }));

        return $collection->where('created_at', '>=', Carbon::now()->subDays(30))->sortByDesc('created_at');
    }

    // get last people IN - Working
    public function getLastPeopleWorking(){
        $last_employees = [];
        $current_day = Carbon::now()->format('Y-m-d');
        $last_employee_ids = Action::whereDate('datetime', $current_day)->whereHas('option', function ($query){
            $query->where('type', 'in');
        })->get()->pluck('employee_id');
        $index = 0;
        foreach ($last_employee_ids as $employee_id){
            $index++;
            if($index>10) break;
            $employee_name = Employee::find($employee_id)->user->name;
            $employee_photo = Employee::find($employee_id)->photo;
            $last_check = Action::where('employee_id', $employee_id)->orderBy('datetime', 'desc')->whereHas('option', function ($query){
                $query->where('type', 'in');
            })->first()->datetime;
            $employee = ['name'=>$employee_name, 'photo'=>$employee_photo, 'last_check'=>$last_check];
            array_push($last_employees, $employee);
        }
        return $last_employees;
    }
    // get not-working employees
    public function getNotworkingEmployee(){
        $current_date = Carbon::today();
        $current_day = Carbon::now()->format('Y-m-d');
        $employees = backpack_user()->company->employees;
        $notWorkingEmployees = [];
        $employee_ids = Employee::all()->pluck('id');
        foreach($employee_ids as $employee_id){
            // if employee is on vacation, skip
            if(Holiday::where('employee_id', $employee_id)->where('status', 'approved')->where('start_date', '<=', $current_date)->where('end_date', '>=', $current_date)->first()){
                continue;
            }

            // if employee is on company holiday, skip
            if(Employee::find($employee_id)->department){
                $department_holidays = Employee::find($employee_id)->department->holidays;
                $today_department_holidays = $department_holidays->where('start_date', '<=', $current_date)
                    ->where('end_date', '>=', $current_date);

                if($today_department_holidays->count() != 0){
                    continue;
                }
            }
            if(Employee::find($employee_id)->workcenter){
                $workcenter_holidays = Employee::find($employee_id)->workcenter->holidays;
                $today_workcenter_holidays = $workcenter_holidays->where('start_date', '<=', $current_date)
                    ->where('end_date', '>=', $current_date);
                if($today_workcenter_holidays->count() != 0){
                    continue;
                }
            }
            // if employee is on working day, skip
            $working_days = Employee::find($employee_id)->user->workingDays->pluck('id')->toarray();
            $week_day = date('N',strtotime($current_date));
            if(!in_array($week_day, $working_days)){
                continue;
            }
            $count = Action::where('employee_id', $employee_id)->whereDate('datetime', $current_day)->whereHas('option', function ($query){
                $query->where('type', 'in');
            })->get()->count();
            if($count != 0) continue;
            $employee_name = Employee::find($employee_id)->user->name;
            $employee_photo = Employee::find($employee_id)->photo;
            $last_login_at = Employee::find($employee_id)->user->last_login_at;
            $last_check_in = '';
            $last_check_out = '';
            if($last_check_in = Action::where('employee_id', $employee_id)->orderBy('datetime', 'desc')->whereHas('option', function ($query){
                $query->where('type', 'in');
            })->first()){
                $last_check_in = Action::where('employee_id', $employee_id)->orderBy('datetime', 'desc')->whereHas('option', function ($query){
                    $query->where('type', 'in');
                })->first()->datetime;
            }
            if($last_check_out = Action::where('employee_id', $employee_id)->orderBy('datetime', 'desc')->whereHas('option', function ($query){
                $query->where('type', 'out');
            })->first()){
                $last_check_out = Action::where('employee_id', $employee_id)->orderBy('datetime', 'desc')->whereHas('option', function ($query){
                    $query->where('type', 'out');
                })->first()->datetime;
            }

            $employee = ['name'=>$employee_name,
                'photo'=>$employee_photo,
                'last_login_at'=>$last_login_at,
                'last_check_in' => $last_check_in,
                'last_check_out' => $last_check_out,
            ];
            array_push($notWorkingEmployees, $employee);
        }
        return $notWorkingEmployees;
    }
    // get employees on vacation
    public function getEmployeesOnVacation(){
        $current_date = Carbon::today();
        $employee_ids = Holiday::where('status', 'approved')
            ->where('start_date', '<=', $current_date)
            ->where('end_date', '>=', $current_date)
            ->pluck('employee_id');

        $employees = Employee::whereIn('id', $employee_ids)->get();
        $employees_on_vacation = [];
        foreach($employees as $employee){
            array_push($employees_on_vacation, $employee->user);
        }
        return $employees_on_vacation;
    }

    // get employees on department & workcenter holiday
    public function getEmployeesOnHoliday(){
        $current_date = Carbon::now()->format('Y-m-d');
        $employees_on_holiday = [];
//        if($employees = backpack_user()->company->employees){
            $employees = backpack_user()->company->employees;
//            return $employees;
            foreach($employees as $employee){
                if($employee->department){
                    if(!$employee->workcenter){
                        break;
                    }
                    if(!$employee->department){
                        break;
                    }
                    $department_holidays = $employee->department->holidays;
                    $workcenter_holidays = $employee->workcenter->holidays;

                    $today_department_holidays = $department_holidays->where('start_date', '<=', $current_date)
                        ->where('end_date', '>=', $current_date);
                    $today_workcenter_holidays = $workcenter_holidays->where('start_date', '<=', $current_date)
                        ->where('end_date', '>=', $current_date);
                    if($today_department_holidays->count() != 0 or $today_workcenter_holidays->count() !=0){
                        array_push($employees_on_holiday, $employee->user);
                    }
                }
            }
//        }
        return $employees_on_holiday;
    }

    /**
     * Redirect to the dashboard.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        // The '/admin' route is not to be used as a page, because it breaks the menu's active state.
        return redirect(backpack_url('dashboard'));
    }
}
