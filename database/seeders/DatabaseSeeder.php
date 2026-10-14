<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categoriesData = [
            ['name' => 'Công nghệ & AI', 'color' => 'primary', 'icon' => 'bi-cpu', 'description' => 'Sách lập trình, trí tuệ nhân tạo, kiến trúc phần mềm và khoa học dữ liệu.'],
            ['name' => 'Kinh tế & Quản trị', 'color' => 'success', 'icon' => 'bi-graph-up-arrow', 'description' => 'Chiến lược kinh doanh, tài chính cá nhân, khởi nghiệp và lãnh đạo.'],
            ['name' => 'Văn học Kinh điển', 'color' => 'warning', 'icon' => 'bi-Feather', 'icon' => 'bi-book-half', 'description' => 'Tiểu thuyết, truyện ngắn, tác phẩm văn học nổi tiếng trong và ngoài nước.'],
            ['name' => 'Tâm lý & Kỹ năng sống', 'color' => 'info', 'icon' => 'bi-lightbulb', 'description' => 'Phát triển bản thân, tư duy phản biện, tâm lý học hành vi.'],
            ['name' => 'Khoa học & Vũ trụ', 'color' => 'danger', 'icon' => 'bi-rocket-takeoff', 'description' => 'Khám phá vũ trụ, vật lý lượng tử, lịch sử tiến hóa nhân loại.'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[] = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'color' => $cat['color'],
                'icon' => $cat['icon'],
                'description' => $cat['description'],
            ]);
        }

        $authorsData = [
            ['name' => 'Robert C. Martin', 'nationality' => 'Hoa Kỳ', 'birth_year' => '1952', 'bio' => 'Chuyên gia kỹ nghệ phần mềm huyền thoại (Uncle Bob), tác giả bộ sách Clean Code & Clean Architecture.'],
            ['name' => 'Daniel Kahneman', 'nationality' => 'Israel - Hoa Kỳ', 'birth_year' => '1934', 'bio' => 'Nhà tâm lý học đạt giải Nobel Kinh tế năm 2002 với nghiên cứu về kinh tế học hành vi.'],
            ['name' => 'Yuval Noah Harari', 'nationality' => 'Israel', 'birth_year' => '1976', 'bio' => 'Nhà sử học, giáo sư tại Đại học Hebrew Jerusalem, tác giả Sapiens, Homo Deus và 21 Bài học cho thế kỷ 21.'],
            ['name' => 'Nguyễn Nhật Ánh', 'nationality' => 'Việt Nam', 'birth_year' => '1955', 'bio' => 'Nhà văn Việt Nam được yêu thích nhất với hàng loạt tác phẩm tuổi học trò giàu cảm xúc.'],
            ['name' => 'Morgan Housel', 'nationality' => 'Hoa Kỳ', 'birth_year' => '1983', 'bio' => 'Chuyên gia tài chính hành vi, đối tác tại Collaborative Fund và tác giả bestseller Tâm lý học về tiền.'],
            ['name' => 'Martin Kleppmann', 'nationality' => 'Vương quốc Anh', 'birth_year' => '1983', 'bio' => 'Giáo sư nghiên cứu hệ thống phân tán tại Đại học Cambridge, tác giả Designing Data-Intensive Applications.'],
        ];

        $authors = [];
        foreach ($authorsData as $au) {
            $authors[] = Author::create($au);
        }

        $booksData = [
            [
                'isbn' => '978-0132350884',
                'title' => 'Clean Code - Mã Sạch & Con Đường Thành Nghệ Nhân',
                'category_id' => $categories[0]->id,
                'author_id' => $authors[0]->id,
                'publisher' => 'NXB Dân Trí',
                'published_year' => 2023,
                'pages' => 464,
                'cost_price' => 190000,
                'price' => 320000,
                'stock' => 24,
                'min_stock' => 8,
                'sold_count' => 86,
                'rating' => 4.9,
                'cover_color' => '#4f46e5',
                'description' => 'Cẩm nang gối đầu giường cho mọi lập trình viên chuyên nghiệp về cách viết code dễ đọc, dễ bảo trì và mở rộng.',
                'is_featured' => true,
            ],
            [
                'isbn' => '978-1449373320',
                'title' => 'Thiết Kế Hệ Thống Dữ Liệu Lớn (DDIA)',
                'category_id' => $categories[0]->id,
                'author_id' => $authors[5]->id,
                'publisher' => 'NXB Khoa Học & Kỹ Thuật',
                'published_year' => 2024,
                'pages' => 616,
                'cost_price' => 260000,
                'price' => 450000,
                'stock' => 4,
                'min_stock' => 6,
                'sold_count' => 62,
                'rating' => 5.0,
                'cover_color' => '#0284c7',
                'description' => 'Phân tích sâu sắc kiến trúc cơ sở dữ liệu, hệ thống phân tán, xử lý luồng và độ tin cậy quy mô lớn.',
                'is_featured' => true,
            ],
            [
                'isbn' => '978-0134494166',
                'title' => 'Clean Architecture - Kiến Trúc Phần Mềm Hiện Đại',
                'category_id' => $categories[0]->id,
                'author_id' => $authors[0]->id,
                'publisher' => 'NXB Thông Tin & Truyền Thông',
                'published_year' => 2023,
                'pages' => 432,
                'cost_price' => 175000,
                'price' => 295000,
                'stock' => 15,
                'min_stock' => 5,
                'sold_count' => 48,
                'rating' => 4.8,
                'cover_color' => '#2563eb',
                'description' => 'Nguyên lý SOLID, phân tầng kiến trúc độc lập framework giúp hệ thống bền vững hàng chục năm.',
                'is_featured' => false,
            ],
            [
                'isbn' => '978-0857197689',
                'title' => 'Tâm Lý Học Về Tiền (The Psychology of Money)',
                'category_id' => $categories[1]->id,
                'author_id' => $authors[4]->id,
                'publisher' => 'NXB Kinh Tế',
                'published_year' => 2024,
                'pages' => 384,
                'cost_price' => 110000,
                'price' => 189000,
                'stock' => 3,
                'min_stock' => 10,
                'sold_count' => 115,
                'rating' => 4.9,
                'cover_color' => '#059669',
                'description' => '19 câu chuyện ngắn khám phá cách con người suy nghĩ về tiền bạc và đưa ra quyết định tài chính thông minh.',
                'is_featured' => true,
            ],
            [
                'isbn' => '978-0374533557',
                'title' => 'Tư Duy Nhanh Và Chậm (Thinking, Fast and Slow)',
                'category_id' => $categories[3]->id,
                'author_id' => $authors[1]->id,
                'publisher' => 'NXB Thế Giới',
                'published_year' => 2022,
                'pages' => 612,
                'cost_price' => 160000,
                'price' => 279000,
                'stock' => 18,
                'min_stock' => 6,
                'sold_count' => 74,
                'rating' => 4.8,
                'cover_color' => '#d97706',
                'description' => 'Giải mã hai hệ thống tư duy chi phối nhận thức, phán đoán và các thiên kiến tâm lý của con người.',
                'is_featured' => true,
            ],
            [
                'isbn' => '978-0062316097',
                'title' => 'Sapiens: Lược Sử Loài Người',
                'category_id' => $categories[4]->id,
                'author_id' => $authors[2]->id,
                'publisher' => 'NXB Tri Thức',
                'published_year' => 2023,
                'pages' => 560,
                'cost_price' => 145000,
                'price' => 255000,
                'stock' => 5,
                'min_stock' => 8,
                'sold_count' => 98,
                'rating' => 4.9,
                'cover_color' => '#dc2626',
                'description' => 'Hành trình tiến hóa vĩ đại từ loài vượn người cổ đại đến chủ nhân của kỷ nguyên trí tuệ nhân tạo.',
                'is_featured' => true,
            ],
            [
                'isbn' => '978-0062464316',
                'title' => 'Nexus: Lược Sử Mạng Lưới Thông Tin Từ Thời Đồ Đá Đến AI',
                'category_id' => $categories[4]->id,
                'author_id' => $authors[2]->id,
                'publisher' => 'NXB Thế Giới',
                'published_year' => 2025,
                'pages' => 528,
                'cost_price' => 185000,
                'price' => 325000,
                'stock' => 2,
                'min_stock' => 7,
                'sold_count' => 69,
                'rating' => 5.0,
                'cover_color' => '#7c3aed',
                'description' => 'Siêu phẩm mới nhất của Harari phân tích cách dòng chảy thông tin và AI định hình tương lai nhân loại.',
                'is_featured' => true,
            ],
            [
                'isbn' => '978-6041145672',
                'title' => 'Mắt Biếc (Ấn Bản Đặc Biệt)',
                'category_id' => $categories[2]->id,
                'author_id' => $authors[3]->id,
                'publisher' => 'NXB Trẻ',
                'published_year' => 2023,
                'pages' => 300,
                'cost_price' => 80000,
                'price' => 145000,
                'stock' => 30,
                'min_stock' => 8,
                'sold_count' => 92,
                'rating' => 4.9,
                'cover_color' => '#0d9488',
                'description' => 'Câu chuyện trong trẻo và day dứt về tình yêu đơn phương của Ngạn dành cho Hà Lan và làng Đo Đo.',
                'is_featured' => false,
            ],
            [
                'isbn' => '978-6041239801',
                'title' => 'Mùa Hè Không Tên',
                'category_id' => $categories[2]->id,
                'author_id' => $authors[3]->id,
                'publisher' => 'NXB Trẻ',
                'published_year' => 2024,
                'pages' => 292,
                'cost_price' => 85000,
                'price' => 155000,
                'stock' => 19,
                'min_stock' => 6,
                'sold_count' => 53,
                'rating' => 4.7,
                'cover_color' => '#ea580c',
                'description' => 'Ký ức tuổi thơ ngọt ngào và sâu lắng đưa người đọc trở về những mùa hè rực rỡ.',
                'is_featured' => false,
            ],
            [
                'isbn' => '978-1804090251',
                'title' => 'Same As Ever - Quy Luật Bất Biến Giữa Thế Giới Biến Động',
                'category_id' => $categories[1]->id,
                'author_id' => $authors[4]->id,
                'publisher' => 'NXB Kinh Tế',
                'published_year' => 2024,
                'pages' => 340,
                'cost_price' => 120000,
                'price' => 210000,
                'stock' => 12,
                'min_stock' => 5,
                'sold_count' => 41,
                'rating' => 4.8,
                'cover_color' => '#0f766e',
                'description' => 'Thay vì dự đoán những điều sẽ thay đổi, hãy nắm bắt những hành vi con người không bao giờ đổi thay.',
                'is_featured' => false,
            ],
        ];

        $books = [];
        foreach ($booksData as $b) {
            $books[] = Book::create($b);
        }

        $customersData = [
            ['name' => 'Trần Minh Quân', 'phone' => '0901234567', 'email' => 'minhquan.dev@gmail.com', 'address' => 'Quận 1, TP. Hồ Chí Minh', 'tier' => 'diamond', 'points' => 620, 'total_spent' => 6200000],
            ['name' => 'Lê Ngọc Bảo Trân', 'phone' => '0912345678', 'email' => 'baotran.le@gmail.com', 'address' => 'Quận Cầu Giấy, Hà Nội', 'tier' => 'gold', 'points' => 285, 'total_spent' => 2850000],
            ['name' => 'Phạm Hoàng Nam', 'phone' => '0987654321', 'email' => 'hoangnam.pham@gmail.com', 'address' => 'Quận Hải Châu, Đà Nẵng', 'tier' => 'silver', 'points' => 95, 'total_spent' => 950000],
            ['name' => 'Nguyễn Thu Hà', 'phone' => '0938112233', 'email' => 'thuha.nguyen@gmail.com', 'address' => 'Quận 3, TP. Hồ Chí Minh', 'tier' => 'bronze', 'points' => 32, 'total_spent' => 320000],
        ];

        $customers = [];
        foreach ($customersData as $c) {
            $customers[] = Customer::create($c);
        }

        // Tạo 6 đơn hàng mẫu trải đều trong 6 ngày gần đây để biểu đồ doanh thu trực quan
        $sampleOrders = [
            [
                'days_ago' => 5,
                'customer' => $customers[0],
                'channel' => 'pos',
                'payment_method' => 'qr_transfer',
                'status' => 'completed',
                'items' => [
                    ['book' => $books[0], 'qty' => 2],
                    ['book' => $books[1], 'qty' => 1],
                ],
                'note' => 'Khách mua cho team kỹ thuật',
            ],
            [
                'days_ago' => 4,
                'customer' => $customers[1],
                'channel' => 'online',
                'payment_method' => 'card',
                'status' => 'completed',
                'items' => [
                    ['book' => $books[3], 'qty' => 2],
                    ['book' => $books[4], 'qty' => 1],
                ],
                'note' => 'Giao giờ hành chính',
            ],
            [
                'days_ago' => 3,
                'customer' => $customers[2],
                'channel' => 'pos',
                'payment_method' => 'cash',
                'status' => 'completed',
                'items' => [
                    ['book' => $books[5], 'qty' => 1],
                    ['book' => $books[7], 'qty' => 2],
                ],
                'note' => 'Gói quà sinh nhật',
            ],
            [
                'days_ago' => 2,
                'customer' => $customers[0],
                'channel' => 'online',
                'payment_method' => 'qr_transfer',
                'status' => 'completed',
                'items' => [
                    ['book' => $books[6], 'qty' => 2],
                    ['book' => $books[2], 'qty' => 1],
                ],
                'note' => 'Ưu đãi VIP Kim Cương 10%',
            ],
            [
                'days_ago' => 1,
                'customer' => $customers[3],
                'channel' => 'pos',
                'payment_method' => 'cash',
                'status' => 'completed',
                'items' => [
                    ['book' => $books[8], 'qty' => 1],
                    ['book' => $books[9], 'qty' => 1],
                ],
                'note' => 'Thanh toán tại quầy',
            ],
            [
                'days_ago' => 0,
                'customer' => $customers[1],
                'channel' => 'pos',
                'payment_method' => 'qr_transfer',
                'status' => 'completed',
                'items' => [
                    ['book' => $books[1], 'qty' => 1],
                    ['book' => $books[6], 'qty' => 1],
                ],
                'note' => 'Đơn hàng mới trong ngày hôm nay',
            ],
        ];

        foreach ($sampleOrders as $idx => $s) {
            $date = Carbon::now()->subDays($s['days_ago']);
            $order = new Order([
                'order_code' => 'BK-' . $date->format('ymd') . '-0' . ($idx + 1),
                'customer_id' => $s['customer']->id,
                'channel' => $s['channel'],
                'payment_method' => $s['payment_method'],
                'status' => $s['status'],
                'note' => $s['note'],
            ]);
            $order->created_at = $date;
            $order->updated_at = $date;
            $order->save();

            foreach ($s['items'] as $itemData) {
                /** @var Book $book */
                $book = $itemData['book'];
                $qty = $itemData['qty'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $book->id,
                    'quantity' => $qty,
                    'unit_price' => $book->price,
                    'cost_price' => $book->cost_price,
                    'line_total' => $book->price * $qty,
                ]);
            }

            $order->recalculateTotals();
            $order->created_at = $date;
            $order->updated_at = $date;
            $order->save();
        }
    }
}
