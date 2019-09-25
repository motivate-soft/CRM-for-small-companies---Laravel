<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActionsOption;
use App\Models\Company;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Device;
use App\Models\Event;
use App\Models\Soap;
use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CompanyStoreRequest as StoreRequest;
use App\Http\Requests\CompanyUpdateRequest as UpdateRequest;
use Monolog\Handler\CubeHandler;

/**
 * Class CompanyCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class CompanyCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Company');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/companies');
        $this->crud->setEntityNameStrings(trans('fields.company'), trans('fields.companies'));
        $this->crud->addButtonFromModelFunction('line', 'password', 'change_password_button', 'end');
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        $this->crud->orderBy('created_at', 'DESC');

        $this->crud->enableExportButtons();

        $this->crud->setColumns([
            [
                'name'  => 'name',
                'label' => trans('fields.name'),
                'type'  => 'text',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('name', 'like', '%'.$searchTerm.'%');
                    });
                }
            ],
            [
                'name'  => 'email',
                'label' => trans('fields.email'),
                'type'  => 'email',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('email', 'like', '%'.$searchTerm.'%');
                    });
                }
            ],
            [
                'name'  => 'status',
                'label' => trans('fields.status'),
                'type'  => 'text',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhereHas('user', function ($q) use ($column, $searchTerm) {
                        $q->where('status', 'like', '%'.$searchTerm.'%');
                    });
                }
            ],
            [
                'name'  => 'DevicesCount',
                'label' => trans('fields.devices_count'),
                'type'  => 'text',
            ],
            [
                'name'  => 'vat_number',
                'label' => trans('fields.vat_number'),
                'type'  => 'text',
            ],
            [
                'name'  => 'address',
                'label' => trans('fields.address'),
                'type'  => 'text',
            ],
            [
                'name'  => 'country',
                'label' => trans('fields.country'),
                'type'  => 'text',
            ],
            [
                'name'  => 'currency_id',
                'label' => trans('fields.currency'),
                'type' => 'select',
                'entity' => 'currency', // the method that defines the relationship in your Model
                'attribute' => "short_key", // foreign key attribute that is shown to user
                'model' => "App\Models\Currency", // foreign key model
            ],
            [
                'name'  => 'signatory',
                'label' => trans('fields.signatory'),
                'type'  => 'image',
            ],
            [
                'name' => 'created_at',
                'label' => trans('fields.created_at'),
                'type' => 'date',
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
                'name'  => 'email',
                'label' => trans('fields.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'password',
                'label' => trans('fields.password'),
                'type'  => 'password',
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('fields.password_confirmation'),
                'type'  => 'password',
            ],
            [
                'name'  => 'status',
                'label' => trans('fields.status'),
                'type'  => 'enum',
            ],
            [ // select_from_array
                'name' => 'status',
                'label' => trans('fields.status'),
                'type' => 'select2_from_array',
                'options' => ['approved' => 'Approved', 'disabled'=> 'Disabled', 'banned' => 'Banned'],
                'allows_null' => false,
                'default' => 'approved',
            ],
            [
                'name'  => 'vat_number',
                'label' => trans('fields.vat_number'),
                'type'  => 'text',
            ],
            [
                'name'  => 'address',
                'label' => trans('fields.address'),
                'type'  => 'address_algolia',
            ],
//            [
//                'name'  => 'country',
//                'label' => trans('fields.country'),
//                'type'  => 'text',
//            ],
            [
                'name' => 'country_id',
                'label' => trans('fields.country'),
                'type' => 'select2',
                'entity' => 'country', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Country", // foreign key model
                'allows_null' => false,
            ],
	    [
                'name'  => 'currency_id',
                'label' => trans('fields.currency'),
                'type' => 'select2',
                'entity' => 'currency', // the method that defines the relationship in your Model
                'attribute' => "short_key", // foreign key attribute that is shown to user
                'model' => "App\Models\Currency", // foreign key model
            ],
            [
                'label' => trans('fields.signatory'),
                'name' => "signatory",
                'type' => 'image',
                'upload' => true,
                'crop' => false,
                'aspect_ratio' => 1,
            ],
            [
                'name' => 'language_id',
                'label' => trans('fields.language'),
                'type' => 'select2',
                'entity' => 'language', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model' => "App\Models\Language", // foreign key model
                'allows_null' => false,
            ],
            [
                'name' => 'timezone',
                'label' => trans('fields.timezone'),
                'type' => 'select2_from_array',
                'options' => array_combine(timezone_identifiers_list(), timezone_identifiers_list()),
                'allows_null' => false,
            ],

//            [
//                'name'  => 'holidays',
//                'label' => trans('fields.holidays'),
//                'type'  => 'number',
//            ],
//            [
//                'label'     => 'Working Days',
//                'type'      => 'checklist',
//                'name'      => 'workingdays',
//                'entity'    => 'workingDays',
//                'attribute' => 'name',
//                'model'     => "App\Models\WorkingDays",
//                'pivot'     => true,
//            ],
            [
                'name' => 'user_id',
                'type' => 'hidden'
            ],
        ]);

        // add asterisk for fields that are required in CompanyRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }

    public function store(StoreRequest $request)
    {
        $user_model_fqn = config('backpack.base.user_model_fqn');
        $user = new $user_model_fqn();
        $country_id = $request->country_id;
        $holiday_days = Country::find($country_id)->holiday_days;
        $user = $user->create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'role'      => User::ROLE_COMPANY,
            'status'    => $request->status,
            'holiday_days' => $holiday_days
        ]);

        $request->request->set('user_id', $user->id);

        $request->request->set('access_company_token', Company::createAccessToken());

//        $soap = new Soap();
//
//        $client = $soap->clientSave([
//            'name' => $request->name,
//            'postUrl' => route('api.devices'),
//        ]);
//
        $request->request->set('client_id', 1);
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);

        // default holiday days
        $working_days = Country::find($country_id)->workingDays->pluck('id');
        $user_id = $user->id;
        User::find($user_id)->workingDays()->attach($working_days);

        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry

        Company::createMandatoryEntities($this->crud->entry->id);

        return $redirect_location;
    }

    public function update(UpdateRequest $request)
    {
        $this->handlePasswordInput($request);

        $user_model_fqn = config('backpack.base.user_model_fqn');

        $user = $user_model_fqn::find($request->user_id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;

        if($request->password) {
            $user->password = $request->password;
        }

        $user->save();

        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function destroy($id)
    {
        $user_id = Company::find($id)->user_id;

        $redirect_location = parent::destroy($id);
        User::destroy($user_id);

        return $redirect_location;
    }

    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', bcrypt($request->input('password')));
        } else {
            $request->request->remove('password');
        }
    }
}
