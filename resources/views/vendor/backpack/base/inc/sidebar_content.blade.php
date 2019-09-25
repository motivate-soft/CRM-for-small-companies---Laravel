<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li><a href="{{ backpack_url('dashboard') }}"><i class="fa fa-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>

@role('admin')
    <li><a href="{{ backpack_url('financial_summary') }}"><i class="fa fa-credit-card"></i> {{ trans('fields.financial_summary') }}</a></li>

    <li><a href="{{ backpack_url('companies') }}"><i class="fa fa-black-tie"></i> <span>{{ trans('fields.companies') }}</span></a></li>
    <li><a href="{{ backpack_url('countries') }}"><i class="fa fa-black-tie"></i> <span>{{ trans('fields.countries') }}</span></a></li>
    <li><a href="{{ backpack_url('reject_list') }}"><i class="fa fa-black-tie"></i> <span>{{ trans('fields.reject_list') }}</span></a></li>
    <li><a href="{{ backpack_url('trial_mode') }}"><i class="fa fa-black-tie"></i> <span>{{ trans('fields.trial_mode') }}</span></a></li>

    <li><a href="{{ backpack_url('currencies') }}"><i class="fa fa-money"></i> {{ trans('fields.currencies') }}</a></li>
    <li><a href="{{ backpack_url('plans') }}"><i class="fa fa-tasks"></i> {{ trans('fields.plans') }}</a></li>
    <li><a href="{{ backpack_url('company_palns') }}"><i class="fa fa-tasks"></i> {{ trans('fields.company_plans') }}</a></li>

    <li class="treeview">
        <a href="#"><i class="fa fa-globe"></i> <span>{{ trans('fields.translations') }}</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
            <li><a href="{{ url(config('backpack.base.route_prefix', 'admin').'/language') }}"><i class="fa fa-flag-checkered"></i> {{ trans('fields.languages') }}</a></li>
            <li><a href="{{ url(config('backpack.base.route_prefix', 'admin').'/language/texts') }}"><i class="fa fa-language"></i> {{ trans('fields.site_texts') }}</a></li>
        </ul>
    </li>
@endrole

