<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with('customer')->get();
            return response()->json($orders);
        }

        return view('orders.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|string'
        ]);

        $validated['order_number'] = 'ORD-' . strtoupper(uniqid());

        $order = Order::create($validated);

        return response()->json(['message' => 'Order added successfully!', 'order' => $order]);
    }

    public function show(Order $order)
    {
        $order->load('customer');
        return response()->json($order);
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|string'
        ]);

        $order->update($validated);

        return response()->json(['message' => 'Order updated successfully!', 'order' => $order]);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully!']);
    }
}
