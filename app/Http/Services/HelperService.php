<?php
namespace App\Http\Services;

use App\Enums\ResponseTypes;

class HelperService
{

    public function entity($data, $statusCode = 200, $status = ResponseTypes::SUCCESS, $message = null)
    {
        return response()->json(['status' => $status, 'statusCode' => $statusCode, 'data' => $data, 'message' => $message], $statusCode);
    }

    public function serverException($exception)
    {
        return response()->json(['status' => ResponseTypes::SERVER_ERROR, 'statusCode' => 500, 'message' => $exception->getMessage()], 500);
    }

    public function messageException($message = 'No data found...', $statusCode = 404, $status = ResponseTypes::ERROR)
    {
        return response()->json(['status' => $status, 'statusCode' => $statusCode, 'message' => $message], $statusCode);
    }

    public function validateError($data, $override = false)
    {
        $errors       = [];
        $errorPayload = ! $override ? $data->getMessages() : $data;
        info($errorPayload);
        foreach ($errorPayload as $key => $value) {
            $errors[$key] = ! $override ? $value[0] : $value;
        }
        return response(['status' => ResponseTypes::VALIDATE_ERROR, 'statusCode' => 422, 'data' => $errors], 422);
    }

    public function funcResponse($data = [])
    {
        $type = $data['type'] ?? ResponseTypes::SUCCESS;
        $ok   = $type === ResponseTypes::SUCCESS ? true : false;

        return [
            'ok'      => $ok,
            'type'    => $type,
            'code'    => $data['code'] ?? 200,
            'data'    => $data['data'] ?? null,
            'message' => $data['message'] ?? null,
        ];
    }

    public function getQueryFieldRelations($queries = [])
    {
        $fields    = [];
        $relations = [];

        if (! empty($queries['fields'])) {
            $fields = gettype($queries['fields']) === 'array' ? $queries['fields'] : explode(',', $queries['fields']);
        }

        if (! empty($queries['relations'])) {
            $relations = gettype($queries['relations']) === 'array' ? $queries['relations'] : explode(',', $queries['relations']);
        }

        return ['fields' => $fields, 'relations' => $relations];
    }

    public function paginate($data)
    {
        return [
            'meta'   => [
                'total'        => $data['total'],
                'current_page' => $data['current_page'],
                'last_page'    => $data['last_page'],
                'per_page'     => $data['per_page'],
                'from'         => $data['from'],
                'to'           => $data['to'],
            ],
            'result' => $data['data'],
        ];
    }
}
