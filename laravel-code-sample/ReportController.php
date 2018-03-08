<?php namespace App\Http\Controllers\Api;

use App\ErrorReporting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends ApiController
{

    /**
     * @SWG\Post(
     *   path="/api/v1/report",
     *   tags={"Error Reports"},
     *   summary="Report an issue",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Report object that needs to be added",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Report"),
     *     ),
     *   @SWG\Response(
     *     response=200,
     *     description="Array the reported issue"
     *   ),
     *    @SWG\Response(
     *     response=401,
     *     description="No user exists for given ID"
     *   )
     * )
     */
    public function error_reporting(Request $request)
    {
	if ($request->has('user_id')) {
            $is_regular = $request->input('user_id');
        } else {
            return $this->respondMissingParameters('user_id field is required.');
        }
	
	    $error_type = $request->input('error_type');
	    //Get error report
        $errorReport = $this->dataReport($request, $user_id, $error_type);
        
        return $this->respondCreated([
            'created' => $errorReport,
        ]);
    }

    /**
     * Return saved feedback report
     *
     * @param  Request                    $request
     * @param  int                        $user_id     ID of user
     * @param  string                     $error_type  type of error reprot (feedback/crash)
     * @return object[App\ErrorReporting] $errorReport Feedback report
     */
    protected function dataReport($request, $user_id, $error_type)
    {
        $note            = $request->input('note');
        $description     = $request->input('description');
        $os_version      = $request->has('os_version') ? $request->input('os_version') : null;
        $platform        = $request->has('platform') ? $request->input('platform') : null;
        $release_version = $request->has('release_version') ? $request->input('release_version') : null;
        $manufacturer    = $request->has('manufacturer') ? $request->input('manufacturer') : null;
        $model           = $request->has('model') ? $request->input('model') : null;

        $errorReport = new ErrorReporting([
            'user_id'         => $user_id,
            'error_type'      => $error_type,
            'os_version'      => $os_version,
            'platform'        => $platform,
            'release_version' => $release_version,
            'manufacturer'    => $manufacturer,
            'model'           => $model,
            'note'            => $note,
            'description'     => $description,
        ]);

        try {
            $errorReport->save();
        } catch (QueryException $e) {
            return $this->respondInvalidParameters('No user exists for given ID.');
        }

        return $errorReport;
    }

