<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use SoapClient;
use SoapFault;

class Soap
{
    protected $client;

    private const TOKEN_SALT = 'B1o#pA5s$83d01';

    public function __construct()
    {
        try {
            $this->client = new SoapClient(config('app.soap_wsdl'), [
                'trace' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'encoding' => 'UTF-8',
                'exceptions' => true
            ]);
        } catch (SoapFault $e) {
            $this->throwException($e);
        }
    }

    private function throwException($e)
    {
        Log::error($e);

        error_clear_last();
        abort(500);
    }

    public function logsGetAll($data)
    {
        try {
            $response = $this->client->LogsGetAll([
                "token" => $this->generateToken($data['clientid']),
                "clientid" => (int)$data['clientid'],
                "deviceid" => (int)$data['deviceid'],
                "subdeviceid" => (int)$data['subdeviceid'],
            ]);

        } catch (SoapFault $e) {
            $this->throwException($e);
        }

        $response = json_decode($response->LogsGetAllResult);

        DeviceLog::create([
            'description' => 'Request for GetActions',
            'company_id' => backpack_user()->company->id,
            'transaction_id' => $response->transactionId
        ]);

        return $response;
    }

    public function logsGetNew($data)
    {
        try {
            $response = $this->client->LogsGetNew([
                "token" => $this->generateToken($data['clientid']),
                "clientid" => (int)$data['clientid'],
                "deviceid" => (int)$data['deviceid'],
                "subdeviceid" => (int)$data['subdeviceid'],
            ]);

        } catch (SoapFault $e) {
            $this->throwException($e);
        }

        $response = json_decode($response->LogsGetNewResult);

        DeviceLog::create([
            'description' => 'Request for GetActions',
            'company_id' => backpack_user()->company->id,
            'transaction_id' => $response->transactionId
        ]);

        return $response;
    }

    public function clientSave($data)
    {
        try {
            $response = $this->client->clientSave([
                "token" => $this->generateToken($data['name']),
                "name" => $data['name'],
                "postUrl" => $data['postUrl'],
            ]);
        } catch (SoapFault $e) {
            $this->throwException($e);
        }

        return json_decode($response->clientSaveResult);
    }

    public function deviceSave($data)
    {
        try {
            $response = $this->client->deviceSave([
                "token" => $this->generateToken($data['clientId']),
                "clientId" => (int)$data['clientId'],
                "deviceId" => (int)$data['deviceId'],
                "subDeviceId" => (int)$data['subDeviceId'],
                "deviceName" => $data['deviceName'],
                "ip" => $data['ip'],
                "port" => (int)$data['port'],
                "deviceType" => (int)$data['deviceType'],
                "deviceVersion" => (int)$data['deviceVersion'],
            ]);
        } catch (SoapFault $e) {
            $this->throwException($e);
        }

        $response = json_decode($response->deviceSaveResult);

        return $response;
    }

    public function deviceDelete($data)
    {
        try {
            $response = $this->client->deviceDelete([
                "token" => $this->generateToken($data['clientId']),
                "clientId" => (int)$data['clientId'],
                "deviceId" => (int)$data['deviceId'],
                "subDeviceId" => (int)$data['subDeviceId'],
            ]);
        } catch (SoapFault $e) {
            $this->throwException($e);
        }

        $response = json_decode($response->deviceDeleteResult);

        return $response;
    }

    public function userGetAll($data)
    {
        try {
            $response = $this->client->userGetAll([
                "token" => $this->generateToken($data['clientid']),
                "clientid" => (int)$data['clientid'],
                "deviceid" => (int)$data['deviceid'],
                "subdeviceid" => (int)$data['subdeviceid'],
            ]);
        } catch (SoapFault $e) {
            $this->throwException($e);
        }

        $response = json_decode($response->userGetAllResult);

        DeviceLog::create([
            'description' => 'Request for GetUsers',
            'company_id' => backpack_user()->company->id,
            'transaction_id' => $response->transactionId
        ]);

        return $response;
    }

    public function userGetNew($data)
    {
        try {
            $response = $this->client->userGetNew([
                "token" => $this->generateToken($data['clientid']),
                "clientid" => (int)$data['clientid'],
                "deviceid" => (int)$data['deviceid'],
                "subdeviceid" => (int)$data['subdeviceid'],
            ]);

        } catch (SoapFault $e) {
            $this->throwException($e);
        }

        $response = json_decode($response->userGetNewResult);

        DeviceLog::create([
            'description' => 'Request for GetUsers',
            'company_id' => backpack_user()->company->id,
            'transaction_id' => $response->transactionId
        ]);

        return $response;
    }

    public function userGetOne($data)
    {
        try {
            $response = $this->client->userGetOne([
                "token" => $this->generateToken($data['clientid']),
                "clientid" => (int)$data['clientid'],
                "deviceid" => (int)$data['deviceid'],
                "subdeviceid" => (int)$data['subdeviceid'],
                "userid" => (int)$data['userid'],
            ]);
        } catch (SoapFault $e) {
            $this->throwException($e);
        }

        $response = json_decode($response->userGetOneResult);

        DeviceLog::create([
            'description' => 'Request for GetUsers',
            'company_id' => backpack_user()->company->id,
            'transaction_id' => $response->transactionId
        ]);

        return $response;
    }

    private function generateToken($parameter)
    {
        return base64_encode(md5(self::TOKEN_SALT . $parameter, true));
    }
}
