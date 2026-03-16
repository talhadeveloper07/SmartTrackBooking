<?php

namespace App\Http\Controllers\Api\Frontend\Service;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Service;
use App\Services\Business\Service\ServiceManagementService;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceManagementService $serviceManagementService
    ) {}

    public function byBusiness(Business $business): JsonResponse
    {
        $services = $this->serviceManagementService->getBusinessServicesForApi($business);

        return response()->json([
            'success' => true,
            'message' => 'Services fetched successfully.',
            'data' => $services,
        ]);
    }

    public function serviceDetails(Business $business, Service $service): JsonResponse
    {
        $data = $this->serviceManagementService->getServiceDetails($business, $service);

        return response()->json([
            'success' => true,
            'message' => 'Service details fetched successfully.',
            'data' => $data,
        ]);
    }
}