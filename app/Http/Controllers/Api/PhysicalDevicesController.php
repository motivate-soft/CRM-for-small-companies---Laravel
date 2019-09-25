<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\ActionsOption;
use App\Models\Company;
use App\Models\DeviceLog;
use App\Models\Employee;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PhysicalDevicesController extends Controller
{
    public function storeData(Request $request)
    {
        try {
            $users = $request->json('result.users');
            $logs = $request->json('result.logs');
            $client_id = $request->json('clientId');

            if ($users && is_array($users) && count($users)) {

                $count_new = $count_updated = $count_total = 0;

                $transaction_id = $request->json('transactionId');

                foreach ($users as $user) {

                    $company = Company::where('client_id', $client_id)->first();

                    if ($company) {

                        $employee = Employee::where('employee_id', $user['id'])->where('company_id', $company->id)->first();

                        $count_total++;

                        if ($employee) {
                            $employee->alias = $user['alias'];
                            $employee->auth_type = $user['authType'];

                            $employee->save();

                            $count_updated++;
                        } else {

                            $user_db = new User();

                            $user_db = $user_db->create([
                                'name' => trans('fields.employee') . ' ' . $user['id'],
                                'email' => 'employee' . $company->id . strtolower(str_random(5)) . $user['id'] . '@' . str_slug($company->user->name) . '.org',
                                'password' => bcrypt('secret'),
                                'role' => User::ROLE_EMPLOYEE,
                                'status' => User::STATUS_APPROVED,
                            ]);

                            Employee::create([
                                'user_id' => $user_db->id,
                                'company_id' => $company->id,
                                'employee_id' => $user['id'],
                                'alias' => $user['alias'],
                                'auth_type' => $user['authType'],
                            ]);

                            $count_new++;
                        }
                    }
                }

                if ($count_total) {
                    DeviceLog::create([
                        'description' => "Information from users received. $count_total " . str_plural('user', $count_total) . " received, $count_new new " . str_plural('user', $count_new) . ", $count_updated " . str_plural('update', $count_updated),
                        'company_id' => $company->id,
                        'transaction_id' => $transaction_id,
                    ]);
                }
            }

            if ($logs && is_array($logs) && count($logs)) {

                $count_total = 0;

                $transaction_id = $request->json('transactionId');

                foreach ($logs as $log) {

                    $company = Company::where('client_id', $client_id)->first();

                    if ($company) {

                        $employee = Employee::where('employee_id', $log['userid'])->where('company_id', $company->id)->first();
                        $option = ActionsOption::where('company_id', $company->id)->where('key', $log['mode'])->first();

                        if ($employee && $option) {
                            Action::create([
                                'datetime' => Carbon::parse($log['date'] . ' ' . $log['time'])->toDateTimeString(),
                                'option_id' => $option->id,
                                'employee_id' => $employee->id,
                            ]);

                            $count_total++;
                        }
                    }
                }

                if ($count_total) {
                    DeviceLog::create([
                        'description' => "Information from actions received. $count_total " . str_plural('action', count($logs)) . " added",
                        'company_id' => $company->id,
                        'transaction_id' => $transaction_id,
                    ]);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            echo 'SQL error. ' . $e->getCode();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function storeAction(Request $request)
    {
        try {


            $transactionId = $request->transactionId;
            $clientId = $request->clientId;
            $code = $request->code;
            $actions = $request->input('result.logs');

            $md5 = md5($transactionId.":".$clientId.":".$code.":BIODACTIL_SECRET");
            if ($md5 != $request->token) {
                return response()->json([
                    'error' => "Forbidden"
                ], 403);
            }

			$result = array();
			
            foreach ($actions as $action) {
                $device = explode(':', $action['device']);
                $ip = $device[0];
                $port = $device[1];
                $employee_id = $action['userid'];
                $option_key = $action['mode'];
                $datetime = Carbon::createFromFormat('m/d/Y H:i:s', $action['date']." ".$action['time']);
                $now = Carbon::parse(Carbon::now())->toDateTimeString();

                $row = DB::table('employees')
                     ->join('companies', 'employees.company_id', '=', 'companies.id')
                     ->join('devices', 'devices.company_id', '=', 'companies.id')
                     ->join('actions_options', 'actions_options.device_id', '=', 'devices.id')					 
                     ->where('devices.ip', 'LIKE', $ip)
                     ->where('devices.port', '=', $port)
                     ->where('employees.employee_uid', '=', $employee_id)
                     ->where('actions_options.key', '=', $option_key)
                     ->select('actions_options.id AS option_id', 'employees.id as employee_id')
                     ->first();


                if ($row) {

                    $action = Action::firstOrCreate([
                        'datetime' => $datetime->toDateTimeString(), // Carbon::parse(Carbon::now())->toDateTimeString(),
                        'option_id' => $row->option_id,
                        'employee_id' => $row->employee_id,
                        'ip' => $ip,
                        'auth_type' => 'device',
                    ]);
					
					if ($action->wasRecentlyCreated) {
						
						array_push($result, ["code" => 0, "message" => "New action added"]);
						
					} else {
						
						array_push($result, ["code" => 1, "message" => "Action already exists"]);
						
					}
					
                } else {
					
					array_push($result, ["code" => 2, "message" => "Error adding action. Maybe not action options created."]);
					
				}


            }

            return response()->json([
                    "transactionId" => $transactionId,
                    "clientId" => $clientId,
                    "result" => "success", 
					"logs" => $result
                ], 200);


            // $validator = Validator::make($request->all(), [
            //     'ip' => 'required',
            //     'port' => 'required|numeric',
            //     'employee_id' => 'required',
            //     'option_id' => 'required',
            //     'datetime' => 'required',
            //     'token' => 'required'
            // ]);

            // if ($validator->fails()) {
            //     return response()->json([
            //         'error' => 'Invalid parameters'
            //     ], 422);
            // }

            // if (!($this->checkMD5($request))) {
            //     return response()->json([
            //         'error' => "Forbidden"
            //     ], 403);
            // }

        } catch (\Illuminate\Database\QueryException $e) {

            return response()->json([
                "transactionId" => $transactionId,
                "clientId" => $clientId,
                'result' => "error",
                'message' => $e->getMessage(),
				"logs" => array()
            ], 400);

        } catch (\Exception $e) {

            return response()->json([
                "transactionId" => $transactionId,
                "clientId" => $clientId,
                'result' => "error",
                'message' => $e->getMessage(),
				"logs" => array()
            ], 400);

        }
    }
}
