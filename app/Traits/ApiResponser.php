<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{
	protected function successResponse($data, $code)
	{
		return response()->json($data, $code);
	}

	protected function errorResponse($data, $code = 400)
	{
		return response()->json($data, $code);
	}

	protected function showAll(Collection $collection,$message = '',$code = 200)
	{
		 $response = [

            'status'  => true,
            'message' => $message,
            'data'    => $collection
        ];

		return $this->successResponse($response, $code);
	}

	protected function showOne(Model $instance,$message = '',$code = 200)
	{
        $response = [

            'status'  => true,
            'message' => $message,
            'data'    => $instance
        ];

		return $this->successResponse($response, $code);
	}

	protected function showMessage($message, $code = 200)
	{
		return $this->successResponse($message, $code);
	}

	function returnNotFoundResponse($message = '', $data = array())
	{
		$returnArr = [
			'statusCode' => 400, //404
			'status'     => false,//'not found',
			'message'    => $message,
			'data'       => ($data) ? ($data) : ((object) $data)
		];
		return response()->json($returnArr, 400); //404
	}

	function returnValidationErrorResponse($message = '', $data = array())
	{
		$returnArr = [
			'statusCode' => 400, //422
			'status'     => false,//'vaidation error',
			'message'    => $message,
			'data'       => ($data) ? ($data) : ((object) $data)
		];
		return response()->json($returnArr, 400); //422
	}

	function returnSuccessResponse($message = '', $data = array(), $is_array = false)
	{
		$is_array = !empty($is_array)?[]:(object)[];
		$returnArr = [
			'statusCode' => 200,
			'status'     => true,//'success'
			'message'    => $message,
			'data'       => ($data || is_null($data)) ? ($data) : $is_array
		];
		return response()->json($returnArr, 200);
	}
	
	function returnErrorResponse($message = '', $data = array())
	{
		$returnArr = [
			'statusCode' => 400, //500
			'status'     => false, //'error'
			'message'    => $message,
			'data'       => ($data) ? ($data) : ((object) $data)
		];
		return response()->json($returnArr, 400); //500
	}

	function returnCustomErrorResponse($message = '', $data = array())
	{
		$returnArr = [
			'statusCode' => 400, //404
			'status'     => false,//'error',
			'message'    => $message,
			'data'       => ($data) ? ($data) : ((object) $data)
		];
		return response()->json($returnArr, 400); //404
	}

	function returnError301Response($message = '', $data = array())
	{
		$returnArr = [
			'statusCode' => 400, //301
			'status'     => false,//'error',
			'message'    => $message,
			'data'       => ($data) ? ($data) : ((object) $data)
		];
		return response()->json($returnArr, 400); //301
	}

	function notAuthorizedResponse($message = '', $data = array())
	{
		$returnArr = [
			'statusCode' => 401,
			'status'     => false,//'error',
			'message'    => $message,
			'data'       => ($data) ? ($data) : ((object) $data)
		];
		return response()->json($returnArr,401);
	}
	
	function forbiddenResponse($message = '', $data = array())
	{
		$returnArr = [
			'statusCode' => 400, //403
			'status'     => false,//'error',
			'message'    => $message,
			'data'       => ($data) ? ($data) : ((object) $data)
		];
		return response()->json($returnArr, 400); //403
	}

	function methodNotAllowedResponse($message = '', $data = array())
	{
		$returnArr = [
			'statusCode' => 400, //405
			'status'     => false,//'error',
			'message'    => $message,
			'data'       => ($data) ? ($data) : ((object) $data)
		];
		return response()->json($returnArr, 400); //405
	}

	function modelNotFoundResponse($message = '', $data = array())
	{
		$returnArr = [
			'statusCode' => 400, //404
			'status'     => false,//'error',
			'message'    => $message,
			'data'       => ($data) ? ($data) : ((object) $data)
		];
		return response()->json($returnArr, 400); //404
	}

	protected function paginate(Collection $collection)
	{
		$rules = [
			'per_page' => 'integer|min:2|max:50',
		];

		Validator::validate(request()->all(), $rules);

		$page = LengthAwarePaginator::resolveCurrentPage();

		$perPage = 6;
		if (request()->has('per_page')) {
			$perPage = (int) request()->per_page;
		}

		$results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

		$paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
			'path' => LengthAwarePaginator::resolveCurrentPath(),
		]);

		$paginated->appends(request()->all());

		return $paginated;

	}
	
}