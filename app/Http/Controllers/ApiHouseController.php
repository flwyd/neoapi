<?php

namespace app\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Http\JsonApi;
use App\Models\Person;
use App\Models\ActionLog;
use App\Http\JsonApi\SerializeRecord;

class ApiHouseController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
        if ($this->user) {
            $this->user->retrieveRoles();
        }
    }

    public function isUser($person): bool
    {
        if (!$this->user)
            return false;

        return is_numeric($person) ? $this->user->id == $person : $this->user->id == $person->id;
    }

    public function findPerson($id)
    {
        if ($this->isUser($id)) {
            return $this->user;
        }

        return Person::findOrFail($id);
    }

    public function getYear():int
    {
        $query = request()->validate([ 'year' => 'required|digits:4']);
        return intval($query['year']);
    }

    public function userHasRole($roles)
    {
        if (!$this->user) {
            return false;
        }

        return $this->user->hasRole($roles);
    }

    public function toJson($data, $status=200)
    {
        return response()->json($data, $status);
    }

    public function jsonApi($resource, $filter=false)
    {
        $authenicatedUser = $filter ? $this->user : null;
        if (is_iterable($resource)) {
            $results = [];
            foreach ($resource as $row) {
                $results[] = $row->toJsonApi($authenicatedUser);
            }
        } else {
            $results = $resource->toJsonApi($authenicatedUser);
        }

        return response()->json([ 'data' => $results ]);
    }

    public function notAuthorized()
    {
        return JsonApi::notAuthorizedReponse(response());
    }

    public function notFound($message)
    {
        return JsonApi::notFoundResponse(response(), $message);
    }

    public function success($record=null)
    {
        if ($record) {
            return JsonApi::successResponse(response(), $record);
        }

        return response()->json([ 'status' => 'success' ]);
    }

    public function deleteSuccess()
    {
        return JsonApi::deleteSuccess(response());
    }

    public function errorJsonApi($record)
    {
        return response()->json((new SerializeRecord($record))->toErrorJsonApi(), 422);
    }

    public function jsonError($message)
    {
        return response()->json([ 'errors' => [
                [ 'title' => $message ]
            ]], 422);
    }

    public function log($area, $event, $message, $data=null, $targetPersonId=null) {
        ActionLog::record(
            $this->user,
            $area,
            $event,
            $message,
            $data,
            $targetPersonId
        );
    }
}
