<?php
/**
 * Dragon Legend Hotel Core - Sample Data Importer
 * Tự động tạo dữ liệu mẫu chuẩn thiết kế khi kích hoạt plugin lần đầu
 *
 * @package DragonLegendCore
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Sample_Data {

    /**
     * Tự động khởi tạo dữ liệu mẫu nếu chưa có phòng nào
     */
    public static function init_sample_data_if_empty() {
        $existing_rooms = get_posts(array(
            'post_type'      => 'dl_room',
            'posts_per_page' => 1,
            'post_status'    => 'any'
        ));

        if (empty($existing_rooms)) {
            self::import_rooms();
            self::import_offers();
            self::import_amenities();
            self::import_testimonials();
        }
    }

    /**
     * Khởi tạo các hạng phòng
     */
    public static function import_rooms() {
        $rooms = array(
            array(
                'slug'        => 'superior-room',
                'category'    => 'Phòng Superior',
                'title'       => 'Superior Double / Twin Room',
                'subtitle'    => 'Thanh lịch, ấm cúng và đầy đủ tiện nghi tiêu chuẩn 4 sao',
                'content'     => 'Phòng Superior tại Dragon Legend Hotel được thiết kế theo lối kiến trúc Tân cổ điển trang nhã, gam màu ấm áp phối cùng sàn gỗ cao cấp. Không gian tràn ngập ánh sáng tự nhiên từ cửa sổ lớn, đem lại sự thư thái tuyệt đối sau ngày dài khám phá Vịnh Hạ Long.',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg',
                'gallery'     => array(
                    'https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg',
                    'https://dragonlegendhotel.com/wp-content/uploads/2019/05/Superior-6-e1567590038520.jpg',
                    'https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg'
                ),
                'price'          => 950000,
                'original_price' => 1350000,
                'size'           => '28 - 32 m²',
                'bed'            => '1 Giường đôi King hoặc 2 Giường đơn',
                'occupancy'      => '2 Người lớn + 1 Trẻ em',
                'view'           => 'City View & Vườn cây Bãi Cháy',
                'badge'          => 'Phổ biến nhất',
                'rating'         => '4.8',
                'reviews_count'  => 142,
                'amenities'      => array('wifi', 'coffee', 'tv', 'bath', 'snowflake', 'wine', 'shield-check', 'sparkles')
            ),
            array(
                'slug'        => 'deluxe-room',
                'category'    => 'Phòng Deluxe',
                'title'       => 'Deluxe Ocean & Marine View',
                'subtitle'    => 'Tầm nhìn khoáng đạt hướng đại lộ Marine và Vịnh biển kỳ quan',
                'content'     => 'Nâng tầm kỳ nghỉ với hạng phòng Deluxe rộng rãi. Ban công riêng và cửa kính Panorama mở ra quang cảnh đại lộ sôi động cùng làn gió biển Bãi Cháy trong lành. Nội thất gỗ óc chó kết hợp các chi tiết mạ đồng vàng tinh tế mang dấu ấn hoàng gia.',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg',
                'gallery'     => array(
                    'https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg',
                    'https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg',
                    'https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg'
                ),
                'price'          => 1350000,
                'original_price' => 1800000,
                'size'           => '34 - 38 m²',
                'bed'            => '1 Giường đôi King Size (2m x 2m)',
                'occupancy'      => '2 Người lớn + 1 Trẻ em',
                'view'           => 'Hướng Đại Lộ Marine & Góc biển Vịnh',
                'badge'          => 'Bán chạy',
                'rating'         => '4.9',
                'reviews_count'  => 218,
                'amenities'      => array('wifi', 'coffee', 'sun', 'bath', 'tv', 'wine', 'waves', 'sparkles')
            ),
            array(
                'slug'        => 'junior-suite',
                'category'    => 'Junior Suite',
                'title'       => 'Junior Suite Luxury Balcony',
                'subtitle'    => 'Không gian nghỉ dưỡng quý tộc với phòng khách biệt lập sang trọng',
                'content'     => 'Junior Suite được thiết kế cho những kỳ nghỉ trăng mật lãng mạn hay những chuyến công tác cao cấp. Căn suite sở hữu sofa tiếp khách riêng biệt, bồn tắm ngâm thảo mộc kính view vịnh tuyệt đẹp, cùng hệ thống đệm bông ép tiêu chuẩn 5 sao quốc tế giúp du khách có giấc ngủ sâu trọn vẹn.',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg',
                'gallery'     => array(
                    'https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg',
                    'https://dragonlegendhotel.com/wp-content/uploads/2019/05/Superior-6-e1567590038520.jpg',
                    'https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg'
                ),
                'price'          => 1950000,
                'original_price' => 2600000,
                'size'           => '46 - 52 m²',
                'bed'            => '1 Giường Super King Size (2m2)',
                'occupancy'      => '2 Người lớn + 2 Trẻ em',
                'view'           => 'Trực diện Vịnh Hạ Long & Đảo Tuần Châu',
                'badge'          => 'Thượng lưu',
                'rating'         => '5.0',
                'reviews_count'  => 96,
                'amenities'      => array('gem', 'coffee', 'bath', 'wine', 'tv', 'waves', 'car', 'sparkles')
            ),
            array(
                'slug'        => 'family-suite',
                'category'    => 'Family Suite',
                'title'       => 'Family Grand Suite 2-Bedroom',
                'subtitle'    => 'Ngôi nhà ấm cúng lý tưởng cho gia đình đa thế hệ nghỉ dưỡng',
                'content'     => 'Family Grand Suite kết hợp sự riêng tư và gắn kết hoàn hảo cho cả đại gia đình. Gồm 2 không gian phòng ngủ kết nối liên thông, 2 phòng tắm rộng với bồn tắm sục, bàn ăn gia đình và ban công góc ngắm hoàng hôn rực rỡ trên vịnh di sản.',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2019/05/Superior-6-e1567590038520.jpg',
                'gallery'     => array(
                    'https://dragonlegendhotel.com/wp-content/uploads/2019/05/Superior-6-e1567590038520.jpg',
                    'https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg',
                    'https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg'
                ),
                'price'          => 2650000,
                'original_price' => 3500000,
                'size'           => '68 - 75 m²',
                'bed'            => '1 Giường King + 2 Giường đơn (2 phòng ngủ)',
                'occupancy'      => '4 Người lớn + 2 Trẻ em',
                'view'           => 'Toàn cảnh Đảo Rều & Vịnh Bãi Cháy',
                'badge'          => 'Dành cho gia đình',
                'rating'         => '4.9',
                'reviews_count'  => 165,
                'amenities'      => array('wifi', 'coffee', 'bath', 'tv', 'sun', 'waves', 'sparkles')
            ),
        );

        foreach ($rooms as $r) {
            $post_id = wp_insert_post(array(
                'post_type'    => 'dl_room',
                'post_title'   => $r['title'],
                'post_name'    => $r['slug'],
                'post_content' => $r['content'],
                'post_excerpt' => $r['subtitle'],
                'post_status'  => 'publish',
            ));

            if ($post_id && !is_wp_error($post_id)) {
                // Gán Taxonomy Loại phòng
                wp_set_object_terms($post_id, $r['category'], 'dl_room_type', false);

                // Gán Metas
                update_post_meta($post_id, '_dl_room_subtitle', $r['subtitle']);
                update_post_meta($post_id, '_dl_room_price', $r['price']);
                update_post_meta($post_id, '_dl_room_original_price', $r['original_price']);
                update_post_meta($post_id, '_dl_room_size', $r['size']);
                update_post_meta($post_id, '_dl_room_bed', $r['bed']);
                update_post_meta($post_id, '_dl_room_occupancy', $r['occupancy']);
                update_post_meta($post_id, '_dl_room_view', $r['view']);
                update_post_meta($post_id, '_dl_room_badge', $r['badge']);
                update_post_meta($post_id, '_dl_room_rating', $r['rating']);
                update_post_meta($post_id, '_dl_room_reviews_count', $r['reviews_count']);
                update_post_meta($post_id, '_dl_room_amenities', $r['amenities']);
                update_post_meta($post_id, '_dl_room_gallery', $r['gallery']);
                update_post_meta($post_id, '_dl_room_sample_image', $r['image']);
            }
        }
    }

    /**
     * Khởi tạo gói ưu đãi mẫu (Đầy đủ nội dung từ chuyên mục Khuyến Mãi)
     */
    public static function import_offers() {
        $offers = array(
            array(
                'title'       => 'Summer Deal – Combo 2 ngày 1 đêm Bãi Cháy',
                'slug'        => 'summer-deal-combo-2n1d-bai-chay',
                'tag'         => 'Combo Nghỉ Dưỡng 4 Sao',
                'badge'       => 'Chỉ 690K / Người',
                'price_text'  => 'Chỉ 690.000 VNĐ / người (cuối tuần, Lễ Tết phụ thu)',
                'expiry'      => 'Áp dụng trong mùa hè (Số lượng có hạn)',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/fsdffsdfsd.jpg',
                'excerpt'     => 'Chỉ 690.000 VNĐ/người (cuối tuần, Lễ Tết phụ thu) – Combo Nghỉ Dưỡng 4 Sao Dragon Legend Hạ Long trọn gói phòng nghỉ, Buffet sáng, 01 bữa chính Set Menu đặc sản biển và tiện ích bể bơi, phòng Gym.',
                'benefits'    => "01 đêm lưu trú tại hạng phòng tiêu chuẩn 4 sao hiện đại\n01 bữa Buffet sáng đa dạng từ món Á đến món Âu\n01 Bữa chính (Trưa hoặc Tối) Set Menu đặc sản biển Hạ Long\nMiễn phí sử dụng bể bơi bốn mùa & phòng tập Gym hiện đại\nMiễn phí cho 02 trẻ em dưới 6 tuổi (ngủ chung giường bố mẹ)\nVị trí vàng chỉ 5 phút tới biển Bãi Cháy, Sun World, Cảng tàu",
                'content'     => '<p>Bạn đang lên kế hoạch cho chuyến vi vu Hạ Long nhưng còn lăn tăn về chi phí? Bạn muốn tìm một không gian nghỉ dưỡng đẳng cấp 4 sao, đầy đủ tiện nghi mà giá vẫn “hạt dẻ”? Đừng bỏ qua bài viết này vì <strong>Dragon Legend Hotel</strong> đang tung ra gói combo 2N1Đ cực hời chỉ với <strong>690.000đ/người</strong> (cuối tuần, Lễ Tết phụ thu)!</p>
<h2 id="tai-sao-dragon-legend-ha-long-hotel-la-lua-chon-hang-dau">1. Tại sao Dragon Legend Hạ Long Hotel là lựa chọn hàng đầu?</h2>
<p>Tọa lạc ngay tại Đại lộ Hạ Long Marine – Bãi Cháy, <strong>Dragon Legend Hotel</strong> không chỉ sở hữu lối kiến trúc tân cổ điển sang trọng mà còn mang đến dịch vụ và tầm nhìn tuyệt đẹp. Đây là điểm dừng chân lý tưởng để bạn dễ dàng di chuyển đến Công viên Sun World, cảng tàu du lịch hay các khu phố ẩm thực, vui chơi sầm uất.</p>
<h2 id="chi-tiet-combo-pha-dao-ha-long-chi-690-000d-khach">2. Chi tiết Combo “Phá đảo Hạ Long” chỉ 690.000đ/khách</h2>
<p>Chỉ với <strong>690.000 đồng mỗi người</strong>, bạn sẽ nhận được trọn gói dịch vụ nghỉ dưỡng tiêu chuẩn 4 sao bao gồm:</p>
<ul>
<li><strong>Phòng nghỉ hiện đại:</strong> 01 đêm lưu trú tại hạng phòng tiêu chuẩn với đầy đủ trang thiết bị, không gian ấm cúng và tinh tế.</li>
<li><strong>Buffet sáng:</strong> Thực đơn đa dạng từ món Á đến món Âu, tiếp năng lượng cho ngày dài khám phá.</li>
<li><strong>01 Bữa chính (Trưa hoặc Tối):</strong> Thưởng thức Set Menu được thiết kế riêng bởi các đầu bếp chuyên nghiệp, mang đậm hương vị đặc sản biển Hạ Long.</li>
<li><strong>Tiện ích miễn phí:</strong> Thoải mái check-in và thư giãn tại <strong>bể bơi</strong> cực chill; duy trì vóc dáng tại phòng <strong>Gym</strong> hiện đại của khách sạn.</li>
<li><strong>Miễn phí cho 02 trẻ em dưới 6 tuổi</strong> (ngủ chung giường với bố mẹ) – một điểm cộng cực lớn cho các gia đình đi du lịch Hạ Long hè này.</li>
<li><strong>Vị trí vàng:</strong> Chỉ 5 phút di chuyển tới bãi biển Bãi Cháy, Sun World, cảng Du thuyền, chợ đêm...</li>
</ul>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6">
  <figure><img src="https://dragonlegendhotel.com/wp-content/uploads/2026/02/Twn.Sup-1-scaled.jpg" alt="Phòng nghỉ 4 sao Dragon Legend Hotel" /><figcaption>Không gian phòng nghỉ 4 sao ấm cúng, tinh tế</figcaption></figure>
  <figure><img src="https://dragonlegendhotel.com/wp-content/uploads/2026/02/NT-356-scaled.jpg" alt="Hồ bơi Dragon Legend Hotel" /><figcaption>Hồ bơi thư giãn tại khách sạn</figcaption></figure>
  <figure><img src="https://dragonlegendhotel.com/wp-content/uploads/2026/02/IMG_3440-scaled.jpg" alt="Phòng tập Gym Dragon Legend Hotel" /><figcaption>Phòng tập Gym trang bị hiện đại</figcaption></figure>
  <figure><img src="https://dragonlegendhotel.com/wp-content/uploads/2026/02/Rest-6-scaled.jpg" alt="Nhà hàng ẩm thực Dragon Legend Hotel" /><figcaption>Không gian nhà hàng phục vụ Buffet &amp; Set Menu</figcaption></figure>
</div>
<h2 id="uu-dai-dat-som-chi-690-000-nguoi">3. Ưu Đãi Đặt Sớm – Chỉ 690.000đ/người</h2>
<p>Quên đi cái nắng oi ả, hãy để <strong>Dragon Legend Hotel</strong> trở thành “người kể chuyện” cho chuyến hành trình giải nhiệt mùa hè rực rỡ của bạn tại Hạ Long. Một kỳ nghỉ 4 sao đúng nghĩa với hồ bơi cực chill và những bữa ăn ngon tuyệt đang chờ đón cả gia đình.</p>
<p>Vì số lượng phòng ưu đãi cho gói combo 690K có hạn và thường “hết phòng” vào những ngày cao điểm hè, quý khách vui lòng liên hệ với chúng tôi ngay hôm nay để được tư vấn và giữ chỗ tốt nhất.</p>
<ul>
<li><strong>Hotline:</strong> +84 203 3518 999</li>
<li><strong>Địa chỉ:</strong> Đại lộ Hạ Long Marine, Bãi Cháy, Hạ Long, Quảng Ninh.</li>
<li><strong>Website:</strong> Dragon Legend Hotel</li>
</ul>'
            ),
            array(
                'title'       => 'ƯU ĐÃI ĐẶT PHÒNG SỚM – GIẢM NGAY 20%',
                'slug'        => 'early-bird-dat-som-giam-ngay-20',
                'tag'         => 'Tiết Kiệm Thông Minh',
                'badge'       => 'Giảm Ngay 20%',
                'price_text'  => 'Giảm trực tiếp 20% trên giá phòng công bố',
                'expiry'      => 'Đặt trước ngày khởi hành ít nhất 15 – 30 ngày',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/Untitled-1-1.jpg',
                'excerpt'     => 'GIẢM NGAY 20% PHÒNG NGHỈ TẠI DRAGON LEGEND HOTEL HẠ LONG – Chương trình ưu đãi “Early Bird – Đặt Càng Sớm, Giảm Càng Sâu” với mức chiết khấu lên đến 20% khi đặt trước từ 15–30 ngày.',
                'benefits'    => "Giảm trực tiếp 20% trên giá phòng công bố\nÁp dụng cho booking đặt trước khởi hành ít nhất 15 – 30 ngày\nÁp dụng đa dạng từ hạng phòng Superior đến Suite sang trọng\nMiễn phí hồ bơi bốn mùa trong nhà và phòng tập Gym hiện đại",
                'content'     => '<p>Bạn đã có kế hoạch cho chuyến du lịch Hạ Long sắp tới nhưng vẫn đang cân nhắc về ngân sách? Đừng bỏ lỡ cơ hội vàng để tận hưởng không gian nghỉ dưỡng 4 sao đẳng cấp với mức giá “siêu hời”. <strong>Dragon Legend Hotel</strong> chính thức tung chương trình ưu đãi: <strong>“Early Bird – Đặt Càng Sớm, Giảm Càng Sâu”</strong> với mức chiết khấu lên đến <strong>20%</strong>.</p>
<h2 id="uu-dai-20-dac-quyen-danh-rieng-cho-khach-hang-dat-som">1. Ưu đãi 20% – Đặc quyền dành riêng cho khách hàng đặt sớm</h2>
<p>Chúng tôi hiểu rằng một chuyến đi hoàn hảo bắt đầu từ sự chuẩn bị kỹ lưỡng. Chương trình ưu đãi 20% giá phòng được thiết kế để tri ân những du khách có kế hoạch sớm, giúp bạn tiết kiệm chi phí mà vẫn đảm bảo được những dịch vụ chất lượng nhất.</p>
<ul>
<li><strong>Mức giảm giá:</strong> Trực tiếp <strong>20%</strong> trên giá phòng công bố.</li>
<li><strong>Thời hạn áp dụng:</strong> Dành cho các booking đặt trước ngày khởi hành ít nhất 15–30 ngày (tùy thời điểm).</li>
<li><strong>Hạng phòng áp dụng:</strong> Đa dạng từ Superior hiện đại đến Suite sang trọng.</li>
</ul>
<h2 id="tai-sao-nen-chon-nghi-duong-tai-dragon-legend-hotel">2. Tại sao nên chọn nghỉ dưỡng tại Dragon Legend Hotel?</h2>
<p>Không chỉ dừng lại ở mức giá hấp dẫn, Dragon Legend Hotel còn là điểm dừng chân lý tưởng bởi những giá trị trải nghiệm vượt trội:</p>
<ul>
<li><strong>Vị trí “vàng” tại Bãi Cháy:</strong> Nằm ngay tại đại lộ Hạ Long Marine, gần chợ hải sản Cái Dăm. Chỉ 5 phút di chuyển ra Sun World Hạ Long và Cảng Du thuyền.</li>
<li><strong>Tiện ích 4 sao hoàn hảo:</strong> Thư giãn tại hồ bơi trong nhà bốn mùa, tái tạo năng lượng, duy trì vóc dáng tại phòng Gym hiện đại.</li>
<li><strong>Ẩm thực tinh tế:</strong> Hệ thống nhà hàng phục vụ từ Buffet sáng đa dạng đến các bữa tiệc hải sản tươi ngon đặc trưng của vùng biển Quảng Ninh.</li>
</ul>
<h2 id="bi-kip-de-nhan-uu-dai-20-nhanh-nhat">3. Bí kíp để nhận ưu đãi 20% nhanh nhất</h2>
<p>Để chắc chắn sở hữu căn phòng có tầm nhìn đẹp nhất với mức giá giảm 20%, bạn chỉ cần thực hiện các bước đơn giản sau:</p>
<ol>
<li><strong>Lên kế hoạch sớm:</strong> Xác định ngày đi và số lượng thành viên.</li>
<li><strong>Đặt phòng trực tiếp:</strong> Liên hệ qua Fanpage, Website hoặc Hotline để được áp dụng mã giảm giá sớm nhất.</li>
<li><strong>Tận hưởng kỳ nghỉ:</strong> Check-in và tận hưởng không gian “Viên ngọc giữa lòng di sản” với chi phí cực kỳ tối ưu.</li>
</ol>
<h2 id="thong-tin-lien-he-va-dat-phong">4. Thông tin liên hệ và đặt phòng</h2>
<p>Đừng để kỳ nghỉ trong mơ trôi qua vì chần chừ! Hãy đặt phòng ngay hôm nay để nhận mức ưu đãi thấp nhất năm.</p>
<ul>
<li><strong>Địa chỉ:</strong> Đại lộ Hạ Long Marine, Bãi Cháy, Hạ Long, Quảng Ninh.</li>
<li><strong>Hotline tư vấn 24/7:</strong> +84 203 3518 999</li>
<li><strong>Website:</strong> dragonlegendhotel.com</li>
<li><strong>Email:</strong> info@dragonlegendhotel.com</li>
</ul>
<blockquote><p><strong>Dragon Legend Hotel – Đẳng cấp nghỉ dưỡng, giá trị thực tâm!</strong><br /><em>Lưu ý: Chương trình có thể kết thúc sớm khi hết số lượng phòng ưu đãi mỗi ngày. Quý khách vui lòng liên hệ sớm để được hỗ trợ tốt nhất.</em></p></blockquote>'
            ),
            array(
                'title'       => 'ƯU ĐÃI CHO MÙA SỰ KIỆN: HỘI NGHỊ, SỰ KIỆN, GALA',
                'slug'        => 'goi-su-kien-hoi-nghi-tiec-gala-dinh-cao',
                'tag'         => 'Khách Đoàn & Doanh Nghiệp',
                'badge'       => 'Dành Cho MICE',
                'price_text'  => 'Chiết khấu sâu khách đoàn & Combo MICE trọn gói',
                'expiry'      => 'Ưu đãi theo đoàn thể & doanh nghiệp',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/491753343_1190446813091492_8232416561654215596_n.jpg',
                'excerpt'     => 'Ưu đãi ngập tràn cho mùa sự kiện. Nếu bạn đang lên kế hoạch cho đoàn đông hoặc hội thảo trọn gói, hãy chọn Dragon Legend Hotel để tận hưởng mức giá ưu đãi cùng dịch vụ 4 sao tiêu chuẩn quốc tế.',
                'benefits'    => "Giá phòng ưu đãi chiết khấu sâu cho đơn đặt phòng số lượng lớn (khách đoàn)\nGói trọn gói (Combo MICE): Phòng nghỉ, phòng họp và tiệc trà (Tea-break)\nHỗ trợ kỹ thuật 24/7 cùng hệ thống âm thanh, ánh sáng, màn hình LED quốc tế\nThực đơn tiệc đa dạng từ Tea-break đến tiệc tối Buffet / Set Menu đẳng cấp",
                'content'     => '<p>Ưu đãi ngập tràn cho mùa sự kiện. Nếu bạn đang lên kế hoạch cho đoàn đông hoặc hội thảo trọn gói, hãy chọn <strong>Dragon Legend Hotel</strong> để tận hưởng mức giá ưu đãi cùng dịch vụ 4 sao tiêu chuẩn quốc tế.</p>
<p>Bạn đang tìm kiếm một địa điểm đẳng cấp để tổ chức hội thảo, tọa đàm hay các sự kiện doanh nghiệp quan trọng? Với hệ thống hạ tầng hiện đại cùng vị trí đắc địa tại tâm điểm du lịch Bãi Cháy, <strong>Dragon Legend Hotel</strong> tự hào là điểm đến hàng đầu, mang lại sự khởi đầu hoàn hảo cho mọi thành công của quý đối tác.</p>
<h2 id="khong-gian-su-kien-chuyen-nghiep-dang-cap-4-sao">1. Không gian sự kiện chuyên nghiệp – Đẳng cấp 4 sao</h2>
<p>Tại Dragon Legend Hotel, chúng tôi hiểu rằng không gian là yếu tố then chốt tạo nên vị thế của một sự kiện. Khách sạn sở hữu hệ thống phòng hội nghị bố trí linh hoạt:</p>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 my-6">
  <figure><img src="https://dragonlegendhotel.com/wp-content/uploads/2026/02/z7329380153803_e8304ee99db6c114f1807c4cad792c68.jpg" alt="Phòng chữ U Dragon Legend Hotel" /><figcaption><strong>Phòng chữ U</strong></figcaption></figure>
  <figure><img src="https://dragonlegendhotel.com/wp-content/uploads/2026/02/SN4.jpg" alt="Phòng tiệc Dragon Legend Hotel" /><figcaption><strong>Phòng tiệc</strong></figcaption></figure>
  <figure><img src="https://dragonlegendhotel.com/wp-content/uploads/2026/02/z7329375811687_a4845ebec7a76238b15941b858963497.jpg" alt="Phòng Class Dragon Legend Hotel" /><figcaption><strong>Phòng Class</strong></figcaption></figure>
</div>
<ul>
<li><strong>Phòng hội nghị đa năng:</strong> Sức chứa linh hoạt, phù hợp cho cả những buổi họp kín quy mô nhỏ đến các hội thảo lên tới hàng trăm khách.</li>
<li><strong>Trang thiết bị hiện đại:</strong> Hệ thống âm thanh, ánh sáng tiêu chuẩn quốc tế, màn hình LED sắc nét và Wi-Fi tốc độ cao phủ sóng toàn diện.</li>
<li><strong>Thiết kế tinh tế:</strong> Không gian được bài trí sang trọng, tối ưu công năng, tạo cảm giác chuyên nghiệp và thoải mái cho người tham dự.</li>
</ul>
<h2 id="dich-vu-luu-tru-ly-tuong-cho-khach-doan">2. Dịch vụ lưu trú lý tưởng cho khách đoàn</h2>
<p>Bên cạnh khu vực hội họp, Dragon Legend Hotel cung cấp hệ thống <strong>138 phòng nghỉ</strong> sang trọng với tầm nhìn hướng vịnh kỳ quan hoặc thành phố nhộn nhịp. Đây là lựa chọn lưu trú tuyệt vời cho các đoàn khách MICE (Du lịch kết hợp hội nghị), giúp các thành viên có thời gian nghỉ ngơi, tái tạo năng lượng sau những giờ làm việc căng thẳng.</p>
<figure><img src="https://dragonlegendhotel.com/wp-content/uploads/2026/02/J.Suite-ok.jpg" alt="Phòng nghỉ sang trọng cho khách đoàn MICE tại Dragon Legend Hotel" /><figcaption>Hệ thống 138 phòng nghỉ chuẩn 4 sao phục vụ đoàn khách MICE</figcaption></figure>
<h2 id="chuong-trinh-uu-dai-dac-biet-danh-cho-doanh-nghiep">3. Chương trình ưu đãi đặc biệt dành cho doanh nghiệp</h2>
<p>Để đồng hành cùng quý khách hàng trong mùa sự kiện, Dragon Legend Hotel triển khai gói ưu đãi hấp dẫn:</p>
<ul>
<li><strong>Giá phòng ưu đãi:</strong> Chiết khấu sâu cho các đơn đặt phòng số lượng lớn (khách đoàn).</li>
<li><strong>Gói trọn gói (Combo MICE):</strong> Bao gồm phòng nghỉ, phòng họp và tiệc trà (Tea-break) với mức giá cạnh tranh nhất khu vực.</li>
<li><strong>Hỗ trợ kỹ thuật 24/7:</strong> Đội ngũ nhân viên giàu kinh nghiệm luôn túc trực, đảm bảo sự kiện diễn ra suôn sẻ, không sai sót.</li>
<li><strong>Thực đơn tiệc đa dạng:</strong> Từ tiệc trà nhẹ nhàng đến tiệc tối buffet/set menu đẳng cấp, đáp ứng mọi khẩu vị của quan khách.</li>
</ul>
<figure><img src="https://dragonlegendhotel.com/wp-content/uploads/2026/02/491831841_1190446553091518_6026410407309418018_n.jpg" alt="Sự kiện hội nghị và Gala tại Dragon Legend Hotel" /><figcaption>Không gian hội nghị &amp; sự kiện chuyên nghiệp tại Dragon Legend Hotel</figcaption></figure>'
            ),
        );

        foreach ($offers as $o) {
            $existing = !empty($o['slug']) ? get_page_by_path($o['slug'], OBJECT, 'dl_offer') : null;
            $post_arr = array(
                'post_type'    => 'dl_offer',
                'post_title'   => wp_slash($o['title']),
                'post_name'    => $o['slug'] ?? sanitize_title($o['title']),
                'post_content' => wp_slash($o['content']),
                'post_excerpt' => wp_slash($o['excerpt'] ?? $o['content']),
                'post_status'  => 'publish',
            );
            if ($existing && isset($existing->ID)) {
                $post_arr['ID'] = $existing->ID;
                $post_id = wp_update_post($post_arr);
            } else {
                $post_id = wp_insert_post($post_arr);
            }

            if ($post_id && !is_wp_error($post_id)) {
                update_post_meta($post_id, '_dl_offer_tag', $o['tag']);
                update_post_meta($post_id, '_dl_offer_badge', $o['badge']);
                update_post_meta($post_id, '_dl_offer_price_text', $o['price_text']);
                update_post_meta($post_id, '_dl_offer_expiry', $o['expiry']);
                update_post_meta($post_id, '_dl_offer_benefits', $o['benefits']);
                update_post_meta($post_id, '_dl_offer_sample_image', $o['image']);
            }
        }
    }

    /**
     * Khởi tạo dịch vụ mẫu
     */
    public static function import_amenities() {
        $amenities = array(
            array(
                'title'       => 'Hồ Bơi Vô Cực & Sky Fitness',
                'location'    => 'Tầng 12 - Tầng cao nhất',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/DJI_0081-HDR.jpg',
                'content'     => 'Đắm mình trong làn nước trong vắt tại hồ bơi vô cực ngắm trọn vẹn vịnh Hạ Long kỳ vĩ từ tầng 12. Phòng tập Gym trang bị máy móc hiện đại chuẩn LifeFitness, mở cửa từ 06:00 đến 22:00.',
                'highlights'  => "Tầm nhìn Panorama 360° bao trọn vịnh Bãi Cháy\nNước ấm bốn mùa thư giãn\nQuầy Sky Bar phục vụ cocktail và đồ uống nhiệt đới",
                'icon'        => 'pool'
            ),
            array(
                'title'       => 'Nhà Hàng Ẩm Thực Legend Gourmet',
                'location'    => 'Tầng 2 & Tầng M',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2019/08/z7329376216471_19994c0c3cb3524cbd0e2b56078cd8ab.jpg',
                'content'     => 'Hành trình vị giác thăng hoa với sự kết hợp tinh hoa giữa ẩm thực biển Hạ Long tươi sống và các món ăn Á - Âu đặc sắc chế biến bởi đội ngũ đầu bếp giàu kinh nghiệm.',
                'highlights'  => "Buffet sáng hơn 60 món Á - Âu đa dạng\nHải sản tươi sống chọn trực tiếp tại bể\nPhòng ăn VIP riêng tư sang trọng chuẩn hoàng gia",
                'icon'        => 'restaurant'
            ),
            array(
                'title'       => 'Trung Tâm Hội Nghị & Tiệc Cưới Grand Ballroom',
                'location'    => 'Tầng 3',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/BQ5.jpg',
                'content'     => 'Không gian đại tiệc tráng lệ không cột với trần cao 5.5m, đèn chùm pha lê tân cổ điển sang trọng, màn hình LED cong cực lớn và công nghệ âm thanh vòm đỉnh cao.',
                'highlights'  => "Sức chứa lên tới 350 khách tham dự\nĐội ngũ Event Planner chuyên nghiệp đồng hành\nHệ thống vách ngăn cách âm thông minh linh hoạt",
                'icon'        => 'ballroom'
            ),
            array(
                'title'       => 'Lotus Spa & Chăm Sóc Sức Khỏe',
                'location'    => 'Tầng 11',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg',
                'content'     => 'Không gian thanh tịnh thơm ngát hương tinh dầu thảo mộc và sen hồng. Liệu trình massage truyền thống kết hợp xông hơi đá muối Himalaya giúp tái tạo năng lượng hoàn hảo.',
                'highlights'  => "Trị liệu bấm huyệt chuyên sâu phục hồi thể lực\nPhòng xông hơi đá muối khô & ướt tiêu chuẩn\nSản phẩm chăm sóc da thảo mộc 100% hữu cơ",
                'icon'        => 'lotus-spa'
            ),
        );

        foreach ($amenities as $a) {
            $post_id = wp_insert_post(array(
                'post_type'    => 'dl_amenity',
                'post_title'   => $a['title'],
                'post_content' => $a['content'],
                'post_status'  => 'publish',
            ));

            if ($post_id && !is_wp_error($post_id)) {
                update_post_meta($post_id, '_dl_amenity_location', $a['location']);
                update_post_meta($post_id, '_dl_amenity_highlights', $a['highlights']);
                update_post_meta($post_id, '_dl_amenity_icon', $a['icon']);
                update_post_meta($post_id, '_dl_amenity_sample_image', $a['image']);
            }
        }
    }

    /**
     * Khởi tạo đánh giá mẫu
     */
    public static function import_testimonials() {
        $reviews = array(
            array(
                'title'    => 'Dennis Ellul',
                'location' => 'Canada',
                'rating'   => 5,
                'date'     => 'Tháng 2, 2026',
                'quote'    => 'Vượt qua sự mong đợi của tôi!',
                'content'  => 'Stopped for three nights at this hotel and was very impressed with the service, the food, and cleanness. The staff was incredibly helpful, especially Ngoc at reception and Huong at the restaurant. Highly recommend this hotel to anyone visiting Ha Long Bay!',
                'avatar'   => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/408634589_10226615376023160_1198021278240416737_n.jpg'
            ),
            array(
                'title'    => 'Ms. Thúy Nga',
                'location' => 'Nam Định',
                'rating'   => 5,
                'date'     => 'Tháng 1, 2026',
                'quote'    => 'Khách sạn tiêu chuẩn xịn xò, nhân viên rất chu đáo',
                'content'  => 'Mình vừa cùng gia đình nghỉ tại Dragon Legend tuần trước. Trải nghiệm rất trọn vẹn, phòng ốc sạch sẽ thơm tho, đồ ăn sáng phong phú và rất vừa miệng. Các bạn nhân viên luôn tươi cười đón tiếp. Chắc chắn sẽ quay lại vào dịp hè!',
                'avatar'   => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/557370981_4220393851610917_766684084316907759_n-1.jpg'
            ),
            array(
                'title'    => 'Anh Chung',
                'location' => 'Hà Nội',
                'rating'   => 5,
                'date'     => 'Tháng 2, 2026',
                'quote'    => 'Vị trí đẹp, tầng 12 ngắm hoàng hôn cực phẩm',
                'content'  => 'Nhà mình đi du lịch 3 ngày 2 đêm ở đây. Vị trí trên đại lộ Marine rất thuận tiện đi dạo, ăn uống và đi sang Sun World. Hồ bơi tầng 12 view vịnh chụp ảnh sống ảo tuyệt đẹp. Chất lượng 4 sao nhưng giá cực kỳ hợp lý.',
                'avatar'   => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/558781329_1495380595087742_988380832516943251_n.jpg'
            ),
            array(
                'title'    => 'Phương Nam',
                'location' => 'Hải Phòng',
                'rating'   => 5,
                'date'     => 'Tháng 2, 2026',
                'quote'    => 'Điểm lưu trú lý tưởng khi đến Hạ Long',
                'content'  => 'Dù ở Hải Phòng sang Hạ Long nhiều lần nhưng đây là lần đầu tôi ở Dragon Legend Hotel. Phong cách thiết kế tân cổ điển rất sang trọng, giường đệm ngủ rất êm không bị đau lưng. Dịch vụ chuyên nghiệp đáng khen ngợi.',
                'avatar'   => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/600328087_4307218056261829_8379978694470017721_n.jpg'
            )
        );

        foreach ($reviews as $rev) {
            $post_id = wp_insert_post(array(
                'post_type'    => 'dl_testimonial',
                'post_title'   => $rev['title'],
                'post_content' => $rev['content'],
                'post_status'  => 'publish',
            ));

            if ($post_id && !is_wp_error($post_id)) {
                update_post_meta($post_id, '_dl_testimonial_location', $rev['location']);
                update_post_meta($post_id, '_dl_testimonial_rating', $rev['rating']);
                update_post_meta($post_id, '_dl_testimonial_date', $rev['date']);
                update_post_meta($post_id, '_dl_testimonial_title', $rev['quote']);
                update_post_meta($post_id, '_dl_testimonial_sample_avatar', $rev['avatar']);
            }
        }
    }
}
