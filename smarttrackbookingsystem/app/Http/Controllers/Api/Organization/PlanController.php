<?php

namespace App\Http\Controllers\Api\Organization;
use App\Http\Controllers\Controller;
use App\Services\Organization\PlanService;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    protected $planService;
      public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    public function index()
    {

        return response()->json([
            'success' => true,
            'data' => $this->planService->all()
        ]);

    }

    public function show($id)
    {

        return response()->json([
            'success' => true,
            'data' => $this->planService->find($id)
        ]);

    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric'
        ]);

        $plan = $this->planService->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully',
            'data' => $plan
        ]);

    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric'
        ]);

        $plan = $this->planService->update($id, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully',
            'data' => $plan
        ]);

    }

    public function destroy($id)
    {

        $this->planService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully'
        ]);

    }

}