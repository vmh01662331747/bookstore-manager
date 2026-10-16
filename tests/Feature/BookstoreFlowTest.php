<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookstoreFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_catalog_and_online_storefront_load_successfully(): void
    {
        $this->seed();

        $this->get(route('dashboard'))->assertStatus(200)->assertSee('BookVerse');
        $this->get(route('shop.index'))->assertStatus(200)->assertSee('BookVerse');
        $this->get(route('books.index'))->assertStatus(200)->assertSee('Clean Code');
        $this->get(route('categories.index'))->assertStatus(200);
        $this->get(route('authors.index'))->assertStatus(200);
        $this->get(route('customers.index'))->assertStatus(200);
        $this->get(route('orders.index'))->assertStatus(200);
    }

    public function test_smart_pos_order_deducts_stock_applies_vip_discount_and_cancels_cleanly(): void
    {
        $category = Category::create([
            'name' => 'Công nghệ',
            'slug' => 'cong-nghe',
            'color' => 'primary',
            'icon' => 'bi-cpu',
        ]);

        $author = Author::create([
            'name' => 'Robert C. Martin',
            'nationality' => 'USA',
        ]);

        $book = Book::create([
            'isbn' => '978-0132350884',
            'title' => 'Clean Code',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'cost_price' => 150000,
            'price' => 300000,
            'stock' => 10,
            'min_stock' => 3,
        ]);

        $customer = Customer::create([
            'name' => 'Nguyễn Văn VIP',
            'phone' => '0909999888',
            'tier' => 'diamond',
            'points' => 500,
            'total_spent' => 5000000,
        ]);

        $response = $this->post(route('orders.store'), [
            'customer_id' => $customer->id,
            'channel' => 'pos',
            'payment_method' => 'qr_transfer',
            'note' => 'Mua tại quầy POS',
            'items' => [
                ['book_id' => $book->id, 'quantity' => 2],
            ],
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.show', $order));

        $this->assertEquals(600000, $order->subtotal);
        $this->assertEquals(60000, $order->discount_amount);
        $this->assertEquals(540000, $order->total_amount);

        $book->refresh();
        $this->assertEquals(8, $book->stock);
        $this->assertEquals(2, $book->sold_count);

        $this->post(route('orders.cancel', $order))->assertRedirect(route('orders.show', $order));
        $book->refresh();
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
        $this->assertEquals(10, $book->stock);
        $this->assertEquals(0, $book->sold_count);
    }

    public function test_customer_can_buy_books_online_via_storefront_cart_and_checkout(): void
    {
        $category = Category::create([
            'name' => 'Kinh tế',
            'slug' => 'kinh-te',
            'color' => 'success',
            'icon' => 'bi-graph-up-arrow',
        ]);

        $author = Author::create([
            'name' => 'Morgan Housel',
            'nationality' => 'USA',
        ]);

        $book = Book::create([
            'isbn' => '978-0857197689',
            'title' => 'Tâm Lý Học Về Tiền',
            'category_id' => $category->id,
            'author_id' => $author->id,
            'cost_price' => 100000,
            'price' => 200000,
            'stock' => 15,
            'min_stock' => 5,
            'is_active' => true,
        ]);

        // Thêm 2 cuốn vào giỏ hàng Online
        $this->post(route('shop.cart.add', $book), ['quantity' => 2, 'buy_now' => 1])
            ->assertRedirect(route('shop.cart'));

        // Đặt hàng Online
        $checkoutResponse = $this->post(route('shop.checkout'), [
            'name' => 'Lê Minh Khách Online',
            'phone' => '0911222333',
            'email' => 'khachonline@gmail.com',
            'address' => '123 Nguyễn Huệ, Quận 1, TP.HCM',
            'payment_method' => 'qr_transfer',
            'note' => 'Giao nhanh trong ngày',
        ]);

        $order = Order::where('channel', 'online')->first();
        $this->assertNotNull($order);
        $checkoutResponse->assertRedirect(route('shop.track', ['code' => $order->order_code]));
        $this->assertEquals(400000, $order->total_amount);

        // Kiểm tra tồn kho đã giảm và hội viên mới đã được tạo + tích 40 điểm
        $book->refresh();
        $this->assertEquals(13, $book->stock);
        $this->assertDatabaseHas('customers', [
            'phone' => '0911222333',
            'points' => 40,
        ]);
    }
}
