<?php

namespace App\Http\Controllers;

use App\common;
use App\Enum\FeeType;
use App\Fee;
use Illuminate\Http\Request;
use Mockery\Exception;

class FeeController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Get all Fee Types.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeeTypes()
    {
        try {
            $feeTypes = FeeType::getAll();
            return response()->json(compact('feeTypes'));
        } catch (Exception $e) {
            return response()->json([common::$FEE_TYPE_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Fees from Fee Type.
     *
     * @param $type Fee Type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeeFromFeeType($type)
    {
        try {
            if (!FeeType::isValidValue($type)) {
                return response()->json([common::$INVALID_FEE_TYPE], common::$HTTP_BAD_REQUEST);
            }

            $fees = Fee::where('feeType', $type)->get();
            return response()->json(compact('fees'));
        } catch (Exception $e) {
            return response()->json([common::$FEE_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create Fee.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $fee = new Fee();
            $fee->fill($request->json('fee'));

            if (!$fee->validate()) {
                $error = $fee->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $fee->save();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_SAVE_FEE], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Fee.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            if($request->json('fee') == null){
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $fee = Fee::find($request->json('fee.id'));
            $fee->fill($request->json('fee'));

            if($fee == null){
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            if (!$fee->validate()) {
                $error = $fee->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $fee->save();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_UPDATE_FEE], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Fee.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        if ($id == null) {
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {
            $fee = Fee::find($id);

            if ($fee == null) {
                return response()->json([common::$FEE_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('fee'));
            }
        } catch (Exception $e) {
            return response()->json([common::$FEE_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Fee.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $feeId = $request->json('feeId');

        try {
            if ($feeId == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $fee = Fee::find($feeId);

            if ($fee == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            $fee->delete();
            return response()->json([common::$SUCCESS]);
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_FEE], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
