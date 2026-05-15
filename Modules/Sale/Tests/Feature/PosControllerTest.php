<?php

namespace Modules\Sale\Tests\Feature;

use App\Models\User;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\People\Entities\Customer;
use Modules\Product\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Sale\Entities\Sale;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $permission = Permission::create(['name' => 'create_pos_sales']);
        $role->givePermissionTo($permission);
        $user->assignRole($role);

        $this->actingAs($user);
    }

    /** @test */
    public function it_redirects_to_sales_index_after_storing_pos_sale_without_print()
    {
        $customer = Customer::create([
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '123456789',
            'city' => 'Test City',
            'country' => 'Test Country',
            'address' => 'Test Address'
        ]);

        $category = Category::create([
            'category_name' => 'Test Category',
            'category_code' => 'TC01'
        ]);

        $product = Product::create([
            'product_name' => 'Test Product',
            'product_code' => 'TP01',
            'product_quantity' => 10,
            'product_cost' => 100,
            'product_price' => 200,
            'product_unit' => 'pc',
            'product_stock_alert' => 1,
            'category_id' => $category->id
        ]);

        Cart::instance('sale')->add([
            'id'      => $product->id,
            'name'    => $product->product_name,
            'qty'     => 1,
            'price'   => $product->product_price,
            'options' => [
                'code'             => $product->product_code,
                'unit_price'       => $product->product_price,
                'sub_total'        => $product->product_price,
                'product_discount' => 0,
                'product_discount_type' => 'fixed',
                'product_tax'      => 0,
            ]
        ]);

        $response = $this->post(route('app.pos.store'), [
            'customer_id' => $customer->id,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'total_amount' => 200,
            'paid_amount' => 200,
            'payment_method' => 'Cash',
            'note' => 'Test Note'
        ]);

        $response->assertRedirect(route('sales.index'));
        $this->assertDatabaseHas('sales', [
            'customer_id' => $customer->id,
            'total_amount' => 20000, // stored in cents
        ]);
    }

    /** @test */
    public function it_redirects_to_pos_pdf_after_storing_pos_sale_with_print()
    {
        $customer = Customer::create([
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'customer_phone' => '123456789',
            'city' => 'Test City',
            'country' => 'Test Country',
            'address' => 'Test Address'
        ]);

        $category = Category::create([
            'category_name' => 'Test Category',
            'category_code' => 'TC01'
        ]);

        $product = Product::create([
            'product_name' => 'Test Product',
            'product_code' => 'TP01',
            'product_quantity' => 10,
            'product_cost' => 100,
            'product_price' => 200,
            'product_unit' => 'pc',
            'product_stock_alert' => 1,
            'category_id' => $category->id
        ]);

        Cart::instance('sale')->add([
            'id'      => $product->id,
            'name'    => $product->product_name,
            'qty'     => 1,
            'price'   => $product->product_price,
            'options' => [
                'code'             => $product->product_code,
                'unit_price'       => $product->product_price,
                'sub_total'        => $product->product_price,
                'product_discount' => 0,
                'product_discount_type' => 'fixed',
                'product_tax'      => 0,
            ]
        ]);

        $response = $this->post(route('app.pos.store'), [
            'customer_id' => $customer->id,
            'tax_percentage' => 0,
            'discount_percentage' => 0,
            'shipping_amount' => 0,
            'total_amount' => 200,
            'paid_amount' => 200,
            'payment_method' => 'Cash',
            'note' => 'Test Note',
            'print' => 'on'
        ]);

        $sale = Sale::first();
        $response->assertRedirect(route('sales.pos.pdf', $sale->id));
    }
}