@role('company')
    @php
        $plans = backpack_user()->company->companyPlan;
    @endphp

    @if($plans && $plans->status != 'pending' && $plans->status != 'rejected')
    <li><a href="{{ backpack_url('workcenters') }}"><i class="fa fa-briefcase"></i> <span>{{ trans('fields.work_centers') }}</span>
        </a>
    </li>
    <li class="treeview">
        <a href="#"><i class="fa fa-list"></i> <span>{{ trans('fields.departments') }}</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
            <li><a href="{{ backpack_url('departments') }}"><i class="fa fa-building"></i> <span>{{ trans('fields.configuration') }}</span></a></li>
            <li><a href="{{ backpack_url('schedules') }}"><i class="fa fa-caret-square-o-right"></i> <span>{{ trans('fields.schedules') }}</span></a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#"><i class="fa fa-list"></i> <span>{{ trans('fields.calendar') }} {{ trans('fields.events') }}</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
            <li><a href="{{ backpack_url('workingplace_hollidays') }}"><i class="fa fa-plane"></i> <span>{{ trans('fields.holidays') }}</span></a></li>
            <li><a href="{{ backpack_url('calendar_event') }}"><i class="fa fa-users"></i> <span>{{ trans('fields.events') }}</span></a></li>
        </ul>
    </li>
    {{--<li class="treeview">--}}
        {{--<a href="#"><i class="fa fa-list"></i> <span>{{ trans('fields.devices') }}</span> <i class="fa fa-angle-left pull-right"></i></a>--}}
        {{--<ul class="treeview-menu">--}}

            {{--<li><a href="{{ backpack_url('notification') }}"><i class="fa fa-bell"></i> <span>{{ trans('fields.notification') }}</span></a></li>--}}
            {{--<li><a href="{{ backpack_url('devices') }}"><i class="fa fa-caret-square-o-right"></i> <span>{{ trans('fields.configuration') }}</span></a></li>--}}
            {{--<li><a href="{{ backpack_url('devices/schedules') }}"><i class="fa fa-calendar-times-o"></i> <span>{{ trans('fields.schedules') }}</span></a></li>--}}
            {{--<li><a href="{{ backpack_url('devices/logs') }}"><i class="fa fa-sticky-note-o"></i> <span>{{ trans('fields.logs') }}</span></a></li>--}}
        {{--</ul>--}}
    {{--</li>--}}

    <li class="treeview">
        <a href="#"><i class="fa fa-list"></i> <span>{{ trans('fields.in_out_actions') }}</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
            <li><a href="{{ backpack_url('actions-options') }}"><i class="fa fa-square-o"></i> <span>{{ trans('fields.action_options') }}</span></a></li>
            <li><a href="{{ backpack_url('actions') }}"><i class="fa fa-clock-o"></i> <span>{{ trans('fields.actions') }}</span></a></li>
            <li><a href="{{ backpack_url('events') }}"><i class="fa fa-clock-o"></i> <span>{{ trans('fields.events') }}</span></a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#"><i class="fa fa-list"></i> <span>{{ trans('fields.employees') }}</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li><a href="{{ backpack_url('employees') }}"><i class="fa fa-users"></i> <span>{{ trans('fields.management') }}</span></a></li>
            <li><a href="{{ backpack_url('employee_holiday_days') }}"><i class="fa fa-calendar-check-o"></i> <span>{{ trans('fields.employee_holiday_days') }}</span></a></li>
            <li class="treeview">
                <a href="#"><i class="fa fa-table"></i> <span>{{ trans('fields.events') }}</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li>
                        <a href="{{ backpack_url('holidays') }}"><i class="fa fa-plane"></i> <span>{{ trans('fields.holidays') }}</span>
                            @php
                                $employees = backpack_user()->company->employees;
                                $not_read_vacation_count = 0;
                                foreach($employees as $employee){
                                    $employee_id = $employee->id;
                                    $not_read_vacation_count = $not_read_vacation_count + App\Models\Holiday::where('employee_id', $employee_id)->where('company_is_read', 0)->count();
                                }
                            @endphp

                            <span class="pull-right-container vacation_notification_container">
                                @if($not_read_vacation_count != 0)
                                <span class="label pull-right bg-red vacation_notification">
                                    {{$not_read_vacation_count}}
                                </span>
                                @endif
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ backpack_url('expenses') }}"><i class="fa fa-user-times"></i> <span>{{ trans('fields.expense') }}</span>
                            @php
                                $employees = backpack_user()->company->employees;
                                $not_read_expense_count = 0;
                                foreach($employees as $employee){
                                    $employee_id = $employee->id;
                                    $not_read_expense_count = $not_read_expense_count + App\Models\Expense::where('employee_id', $employee_id)->where('company_is_read', 0)->count();
                                }
                            @endphp
                            <span class="pull-right-container expense_notification_container">
                                @if($not_read_expense_count != 0)
                                    <span class="label pull-right bg-blue expense_notification">
                                    {{$not_read_expense_count}}
                                </span>
                                @endif
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ backpack_url('medical_day') }}"><i class="fa fa-plus-square"></i> <span>{{ trans('fields.medical_day') }}</span>
                            @php
                                $employees = backpack_user()->company->employees;
                                $not_read_medical_count = 0;
                                foreach($employees as $employee){
                                    $employee_id = $employee->id;
                                    $not_read_medical_count = $not_read_medical_count + App\Models\MedicalDay::where('employee_id', $employee_id)->where('company_is_read', 0)->count();
                                }
                            @endphp
                            <span class="pull-right-container medical_notification_container">
                                @if($not_read_medical_count != 0)
                                    <span class="label pull-right bg-yellow medical_notification">
                                    {{$not_read_medical_count}}
                                </span>
                                @endif
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ backpack_url('absences') }}"><i class="fa fa-home"></i> <span>{{ trans('fields.absences') }}</span>
                            @php
                                $employees = backpack_user()->company->employees;
                                $not_read_absences_count = 0;
                                foreach($employees as $employee){
                                    $employee_id = $employee->id;
                                    $not_read_absences_count = $not_read_absences_count + App\Models\Absence::where('employee_id', $employee_id)->where('company_is_read', 0)->count();
                                }
                            @endphp
                            <span class="pull-right-container absences_notification_container">
                                @if($not_read_absences_count != 0)
                                    <span class="label pull-right bg-green absences_notification">
                                    {{$not_read_absences_count}}
                                </span>
                                @endif
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ backpack_url('incidents') }}"><i class="fa fa-cab"></i> <span>{{ trans('fields.incident') }}</span>
                            @php
                                $employees = backpack_user()->company->employees;
                                $not_read_incidents_count = 0;
                                foreach($employees as $employee){
                                    $employee_id = $employee->id;
                                    $not_read_incidents_count = $not_read_incidents_count + App\Models\Incident::where('employee_id', $employee_id)->where('company_is_read', 0)->count();
                                }
                            @endphp
                            <span class="pull-right-container incidents_notification_container">
                                @if($not_read_incidents_count != 0)
                                    <span class="label pull-right bg-purple incidents_notification">
                                    {{$not_read_incidents_count}}
                                </span>
                                @endif
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ backpack_url('event_management') }}"><i class="fa fa-list-alt"></i> <span>{{ trans('fields.other_events') }}</span>
                            @php
                                $employees = backpack_user()->company->employees;
                                $not_read_other_count = 0;
                                foreach($employees as $employee){
                                    $employee_id = $employee->id;
                                    $not_read_other_count = $not_read_other_count + App\Models\EmployeeEvent::where('employee_id', $employee_id)->where('company_is_read', 0)->count();
                                }
                            @endphp
                            <span class="pull-right-container other_notification_container">
                                @if($not_read_other_count != 0)
                                    <span class="label pull-right bg-orange other_notification">
                                    {{$not_read_other_count}}
                                </span>
                                @endif
                            </span>
                        </a>
                    </li>
                </ul>
            </li>

