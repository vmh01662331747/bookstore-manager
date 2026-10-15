<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('orders');

        if ($q = trim((string) $request->query('q', ''))) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($tier = $request->query('tier')) {
            $query->where('tier', $tier);
        }

        $customers = $query->orderByDesc('total_spent')->get();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        $customer = new Customer(['tier' => 'bronze']);
        return view('customers.form', compact('customer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30', 'unique:customers,phone'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $customer = Customer::create($data);

        return redirect()->route('customers.index')
            ->with('success', "Đã thêm khách hàng \"{$customer->name}\".");
    }

    public function show(Customer $customer): View
    {
        $customer->load(['orders.items.book']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30', Rule::unique('customers', 'phone')->ignore($customer->id)],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $customer->update($data);

        return redirect()->route('customers.index')
            ->with('success', "Đã cập nhật khách hàng \"{$customer->name}\".");
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->orders()->exists()) {
            return back()->with('error', 'Không thể xóa khách hàng đã có lịch sử mua sách.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Đã xóa khách hàng.');
    }
}
