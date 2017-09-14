<?php

namespace App\Http\Controllers;

use App\common;
use App\Lab;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Mockery\Exception;

class LabController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Create Lab.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $lab = new Lab();
            $lab->fill($request->input('lab'));

            if ($lab == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            if (!$lab->validate()) {
                $error = $lab->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $lab->save();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_SAVE_LAB], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get All Labs.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            $labs = Lab::all();

            if ($labs == null) {
                return response()->json([common::$LAB_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('labs'));
            }
        } catch (Exception $e) {
            return response()->json([common::$LAB_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Lab from ID.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        try {
            $lab = Lab::find($id);

            if ($lab == null) {
                return response()->json([common::$LAB_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('lab'));
            }
        } catch (Exception $e) {
            return response()->json([common::$LAB_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Lab.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $lab = Lab::find($request->input('lab.id'));

            if ($lab == null) {
                return response()->json([common::$LAB_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else if (!$lab->validate()) {
                $error = $lab->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $lab->fill($request->input('lab'));
                $lab->save();

                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_UPDATE_LAB], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        try {
            $lab = Lab::findOrFail($request->input('labId'));

            if ($lab == null) {
                return response()->json([common::$LAB_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                $lab->delete();
                return response()->json([common::$SUCCESS]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$LAB_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_LAB], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
