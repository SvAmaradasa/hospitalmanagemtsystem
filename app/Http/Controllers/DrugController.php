<?php

namespace App\Http\Controllers;

use App\common;
use App\Drug;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Mockery\Exception;

class DrugController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Create Drug.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $drug = new Drug();
            $drug->fill($request->input('drug'));

            if ($drug == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            if (!$drug->validate()) {
                $error = $drug->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $drug->save();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_SAVE_DRUG], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get All Drugs.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            $drugs = Drug::all();

            if ($drugs == null) {
                return response()->json([common::$DRUG_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('drugs'));
            }
        } catch (Exception $e) {
            return response()->json([common::$DRUG_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Drug from ID.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        try {
            $drug = Drug::find($id);

            if ($drug == null) {
                return response()->json([common::$DRUG_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('drug'));
            }
        } catch (Exception $e) {
            return response()->json([common::$DRUG_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Drug.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $drug = Drug::find($request->input('drug.id'));

            if ($drug == null) {
                return response()->json([common::$DRUG_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else if (!$drug->validate()) {
                $error = $drug->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $drug->fill($request->input('drug'));
                $drug->save();

                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_UPDATE_DRUG], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        try {
            $drug = Drug::findOrFail($request->input('drugId'));

            if ($drug == null) {
                return response()->json([common::$DRUG_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                $drug->delete();
                return response()->json([common::$SUCCESS]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([common::$DRUG_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_DRUG], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Drug Schedules
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDrugSchedules()
    {
        try {
            $drugSchedules = common::$DRUG_SCHEDULE;

            return response()->json(compact('drugSchedules'));

        } catch (Exception $e) {
            return response()->json([common::$DRUG_SCHEDULE_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Stock Quantity.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStock(Request $request)
    {
        try {
            $drug = Drug::where('id', $request->input('drugId'))->firstOrFail();

            if ($request->input('qty') == null || intval($request->input('qty')) < 0) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $drug->qty = $drug->qty + intval($request->input('qty'));
            $drug->save();

            return response()->json([common::$SUCCESS]);

        } catch (ModelNotFoundException $e) {
            return response()->json([common::$DRUG_NOT_FOUND], common::$HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_DRUG], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
