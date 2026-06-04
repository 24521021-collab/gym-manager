<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Membership;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->where('user_id', Auth::id())
            ->when($request->filled('status'), fn ($query) => $query->where('payment_status', $request->status))
            ->orderBy('order_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    public function cancel($id)
    {
        $order = Order::with('items')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($order->payment_status !== 'Pending') {
            return back()->with('error', 'Chi co the huy don hang dang cho xac nhan.');
        }

        DB::transaction(function () use ($order) {
            $order->payment_status = 'Cancelled';
            $order->save();

            foreach ($order->items as $item) {
                if ($item->item_type === 'product' && $item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                }

                if ($item->item_type === 'package') {
                    Membership::where('user_id', $order->user_id)
                        ->where('package_id', $item->item_id)
                        ->where('status', 'Inactive')
                        ->update(['status' => 'Cancelled']);
                }

                if ($item->item_type === 'class') {
                    Booking::where('user_id', $order->user_id)
                        ->where('class_id', $item->item_id)
                        ->where('status', 'pending')
                        ->update(['status' => 'cancelled']);
                }
            }
        });

        return redirect()->route('orders.show', $order->id)->with('success', 'Da huy don hang thanh cong.');
    }
}
