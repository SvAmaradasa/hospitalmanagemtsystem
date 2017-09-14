<?php

namespace App\Http\Controllers;

use App\common;
use App\Scan;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Mockery\Exception;

class ScanController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Create Scan.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $scan = new Scan();
            $scan->fill($request->input('scan'));

            if ($scan == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            if (!$scan->validate()) {
                $error = $scan->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $scan->save();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_SAVE_SCAN], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get All Scans.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            $scans = Scan::all();

            if ($scans == null) {
                return response()->json([common::$SCAN_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('scans'));
            }
        } catch (Exception $e) {
            return response()->json([common::$SCAN_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Scan from ID.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        try {
            $scan = Scan::find($id);

            if ($scan == null) {
                return response()->json([common::$SCAN_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('scan'));
            }
        } catch (Exception $e) {
            return response()->json([common::$SCAN_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Scan.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $scan = Scan::find($request->input('scan.id'));

            if ($scan == null) {
                return response()->json([common::$SCAN_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else if (!$scan->validate()) {
                $error = $scan->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $scan->fill($request->input('scan'));
                $scan->save();

                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_UPDATE_SCAN], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        try {
            $scan = Scan::findOrFail($request->input('scanId'));

            if ($scan == null) {
                return response()->json([common::$SCAN_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                $scan->delete();
                return response()->json([common::$SUCCESS]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$SCAN_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_SCAN], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
