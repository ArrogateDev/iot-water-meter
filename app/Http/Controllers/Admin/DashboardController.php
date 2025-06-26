<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WaterMeterDashboard;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardSingle(Request $request)
    {
        $meter_id = (int)$request->input('meter');

        $result = WaterMeterDashboard::getDashboardSingle($meter_id);

        return $this->responseSuccess($result);
    }
}