{{--            <li><a href="{{ backpack_url('event_mandatory_type') }}"><i class="fa fa-outdent"></i> <span>{{ trans('fields.event_mandatory_type') }}</span></a></li>--}}
            <li><a href="{{ backpack_url('event_type') }}"><i class="fa fa-outdent"></i> <span>{{ trans('fields.event_other_type') }}</span></a></li>
            <li><a href="{{ backpack_url('apps') }}"><i class="fa fa-mobile"></i> <span>{{ trans('fields.apps') }}</span></a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#"><i class="fa fa-list"></i> <span>{{ trans('fields.reports') }}</span> <i class="fa fa-angle-left pull-right"></i></a>
        <ul class="treeview-menu">
            <li><a href="{{ backpack_url('reports/general_listing') }}"><i class="fa fa-file-text"></i> <span>{{ trans('fields.general_listing') }}</span></a></li>
            <li><a href="{{ backpack_url('reports/hours_by_employee') }}"><i class="fa fa-user"></i> <span>{{ trans('fields.hours_by_employee') }}</span></a></li>
            <li><a href="{{ backpack_url('reports/monthly_decimal_hours') }}"><i class="fa fa-calendar"></i> <span>{{ trans('fields.monthly_decimal_hours') }}</span></a></li>
            <li><a href="{{ backpack_url('reports/monthly_summary') }}"><i class="fa fa-calendar-o"></i> <span>{{ trans('fields.monthly_summary') }}</span></a></li>
            <li><a href="{{ backpack_url('reports/detailed_hours') }}"><i class="fa fa-user-plus"></i> <span>{{ trans('fields.detailed_hours') }}</span></a></li>
            <li><a href="{{ backpack_url('reports/hours_by_department') }}"><i class="fa fa-folder-open"></i> <span>{{ trans('fields.hours_by_department') }}</span></a></li>
            <li><a href="{{ backpack_url('reports/hours_by_department_employee') }}"><i class="fa fa-address-card"></i> <span>{{ trans('fields.hours_by_department_employee') }}</span></a></li>
            <li><a href="{{ backpack_url('reports/punctuality') }}"><i class="fa fa-user-times"></i> <span>{{ trans('fields.punctuality') }}</span></a></li>
        </ul>
    </li>

    @endif
    @if(!$plans || ($plans && $plans->billing_status != 'unlimited'))
        <li class="treeview">
            <a href="#"><i class="fa fa-list"></i> <span>{{ trans('fields.financial') }}</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
                <li><a href="{{ backpack_url('billing/company_information') }}"><i class="fa fa-info-circle"></i> <span>{{ trans('fields.company_information') }}</span></a></li>
                <li><a href="{{ backpack_url('billing') }}"><i class="fa fa-cc-paypal"></i> <span>{{ trans('fields.payment_method') }}</span></a></li>
                <li><a href="{{ backpack_url('transaction') }}"><i class="fa fa-money"></i> <span>{{ trans('fields.transactions') }}</span></a></li>

            </ul>
        </li>
    @endif
@endrole

