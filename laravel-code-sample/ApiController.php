<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApiController extends Controller
{

    /**
     * Internal application result codes
     */
    const RESULT_OK      = 100;
    const CREATED        = 101;
    const UPDATED        = 102;
    const DELETED        = 104;
    const ALREADY_EXISTS = 201;
    const MISSING_PARAMS = 401;
    const INVALID_PARAMS = 402;
    const INTERNAL_ERROR = 500;

    /**
     * Maximum number of records that may be requested in a GET
     */
    const MAX_RECORDS_LIMIT = 200;

    /**
     * HTTP Status Code
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Application-specific code
     * @var int
     */
    protected $internalCode = 100;

    /**
     * Get protected $statusCode
     * @return int
     */
    protected function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set protected var $statusCode
     * @param int
     */
    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Get protected $internalCode
     * @return int
     */
    protected function getInternalCode()
    {
        return $this->internalCode;
    }

    /**
     * Set the internal application code
     * @param int
     */
    protected function setInternalCode($internalCode)
    {
        $this->internalCode = $internalCode;
        return $this;
    }

    /**
     * Basic HTTP response
     * @param  mixed $data
     * @param  array $headers
     * @return Response
     */
    protected function respond(array $data, array $headers = [])
    {
        $data = array_merge(['result_code' => $this->getInternalCode()], $data);
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    /**
     * Paginated HTTP response
     * @param  array $data
     * @param  Pagination\LengthAwarePaginator $pagination pagination object
     * @param  array $headers
     * @return Response
     */
    protected function respondWithPagination(array $data, Pagination\LengthAwarePaginator $pagination, array $headers = [])
    {
        $total   = $pagination->total();
        $perPage = $pagination->perPage();

        $data = array_merge($data, [
            'pagination' => [
                'total_count'   => $total,
                'total_pages'   => ceil($total / $perPage),
                'current_page'  => $pagination->currentPage(),
                'next_page_url' => $pagination->nextPageUrl(),
                'limit'         => $perPage,
            ],
        ]);

        return $this->respond($data);
    }

    /**
     * Respond with created message
     * @param  string $message
     * @return Response
     */
    protected function respondOk(array $data = ['message' => 'Created'])
    {
        return $this
            ->setStatusCode(SymfonyResponse::HTTP_OK)
            ->respond($data);
    }

    /**
     * Respond with created message
     * @param  string $message
     * @return Response
     */
    protected function respondCreated(array $data = ['message' => 'Created'])
    {
        return $this
            ->setStatusCode(SymfonyResponse::HTTP_CREATED)
            ->setInternalCode(self::CREATED)
            ->respond($data);
    }

    /**
     * Respond with created message
     * @param  array $data
     * @return Response
     */
    protected function respondUpdated(array $data = ['message' => 'Updated'])
    {
        return $this
            ->setStatusCode(SymfonyResponse::HTTP_OK)
            ->setInternalCode(self::UPDATED)
            ->respond($data);
    }

    /**
     * Respond with created message
     * @param  string $message
     * @return Response
     */
    protected function respondDeleted(array $data = ['message' => 'Delete successful.'])
    {
        return $this
            ->setInternalCode(self::DELETED)
            ->respond($data);
    }

    /**
     * Respond with created message
     * @param  string $message
     * @return Response
     */
    protected function respondLogout(array $data = ['message' => 'Logout successful.'])
    {
        return $this
            ->setInternalCode(SymfonyResponse::HTTP_OK)
            ->respond($data);
    }

    /**
     * Respond with error body message
     * @param  string $message Error message.
     * @return Response
     */
    protected function respondWithError($message)
    {
        // ensure a default internal error code is set if another error code is not already set
        if ($this->getInternalCode() < 400) {
            $this->setInternalCode(self::INTERNAL_ERROR);
        }

        return $this->respond([
            'error'       => $message,
            'result_code' => $this->getInternalCode(),
        ]);
    }

    /**
     * Respond with an internal error message and status codes
     * @param  string $message [description]
     * @return [type]          [description]
     */
    protected function respondWithInternalError($message = 'msg to show')
    {
        return $this
            ->setStatusCode(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR)
            ->setInternalCode(self::INTERNAL_ERROR)
            ->respondWithError($message);
    }

    /**
     * Respond with not found message
     * @param  string
     * @return Response
     */
    protected function respondNotFound($message = 'msg to show')
    {
        return $this
            ->setStatusCode(SymfonyResponse::HTTP_NOT_FOUND)
            ->respondWithError($message);
    }

    /**
     * Respond with missing parameters message
     * @param  string $message
     * @return Response
     */
    protected function respondMissingParameters($message = 'msg to show')
    {
        return $this
            ->setStatusCode(SymfonyResponse::HTTP_BAD_REQUEST)
            ->setInternalCode(self::MISSING_PARAMS)
            ->respondWithError($message);
    }

    /**
     * Respond with invalid parameters message
     * @param  string $message
     * @return Response
     */
    protected function respondInvalidParameters($message = 'msg to show')
    {
        return $this
            ->setStatusCode(SymfonyResponse::HTTP_BAD_REQUEST)
            ->setInternalCode(self::INVALID_PARAMS)
            ->respondWithError($message);
    }

    protected function isGoogle()
    {
        return Request::capture()->header('Android');
    }

    public function pagination(Pagination\LengthAwarePaginator $pagination)
    {
        $total   = $pagination->total();
        $perPage = $pagination->perPage();
        return [
            'total_count'   => $total,
            'total_pages'   => ceil($total / $perPage),
            'current_page'  => $pagination->currentPage(),
            'next_page_url' => $pagination->nextPageUrl(),
            'limit'         => $perPage,
        ];
    }

}
