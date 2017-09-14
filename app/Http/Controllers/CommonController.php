<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use JWTAuth;
use Mockery\CountValidator\Exception;

class CommonController extends Controller
{
    public function __construct()
    {
        /*
         * Apply the jwt.auth middleware to all methods in this controller.
         * */
        $this->middleware('jwt.auth');
    }

    /**
     * Get all titles
     * */
    public function getAllTitles()
    {
        try {

            $titles = ['Mr', 'Mrs', 'Ms', 'Miss', 'Rev', 'Dr'];

            if ($titles == null) {
                return response()->json(['error' => 'Information Not Found'], 404);
            } else {
                return response()->json(compact('titles'));
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Could Not Retrieve Information'], 500);
        }
    }

    /**
     * Get all marital statuses
     * */
    public function getAllMaritalStatuses()
    {
        try {

            $maritalStatuses = ['Single', 'Married', 'Divorced', 'Widowed'];

            if ($maritalStatuses == null) {
                return response()->json(['error' => 'Information Not Found'], 404);
            } else {
                return response()->json(compact('maritalStatuses'));
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Could Not Retrieve Information'], 500);
        }
    }

    /**
     * Get all genders
     * */
    public function getAllGenders()
    {
        try {

            $genders = ['Male', 'Female', 'Other'];

            if ($genders == null) {
                return response()->json(['error' => 'Information Not Found'], 404);
            } else {
                return response()->json(compact('genders'));
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Could Not Retrieve Information'], 500);
        }
    }
}
