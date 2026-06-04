<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_order_history(): void
    {
        $this->withoutVite();

        $user = $this->createUser();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 150000,
            'payment_status' => 'Pending',
            'payment_method' => 'COD',
            'order_date' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'item_type' => 'package',
            'item_id' => 1,
            'name' => 'Goi test',
            'price' => 150000,
            'quantity' => 1,
            'subtotal' => 150000,
        ]);

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('#ORD-' . $order->id);
    }

    public function test_customer_can_cancel_pending_order(): void
    {
        $this->withoutVite();

        $user = $this->createUser();
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 150000,
            'payment_status' => 'Pending',
            'payment_method' => 'COD',
            'order_date' => now(),
        ]);

        $this->actingAs($user)
            ->patch(route('orders.cancel', $order->id))
            ->assertRedirect(route('orders.show', $order->id));

        $this->assertSame('Cancelled', $order->fresh()->payment_status);
    }

    private function createUser(): User
    {
        return User::create([
            'full_name' => 'Khach hang test',
            'email' => 'customer-test@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);
    }
}