@role('employee')
    <li><a href="{{ backpack_url('record') }}"><i class="fa fa-clock-o"></i> <span>{{ trans('fields.record') }}</span></a></li>
    <li><a href="{{ backpack_url('actions') }}"><i class="fa fa-clock-o"></i> <span>{{ trans('fields.actions') }}</span></a></li>
    <li><a href="{{ backpack_url('calendar') }}"><i class="fa fa-calendar"></i> <span>{{ trans('fields.calendar') }}</span></a></li>
    <li><a href="{{ backpack_url('employee_holiday_days') }}"><i class="fa fa-calendar-check-o"></i> <span>{{ trans('fields.employee_holiday_days') }}</span></a></li>
    <li>
        <a href="{{ backpack_url('holidays') }}"><i class="fa fa-plane"></i> <span id="holiday">{{ trans('fields.holidays') }}</span>
            @php
                $employee_id = backpack_user()->employee->id;
                $not_read_vacation_count = App\Models\Holiday::where('employee_id', $employee_id)->where('employee_is_read', 0)->count();
            @endphp
            <span class="pull-right-container vacation_notification_container">
                            @if($not_read_vacation_count != 0)
                    <span class="label pull-right bg-red vacation_notification">
                                    {{$not_read_vacation_count}}
                                </span>
                @endif
                        </span>
        </a>
    </li>
    <li>
        <a href="{{ backpack_url('expenses') }}"><i class="fa fa-user-times"></i> <span>{{ trans('fields.expense') }}</span>
            @php
                $employee_id = backpack_user()->employee->id;
                $not_read_expense_count = App\Models\Expense::where('employee_id', $employee_id)->where('employee_is_read', 0)->count();
            @endphp
            <span class="pull-right-container expense_notification_container">
                            @if($not_read_expense_count != 0)
                    <span class="label pull-right bg-red expense_notification">
                                    {{$not_read_expense_count}}
                                </span>
                @endif
                        </span>
        </a>
    </li>
    <li>
        <a href="{{ backpack_url('medical_day') }}"><i class="fa fa-plus-square"></i> <span>{{ trans('fields.medical_day') }}</span>
            @php
                $employee_id = backpack_user()->employee->id;
                $not_read_medical_count = App\Models\MedicalDay::where('employee_id', $employee_id)->where('employee_is_read', 0)->count();
            @endphp
            <span class="pull-right-container medical_notification_container">
                            @if($not_read_medical_count != 0)
                    <span class="label pull-right bg-red medical_notification">
                                    {{$not_read_medical_count}}
                                </span>
                @endif
                        </span>
        </a>
    </li>
    <li>
        <a href="{{ backpack_url('absences') }}"><i class="fa fa-home"></i> <span>{{ trans('fields.absences') }}</span>
            @php
                $employee_id = backpack_user()->employee->id;
                $not_read_absences_count = App\Models\Absence::where('employee_id', $employee_id)->where('employee_is_read', 0)->count();
            @endphp
            <span class="pull-right-container absences_notification_container">
                            @if($not_read_absences_count != 0)
                    <span class="label pull-right bg-red absences_notification">
                                    {{$not_read_absences_count}}
                                </span>
                @endif
                        </span>
        </a>
    </li>
    <li>
        <a href="{{ backpack_url('incidents') }}"><i class="fa fa-cab"></i> <span>{{ trans('fields.incident') }}</span>
            @php
                $employee_id = backpack_user()->employee->id;
                $not_read_incidents_count = App\Models\Incident::where('employee_id', $employee_id)->where('employee_is_read', 0)->count();
            @endphp
            <span class="pull-right-container incidents_notification_container">
                            @if($not_read_incidents_count != 0)
                    <span class="label pull-right bg-red incidents_notification">
                                    {{$not_read_incidents_count}}
                                </span>
                @endif
                        </span>
        </a>
    </li>
    <li>
        <a href="{{ backpack_url('event_management') }}"><i class="fa fa-users"></i> <span>{{ trans('fields.other_events') }}</span>
            @php
                $employee_id = backpack_user()->employee->id;
                $not_read_other_count = App\Models\EmployeeEvent::where('employee_id', $employee_id)->where('employee_is_read', 0)->count();
            @endphp
            <span class="pull-right-container other_notification_container">
                            @if($not_read_other_count != 0)
                    <span class="label pull-right bg-red other_notification">
                                    {{$not_read_other_count}}
                                </span>
                @endif
                        </span>
        </a>
    </li>
@endrole
