<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Services\Organization\PlanService;
use App\Models\Plan;
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
        // Fetch all plans and include the count of subscriptions in one go
        $plans = Plan::withCount('subscriptions')->get();

        return view('organization.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('organization.plans.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric'
        ]);

        $this->planService->create($request->all());

        return redirect()
            ->route('org.plans.index')
            ->with('success', 'Plan created successfully');

    }

    public function edit($id)
    {
        $plan = $this->planService->find($id);

        return view('organization.plans.edit', compact('plan'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric'
        ]);

        $this->planService->update($id, $request->all());

        return redirect()
            ->route('org.plans.index')
            ->with('success', 'Plan updated successfully');

    }

    public function destroy($id)
    {

        $this->planService->delete($id);

        return redirect()
            ->route('org.plans.index')
            ->with('success', 'Plan deleted successfully');

    }

}