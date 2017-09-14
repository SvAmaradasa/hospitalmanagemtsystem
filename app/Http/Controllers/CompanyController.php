<?php

namespace App\Http\Controllers;

use App\common;
use App\Company;
use Illuminate\Http\Request;
use Mockery\Exception;

class CompanyController extends Controller
{
    public function __construct()
    {
        //Apply the jwt.auth middleware to all methods in this controller.
        $this->middleware('jwt.auth');
    }

    /**
     * Create Company
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $company = new Company();
            $company->fill($request->input('company'));

            if ($company == null) {
                return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
            }

            if (!$company->validate()) {
                $error = $company->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $company->save();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_SAVE_COMPANY], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Companies.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        try {
            $companies = Company::all();

            if ($companies == null) {
                return response()->json([common::$COMPANY_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('companies'));
            }
        } catch (Exception $e) {
            return response()->json([common::$COMPANY_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Company.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id)
    {
        try {
            $company = Company::find($id);

            if ($company == null) {
                return response()->json([common::$COMPANY_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                return response()->json(compact('company'));
            }
        } catch (Exception $e) {
            return response()->json([common::$COMPANY_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete Company.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        try {
            $company = Company::find($request->input('companyId'));

            if ($company == null) {
                return response()->json([common::$COMPANY_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else {
                $company->delete();
                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_DELETE_COMPANY], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update Company.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $company = Company::find($request->input('company.id'));

            if ($company == null) {
                return response()->json([common::$COMPANY_NOT_FOUND], common::$HTTP_NOT_FOUND);
            } else if (!$company->validate()) {
                $error = $company->getValidationErrors();
                return response()->json(compact('error'), common::$HTTP_BAD_REQUEST);
            } else {
                $company->fill($request->input('company'));
                $company->save();

                return response()->json([common::$SUCCESS]);
            }
        } catch (Exception $e) {
            return response()->json([common::$UNABLE_TO_UPDATE_COMPANY], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search Company.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {

        $searchBy = $request->input('searchBy');
        $searchText = $request->input('searchText');

        //Null Check
        if ($searchBy == null || $searchBy == "" || $searchText == null || $searchText == "") {
            //If request is null return bad request response
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        //Check Search by Fields
        if (!in_array($searchBy, common::$COMPANY_SEARCH_BY_FIELDS)) {
            //If request is null return bad request response
            return response()->json([common::$INVALID_PARAMETERS], common::$HTTP_BAD_REQUEST);
        }

        try {

            $companies = Company::where($searchBy, 'LIKE', '%' . $searchText . '%')->get();

            return response()->json(compact('companies'));

        } catch (Exception $e) {
            return response()->json([common::$COMPANY_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get Company count.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        try {
            $companyCount = Company::all()->count();
            return response()->json(compact('companyCount'));
        } catch (Exception $e) {
            return response()->json([common::$COMPANY_UNABLE_TO_GET], common::$HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
