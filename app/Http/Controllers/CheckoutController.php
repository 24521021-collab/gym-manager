<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(){
        if(!session('cart') || count(session('cart')) == 0) {
            return redirect()->route('products.index')->with('error', 'Giỏ hàng trống!');
        }
        return view('checkout');
    }

    public function processCheckout(Request $request) {
        $cart = session()->get('cart');
        $total = 0;
        foreach($cart as $item) { $total += $item['price'] * $item['stock_quantity']; }

        if ($request->payment_method == 'COD') {
            $order = $this->createOrder($total, 'Pending', 'COD');
            session()->forget('cart');
            return redirect()->route('products.index')->with('success', 'Đặt hàng thành công! Vui lòng chờ shipper.');
        } 
        
        if ($request->payment_method == 'VNPAY') {
            // Cấu hình VNPAY (Thông số test)
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = route('vnpay.return');
            $vnp_TmnCode = "BA7TF3EI"; // Mã website tại VNPAY 
            $vnp_HashSecret = "D1EDEE8F3UDW1SO2IHO8MN2R3Q54T47E"; // Chuỗi bí mật

            $vnp_TxnRef = time(); // Mã đơn hàng
            $vnp_OrderInfo = "Thanh toan don hang GymPro";
            $vnp_OrderType = "billpayment";
            $vnp_Amount = $total * 100;
            $vnp_Locale = "vn";
            $vnp_BankCode = "";
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef
            );

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) { $hashdata .= '&' . urlencode($key) . "=" . urlencode($value); } 
                else { $hashdata .= urlencode($key) . "=" . urlencode($value); $i = 1; }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }
            return redirect()->away($vnp_Url);
        }
    }

    public function vnpayReturn(Request $request) {
        if($request->vnp_ResponseCode == '00') {
            $cart = session()->get('cart');
            $total = 0;
            foreach($cart as $item) { $total += $item['price'] * $item['stock_quantity']; }
            
            $this->createOrder($total, 'Paid', 'VNPAY');
            session()->forget('cart');
            return redirect()->route('products.index')->with('success', 'Thanh toán qua VNPAY thành công!');
        }
        return redirect()->route('checkout')->with('error', 'Thanh toán thất bại hoặc đã bị hủy.');
    }

    private function createOrder($total, $status, $method) {
        return DB::transaction(function () use ($total, $status, $method) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'payment_status' => $status,
                'payment_method' => $method,
                'order_date' => now()
            ]);

            foreach(session('cart') as $id => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $details['stock_quantity'],
                    'subtotal'=>$details['price']* $details['stock_quantity']
                ]);
                
                // Trừ kho
                Product::find($id)->decrement('stock_quantity', $details['stock_quantity']);
            }
            return $order;
        });
    }
}
