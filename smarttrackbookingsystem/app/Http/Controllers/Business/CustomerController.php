<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Business\Customer\CustomerService;
use Illuminate\Validation\Rule;



class CustomerController extends Controller
{
    public function index(Business $business)
    {
        return view('business.admin.customer.index', compact('business'));
    }

    public function datatable(Business $business, Request $request)
    {
    $q = Customer::query()
        ->where('business_id', $business->id)
        ->with('user:id,name,email'); // add name

    if ($request->filled('status') && in_array($request->status, ['active','inactive'])) {
        $q->where('status', $request->status);
    }

    return DataTables::of($q)

        ->addColumn('name', function ($row) {
            return $row->user->name ?? '-';
        })

        ->addColumn('email', function ($row) {
            return $row->user->email ?? '-';
        })
        ->editColumn('created_at', function ($row) {
            return optional($row->created_at)->format('d M Y'); 
        })

        ->addColumn('actions', function($row) use ($business){
            $show = route('business.customers.show', [$business->slug, $row->id]);
            $edit = route('business.customers.edit', [$business->slug, $row->id]);
            $del  = route('business.customers.destroy', [$business->slug, $row->id]);

            return '
                <a href="'.$show.'" class="btn btn-warning shadow btn-xs sharp me-1"><i class="fa fa-eye"></i></a>
                <a href="'.$edit.'" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fa fa-pencil"></i></a>

                <form method="POST" action="'.$del.'" style="display:inline-block">
                    '.csrf_field().method_field('DELETE').'
                    <button type="submit" class="btn btn-xs btn-danger sharp btn-delete-customer"><i class="fa fa-trash"></i></button>
                </form>
            ';
        })

        ->rawColumns(['actions'])
        ->make(true);
    }

    public function create(Business $business)
    {
        return view('business.admin.customer.create', compact('business'));
    }

    public function store(Request $request, Business $business, CustomerService $customerService)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|string|max:255',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'nullable|string|max:255',
            'status'      => 'required|in:active,inactive',
        ]);

        try {
            $customerService->store($business, $validated);

            return redirect()
                ->route('business.customers.index', $business->slug)
                ->with('success', 'Customer created successfully.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Business $business, Customer $customer)
    {
        abort_if($customer->business_id !== $business->id, 404);

        $customer->load('user');

        return view('business.admin.customer.show', compact('business','customer'));
    }

    public function edit(Business $business, Customer $customer)
    {
        abort_if($customer->business_id !== $business->id, 404);

        return view('business.admin.customer.edit', compact('business','customer'));
    }

   public function update(Request $request, Business $business, Customer $customer, CustomerService $customerService)
    {
        abort_if($customer->business_id !== $business->id, 404);

        $validated = $request->validate([
            'customer_id'   => 'nullable|string|max:255',
            'name'          => 'required|string|max:255',
            'email'         => ['required','email', Rule::unique('users','email')->ignore($customer->user_id)],
            'phone'         => 'nullable|string|max:255',
            'address'       => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'status'        => ['required', Rule::in(['active','inactive'])],
        ]);

        try {
            $customerService->update($business, $customer, $validated);

            return redirect()
                ->route('business.admin.customer.show', [$business->slug, $customer->id])
                ->with('success', 'Customer updated successfully.');

        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

   public function destroy(Business $business, Customer $customer, CustomerService $customerService)
    {
        abort_if($customer->business_id !== $business->id, 404);

        try {
            $customerService->deleteCustomer($business, $customer);

        return redirect()->route('business.customers.index', $business->slug)->with('success', 'Customer deleted.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}