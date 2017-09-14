<?php

namespace App\Http\Controllers;

use App\common;
use App\Xray;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Mockery\Exception;

class XrayController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Create Xray.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $xray = new Xray();
            $xray->fill($request->input('xray'));

            if ($xray == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            if (!$xray->validate()) {
                $error = $xray->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $xray->save();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_SAVE_SCAN], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get All Xrays.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            $xrays = Xray::all();

            if ($xrays == null) {
                return response()->json([common::$SCAN_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('xrays'));
            }
        } catch (Exception $e) {
            return response()->json([common::$SCAN_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Xray from ID.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        try {
            $xray = Xray::find($id);

            if ($xray == null) {
                return response()->json([common::$SCAN_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('xray'));
            }
        } catch (Exception $e) {
            return response()->json([common::$SCAN_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Xray.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $xray = Xray::find($request->input('xray.id'));

            if ($xray == null) {
                return response()->json([common::$SCAN_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else if (!$xray->validate()) {
                $error = $xray->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $xray->fill($request->input('xray'));
                $xray->save();

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
            $xray = Xray::findOrFail($request->input('xrayId'));

            if ($xray == null) {
                return response()->json([common::$SCAN_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                $xray->delete();
                return response()->json([common::$SUCCESS]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$SCAN_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_SCAN], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
