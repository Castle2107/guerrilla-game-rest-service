<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

trait ExceptionTrait
{
	public function apiException($request, $e)
	{
		if ($this->isModel($e)) {
            return $this->ModelResponse($e);
        }
        if ($this->isHttp($e)) {
            return $this->HttpResponse($e);
        }
        // return $this->GlobalExceptionResponse($e);
        return parent::render($request, $e);
	}

	public function prettyException($e) 
	{
		if ($this->isModel($e)) {
            return $this->ModelResponse($e);
        }
        if ($this->isHttp($e)) {
            return $this->HttpResponse($e);
        }
        return $this->GlobalException($e);
	}

	protected function isModel($e)
	{
		return $e instanceof ModelNotFoundException;
	}

	protected function isFatalErrorException($e)
	{
		return $e instanceof \FatalErrorException;
	}
	
	protected function isHttp($e)
	{
		return $e instanceof NotFoundHttpException; 
	}
	
	protected function GlobalExceptionResponse($e) 
	{
		return $this->customExceptionResponse($e);
	}

	protected function ModelResponse($e)
	{
		return $this->customExceptionResponse(
			'Model not found',
			$e,
			Response::HTTP_NOT_FOUND
		);
	}
	
	protected function HttpResponse($e)
	{
		return $this->customExceptionResponse(
			'Incorrect route',
			$e,
			Response::HTTP_NOT_FOUND
		);
	}

	protected function FatalErrorExceptionResponse($e)
	{
		return $this->customExceptionResponse(
			'Invalid parameter passed. Check the API',
			$e,
			400
		);
	}

	private function customExceptionResponse($errors = '', $exception, $httpCode = 500)
	{
		return response()->json([
			'errors' => $errors,
			'exception' => [
				'code' => $exception->getCode(),
				'message' => $exception->getMessage()
			],
		], $httpCode);
	}
}