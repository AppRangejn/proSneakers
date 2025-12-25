<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items'          => 'required|array',
            'total_price'    => 'required|numeric',
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email',
            'phone'          => 'required|string',
            'payment_method' => 'required|in:card,cash',
            'delivery_type'  => 'required|in:courier,post',
            'city'           => 'required|string',
            'fee'            => 'nullable|numeric',
            // Умовна валідація залежно від типу доставки
            'address'        => 'required_if:delivery_type,courier',
            'post_type'      => 'required_if:delivery_type,post',
            'post_number'    => 'required_if:delivery_type,post',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            // 1. Створюємо замовлення з усіма деталями
            $order = Order::create([
                'user_id'        => auth()->id(),
                'total_price'    => $validated['total_price'],
                'first_name'     => $validated['first_name'],
                'last_name'      => $validated['last_name'],
                'email'          => $validated['email'],
                'phone'          => $validated['phone'],
                'payment_method' => $validated['payment_method'],
                'delivery_type'  => $validated['delivery_type'],
                'city'           => $validated['city'],
                'address'        => $validated['address'] ?? null,
                'post_type'      => $validated['post_type'] ?? null,
                'post_number'    => $validated['post_number'] ?? null,
                'fee'            => $validated['fee'] ?? 0,
                'status'         => 'pending',
            ]);

            // 2. Додаємо товари
            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['id'],
                    'color'      => $item['color'],
                    'size'       => $item['size'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            return response()->json([
                'message' => 'Замовлення №' . $order->id . ' успішно створено!',
                'order'   => $order
            ], 201);
        });
    }

    public function myOrders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items.product')
            ->latest()
            ->get();

        return response()->json($orders);
    }
}