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
                'category'    => 'Phòng Deluxe Ocean',
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
     * Khởi tạo gói ưu đãi mẫu
     */
    public static function import_offers() {
        $offers = array(
            array(
                'title'       => 'Summer Deal – Combo 2N1Đ Bãi Cháy',
                'tag'         => 'Ưu đãi mùa hè',
                'badge'       => 'Best Seller',
                'price_text'  => 'Chỉ từ 690.000 VNĐ / Khách',
                'expiry'      => 'Áp dụng đến hết 31/10/2026',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/fsdffsdfsd-658x492.jpg',
                'content'     => 'Gói nghỉ dưỡng 4 sao trọn gói bao gồm 01 đêm phòng Superior sang trọng, buffet sáng hảo hạng, check-in hồ bơi vô cực tầng 12 và trà chiều thư giãn ngắm vịnh.',
                'benefits'    => "01 đêm phòng hạng Superior hoặc Deluxe\nBữa sáng Buffet Quốc tế hơn 50 món\nMiễn phí sử dụng Sky Pool & Fitness tầng 12\nTặng Voucher giảm 10% tại nhà hàng Dragon Cuisine"
            ),
            array(
                'title'       => 'Early Bird – Đặt Sớm Giảm Ngay 20%',
                'tag'         => 'Tiết kiệm thông minh',
                'badge'       => 'Giảm 20%',
                'price_text'  => 'Tiết kiệm đến 600.000 VNĐ / đêm',
                'expiry'      => 'Áp dụng quanh năm',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/Untitled-1-1-657x492.jpg',
                'content'     => 'Lên kế hoạch sớm cho chuyến du ngoạn Hạ Long để nhận ngay mức giá ưu đãi đặc biệt giảm trực tiếp 20% cho tất cả các hạng phòng khi đặt trước 14 ngày.',
                'benefits'    => "Giảm 20% trên giá niêm yết các hạng phòng\nLinh hoạt thay đổi ngày lưu trú miễn phí 1 lần\nNước uống hoa quả & khăn lạnh đón tiếp\nƯu tiên nâng hạng phòng tùy theo tình trạng phòng"
            ),
            array(
                'title'       => 'Gói Sự Kiện – Hội Nghị & Tiệc Gala Đỉnh Cao',
                'tag'         => 'Khách đoàn & Doanh nghiệp',
                'badge'       => 'Dành cho MICE',
                'price_text'  => 'Trọn gói từ 450.000 VNĐ / Suất tiệc',
                'expiry'      => 'Ưu đãi theo đoàn thể',
                'image'       => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/491753343_1190446813091492_8232416561654215596_n-738x492.jpg',
                'content'     => 'Giải pháp trọn vẹn cho chương trình hội thảo, team building, tri ân khách hàng và tiệc Gala cuối năm với hội trường hiện đại sức chứa tới 300 khách.',
                'benefits'    => "Miễn phí phòng họp với màn hình LED P3 hiện đại\nHệ thống âm thanh ánh sáng sân khấu biểu diễn chuẩn\nThực đơn tiệc 8 món hải sản Hạ Long cao cấp\nTặng gói Teabreak trà bánh sang trọng giữa giờ"
            ),
        );

        foreach ($offers as $o) {
            $post_id = wp_insert_post(array(
                'post_type'    => 'dl_offer',
                'post_title'   => $o['title'],
                'post_content' => $o['content'],
                'post_excerpt' => $o['content'],
                'post_status'  => 'publish',
            ));

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
