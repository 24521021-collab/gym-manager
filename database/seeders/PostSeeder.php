<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dọn dẹp dữ liệu cũ
        Schema::disableForeignKeyConstraints();
        DB::table('posts')->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Lấy danh sách tác giả (Admin hoặc PT)
        $authors = User::whereIn('role', ['admin', 'pt'])->get();

        if ($authors->isEmpty()) {
            return;
        }

        // 3. Dữ liệu mẫu bài viết
        $posts = [
            [
                'title' => 'Sử dụng Creatine đúng cách để bứt phá sức mạnh bùng nổ',
                'category' => 'Dinh dưỡng',
                'content' => '<p><strong>Creatine Monohydrate</strong> từ lâu đã được giới thể hình công nhận là một trong những thực phẩm bổ sung an toàn và hiệu quả nhất để tăng cường sức mạnh, sức bền và khối lượng cơ bắp. Bài viết này sẽ hướng dẫn bạn cách sử dụng đúng chuẩn y khoa thể thao.</p>
                             <h2>1. Creatine hoạt động như thế nào?</h2>
                             <p>Trong các hoạt động cường độ cao và ngắn (như nâng tạ), cơ thể sử dụng ATP làm nguồn năng lượng chính. Tuy nhiên, lượng dự trữ ATP trong cơ bắp rất hạn chế. Creatine giúp tái tạo ATP nhanh chóng, cho phép bạn đẩy tạ nặng hơn và thực hiện nhiều reps hơn trước khi kiệt sức.</p>
                             <h2>2. Phương pháp nạp Creatine chuẩn</h2>
                             <ul>
                                <li><strong>Giai đoạn Loading (Không bắt buộc):</strong> 20g/ngày (chia làm 4 lần, mỗi lần 5g) trong 5-7 ngày đầu tiên. Mục đích để nhanh chóng làm bão hòa lượng Creatine trong cơ bắp.</li>
                                <li><strong>Giai đoạn Duy trì:</strong> 3-5g/ngày liên tục. Hầu hết hội viên KOR GYM chọn cách này vì tính tiện lợi và ít gây khó chịu dạ dày.</li>
                             </ul>
                             <div style="background-color: rgba(227, 24, 55, 0.05); padding: 1.5rem; border-left: 4px solid #E31837; margin: 1.5rem 0; font-style: italic;">
                                "Nên uống Creatine cùng với một nguồn Carbohydrate hấp thu nhanh (như nước ép trái cây) hoặc Protein để tăng cường khả năng hấp thu nhờ sự tăng tiết Insulin." 
                                <br><strong>- Lời khuyên từ HLV KOR GYM</strong>
                             </div>
                             <h2>3. Uống nhiều nước</h2>
                             <p>Creatine hút nước vào trong tế bào cơ bắp (cell volumization), tạo ra môi trường đồng hóa (anabolic) lý tưởng cho cơ bắp phát triển. Do đó, khi sử dụng Creatine, bạn bắt buộc phải uống nhiều nước hơn bình thường (khoảng 3-4 lít/ngày) để tránh tình trạng mất nước và chuột rút.</p>',
                'status' => 'Sẵn sàng',
                'header_image'=> 'post-1.jpg',
            ],
            [
                'title' => 'Bí quyết xây dựng hình thể: Quy trình Bulking và Cutting chuẩn khoa học',
                'category' => 'Kỹ thuật tập luyện',
                'header_image' => 'post-2.jpg',
                'content' => '<h2>Bí Quyết Xây Dựng Hình Thể: Quy Trình Bulking (Xả Cơ) Và Cutting (Siết Cơ) Chuẩn Khoa Học</h2>
                              <p>Để đạt được một hình thể sắc nét, cơ bắp cuồn cuộn nhưng vẫn duy trì được tỷ lệ mỡ (Body Fat) thấp, bạn không thể tập luyện một cách cảm tính. Quy trình <strong>Bulking (Xả cơ)</strong> và <strong>Cutting (Siết cơ)</strong> chính là chìa khóa vàng giúp bạn kiểm soát cân nặng và chuyển hóa vóc dáng một cách tối ưu.</p>
                              <hr />
                              <h3>1. Giai Đoạn Bulking (Xả Cơ): Xây Dựng Nền Tảng Cơ Bắp</h3>
                              <p>Mục tiêu tối thượng của Bulking là <strong>tăng tối đa khối lượng cơ bắp</strong> nạc. Trong giai đoạn này, việc tăng một lượng mỡ nhất định là điều hoàn toàn bình thường và khó tránh khỏi.</p>
                              <ul>
                                  <li><strong>Dinh dưỡng (Caloric Surplus):</strong> Nạp nhiều hơn mức năng lượng tiêu hao (TDEE) từ <em>300 - 500 calo/ngày</em>. Ưu tiên nguồn Carb sạch (khoai lang, yến mạch, cơm trắng) và đảm bảo lượng Protein dồi dào để nuôi cơ.</li>
                                  <li><strong>Tập luyện:</strong> Tập trung vào các bài tập đa khớp (Compound Movements) như <em>Squat, Deadlift, Bench Press, Barbell Row</em>. Hãy tập với mức tạ nặng kích thích áp lực cơ bắp tối đa (chỉ thực hiện được từ 6 - 8 reps/hiệp).</li>
                              </ul>
                              <h3>2. Giai Đoạn Cutting (Siết Cơ) : Gọt Giũa Lớp Mỡ Thừa</h3>
                              <p>Sau khi đã xây dựng được một nền tảng cơ bắp đồ sộ, giai đoạn Cutting bắt đầu để đốt cháy lớp mỡ thừa bao phủ xung quanh, làm lộ rõ các múi cơ cắt nét bên dưới.</p>
                              <ul>
                                  <li><strong>Dinh dưỡng (Caloric Deficit):</strong> Cắt giảm lượng calo nạp vào thấp hơn mức TDEE khoảng <em>300 - 500 calo/ngày</em>. Giảm lượng Carb dần dần, đồng thời duy trì hoặc tăng nhẹ lượng Protein để bảo toàn khối lượng cơ, chống dị hóa (Anti-catabolic).</li>
                                  <li><strong>Tập luyện:</strong> Vẫn cố gắng giữ cường độ và mức tạ nặng như lúc Bulking để "nhắc nhở" cơ thể giữ cơ. Bổ sung thêm các buổi tập Cardio cường độ cao ngắt quãng (HIIT) hoặc Cardio cường độ thấp ổn định (LISS) từ <em>3 - 4 buổi/tuần</em> để tăng tốc tốc độ đốt mỡ.</li>
                              </ul>
                              <hr />',
                'status' => 'Sẵn sàng',
            ],
            [
                'title' => 'Sự kiện KOR Championship 2026 sắp diễn ra',
                'category' => 'Sự kiện',
                'content' => '<h2>KOR Championship 2026 Chính Thức Khởi Động: Giải Chạy Marathon Lớn Nhất Năm Đã Trở Lại!</h2>
    
    <p>Cộng đồng yêu chạy bộ Việt Nam đang nóng lên từng ngày khi <strong>KOR Championship 2026</strong> giải chạy marathon thường niên do hệ thống <strong>KOR GYM</strong> tổ chức – đã chính thức công bố ngày trở lại. Với thông điệp <em>"Bứt Phá Giới Hạn - Làm Chủ Đường Đua"</em>, giải chạy năm nay hứa hẹn sẽ mang đến những cung đường rực lửa, kết nối hàng nghìn runner từ nghiệp dư đến chuyên nghiệp cùng nhau lan tỏa lối sống xanh và tinh thần thể thao bất diệt.</p>

    <h3> Cung Đường Và Thời Gian Diễn Ra</h3>
    <p>Giải chạy năm nay được đầu tư quy mô lớn về cả công tác tổ chức lẫn y tế, đảm bảo trải nghiệm an toàn và tuyệt vời nhất cho các vận động viên:</p>
    <ul>
        <li><strong>Thời gian khởi tranh:</strong> Ngày <em>16/08/2026</em> (Thời gian tập trung và khởi động từ 04:00 sáng).</li>
        <li><strong>Địa điểm tập kết (Vạch Xuất Phát/Đích):</strong> Khu đô thị Phú Mỹ Hưng, Quận 7, TP. Hồ Chí Minh.</li>
        <li><strong>Đơn vị tổ chức:</strong> Hệ thống Trung tâm Tập luyện Cao cấp <strong>KOR GYM & Fitness</strong>.</li>
    </ul>

    <h3> Các Cự Ly Thi Đấu Phù Hợp Cho Mọi Đối Tượng</h3>
    <p>KOR Championship 2026 thiết kế 3 cự ly chạy từ dễ đến khó, giúp bất kỳ ai cũng có thể tham gia thử thách bản thân:</p>
    <ul>
        <li>
            <strong>Cự ly 5KM (Fun Run):</strong> 
            Trải nghiệm tuyệt vời dành cho gia đình, trẻ em và những người mới bắt đầu bước chân vào bộ môn chạy bộ. Đường chạy bằng phẳng, rợp bóng cây xanh.
        </li>
        <li>
            <strong>Cự ly 10KM (Challenge):</strong> 
            Thử thách tầm trung dành cho các thành viên muốn nâng cao thể lực. Cự ly này đòi hỏi bạn phải có một chiến thuật phân phối sức bền hợp lý.
        </li>
        <li>
            <strong>Cự ly 21KM (Half Marathon):</strong> 
            Đấu trường khắc nghiệt dành cho các "chân chạy" thực thụ. Cung đường này sẽ kiểm tra giới hạn thể chất tối đa và ý chí sắt đá của các runner sau chuỗi ngày dài tập luyện.
        </li>
    </ul>

    <h3>Bộ Race Kit Xịn Sò Và Cơ Cấu Giải Thưởng</h3>
    <p>Tất cả vận động viên đăng ký tham gia đều nhận được một <strong>Bộ Race Kit cao cấp</strong> bao gồm: Áo đấu thể thao KOR runner (chất liệu thoáng khí độc quyền), BIB chạy tích hợp chip timming tính giờ tự động, và các nhu yếu phẩm hỗ trợ năng lượng từ nhà tài trợ.</p>
    <p>Tổng giá trị giải thưởng tiền mặt dành cho top 3 vận động viên về đích sớm nhất ở mỗi cự ly (áp dụng cho cả nam và nữ) lên tới <strong>200.000.000 VNĐ</strong>. Đặc biệt, tất cả các runner hoàn thành cuộc đua trong thời gian quy định (Cut-off time) đều sẽ nhận được <em>Huy chương Finisher mạ vàng</em> lưu niệm của giải đấu.</p>

    <h3> Hướng Dẫn Đăng Ký Vé Sớm (Early Bird)</h3>
    <p>Cổng đăng ký mua BIB chạy trực tuyến đã chính thức mở với số lượng giới hạn:</p>
    <ul>
        <li><strong>Giai đoạn Early Bird:</strong> Giảm ngay 25% giá vé khi đăng ký trước ngày <em>15/07/2026</em>.</li>
        <li><strong>Đặc quyền Hội viên:</strong> Giảm thêm 10% cho tất cả các hội viên đang tham gia tập luyện tại các chi nhánh của KOR GYM trên toàn quốc.</li>
    </ul>',
                'status' => 'Sẵn sàng',
                'header_image' => 'post-3.jpg',
            ],
        ];

        // 4. Lưu vào Database
        foreach ($posts as $p) {
            $author = $authors->random();
            
            Post::create([
                'title'         => $p['title'],
                'slug'          => Str::slug($p['title']),
                'category'      => $p['category'],
                'content'       => $p['content'],
                'author_id'     => $author->id,
                'status'        => $p['status'],
                'header_image'  => $p['header_image'] ?? null,
                'created_at'    => Carbon::create(2026, 6, rand(1, 5), rand(8, 20), rand(0, 59)),
            ]);
        }
    }
}
