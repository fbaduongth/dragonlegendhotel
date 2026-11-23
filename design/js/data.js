/**
 * Dragon Legend Hotel - Data Module
 * Thông tin phòng, dịch vụ, ưu đãi và đánh giá thực tế
 */

const HOTEL_INFO = {
  name: "Dragon Legend Hotel Hạ Long",
  stars: 4,
  headline: "Viên Ngọc Rực Rỡ Giữa Lòng Kỳ Quan",
  slogan: "Nghỉ Dưỡng Thượng Lưu - Phong Cách Tân Cổ Điển",
  address: "Đại lộ Hạ Long Marine, Phường Bãi Cháy, TP. Hạ Long, Tỉnh Quảng Ninh",
  hotline: "(+84) 203 3518 999",
  hotlineDisplay: "0203 3518 999",
  mobilePhone: "0968 123 456",
  email: "info@dragonlegendhotel.com",
  bookingEmail: "reservation@dragonlegendhotel.com",
  checkinTime: "14:00",
  checkoutTime: "12:00",
  totalRooms: 138,
  vrUrl: "https://dragonlegendhotel.com/vr/360.html",
  mapEmbedUrl: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6166.100507616104!2d107.0201151!3d20.9582273!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314a598bb174745d%3A0x207cc82c86dc2d25!2sDragon%20Legend%20H%E1%BA%A1%20Long%20Hotel!5e1!3m2!1svi!2s!4v1769987129773!5m2!1svi!2s"
};

const POPULAR_AMENITIES = [
  { id: "airport-shuttle", name: "Xe đưa đón sân bay", icon: "shuttle", desc: "Hỗ trợ đưa đón sân bay Cát Bi & Vân Đồn tiện lợi" },
  { id: "non-smoking", name: "Phòng không hút thuốc", icon: "no-smoking", desc: "Không khí trong lành, khử khuẩn định kỳ" },
  { id: "free-wifi", name: "WiFi miễn phí tốc độ cao", icon: "wifi", desc: "Độ phủ sóng 100% trong phòng và toàn khách sạn" },
  { id: "room-service", name: "Dịch vụ phòng chu đáo", icon: "concierge", desc: "Phục vụ ẩm thực và dọn dẹp theo yêu cầu 24/7" },
  { id: "family-rooms", name: "Phòng gia đình tiện nghi", icon: "family", desc: "Không gian rộng rãi, giường phụ cho trẻ em" },
  { id: "restaurant", name: "Nhà hàng ẩm thực Á - Âu", icon: "restaurant", desc: "Hải sản tươi sống và tiệc buffet phong phú" },
  { id: "front-desk", name: "Lễ tân phục vụ 24 giờ", icon: "reception", desc: "Nhận/trả phòng linh hoạt, hỗ trợ tour vịnh" },
  { id: "elevator", name: "Thang máy hiện đại", icon: "elevator", desc: "Hệ thống thang máy thẻ từ an ninh cao" },
  { id: "air-conditioner", name: "Điều hòa nhiệt độ 2 chiều", icon: "snowflake", desc: "Vận hành êm dịu, tùy chỉnh theo sở thích" },
  { id: "breakfast", name: "Bữa sáng buffet thơm ngon", icon: "coffee", desc: "Hơn 50 món phong phú từ truyền thống đến quốc tế" },
  { id: "sky-pool", name: "Hồ bơi vô cực tầng 12", icon: "pool", desc: "Tầm nhìn bao trọn toàn cảnh Vịnh Hạ Long" },
  { id: "free-parking", name: "Bãi đỗ xe miễn phí", icon: "parking", desc: "Khuôn viên đậu xe ô tô rộng rãi, an ninh 24/7" }
];

const ROOMS_DATA = [
  {
    id: "superior-room",
    category: "superior",
    name: "Superior Double / Twin Room",
    subtitle: "Thanh lịch, ấm cúng và đầy đủ tiện nghi tiêu chuẩn 4 sao",
    image: "https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg",
    gallery: [
      "https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg",
      "https://dragonlegendhotel.com/wp-content/uploads/2019/05/Superior-6-e1567590038520.jpg",
      "https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg"
    ],
    price: 950000,
    originalPrice: 1350000,
    size: "28 - 32 m²",
    bed: "1 Giường đôi King hoặc 2 Giường đơn",
    occupancy: "2 Người lớn + 1 Trẻ em",
    view: "City View & Vườn cây Bãi Cháy",
    badge: "Phổ biến nhất",
    rating: 4.8,
    reviewsCount: 142,
    description: "Phòng Superior tại Dragon Legend Hotel được thiết kế theo lối kiến trúc Tân cổ điển trang nhã, gam màu ấm áp phối cùng sàn gỗ cao cấp. Không gian tràn ngập ánh sáng tự nhiên từ cửa sổ lớn, đem lại sự thư thái tuyệt đối sau ngày dài khám phá Vịnh Hạ Long.",
    amenities: [
      { icon: "wifi", text: "Wifi tốc độ cao miễn phí" },
      { icon: "coffee", text: "Buffet sáng Quốc tế miễn phí" },
      { icon: "tv", text: "Smart TV 50 inch 4K" },
      { icon: "bath", text: "Phòng tắm vòi sen đứng hiện đại" },
      { icon: "snowflake", text: "Điều hòa 2 chiều êm dịu" },
      { icon: "wine", text: "Mini Bar & Trà / Cà phê hảo hạng" },
      { icon: "shield-check", text: "Két sắt an toàn điện tử" },
      { icon: "sparkles", text: "Dịch vụ phòng hằng ngày" }
    ]
  },
  {
    id: "deluxe-room",
    category: "deluxe",
    name: "Deluxe Ocean & Marine View",
    subtitle: "Tầm nhìn khoáng đạt hướng đại lộ Marine và Vịnh biển kỳ quan",
    image: "https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg",
    gallery: [
      "https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg",
      "https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg",
      "https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg"
    ],
    price: 1350000,
    originalPrice: 1800000,
    size: "34 - 38 m²",
    bed: "1 Giường đôi King Size (2m x 2m)",
    occupancy: "2 Người lớn + 1 Trẻ em",
    view: "Hướng Đại Lộ Marine & Góc biển Vịnh",
    badge: "Bán chạy",
    rating: 4.9,
    reviewsCount: 218,
    description: "Nâng tầm kỳ nghỉ với hạng phòng Deluxe rộng rãi. Ban công riêng và cửa kính Panorama mở ra quang cảnh đại lộ sôi động cùng làn gió biển Bãi Cháy trong lành. Nội thất gỗ óc chó kết hợp các chi tiết mạ đồng vàng tinh tế mang dấu ấn hoàng gia.",
    amenities: [
      { icon: "wifi", text: "Wifi cáp quang riêng từng phòng" },
      { icon: "coffee", text: "Buffet sáng phong phú tại nhà hàng" },
      { icon: "sun", text: "Ban công riêng ngắm bình minh" },
      { icon: "bath", text: "Bồn tắm nằm thư giãn & vòi sen" },
      { icon: "tv", text: "Smart TV 55 inch Netflix sẵn sàng" },
      { icon: "wine", text: "Rượu vang chào mừng đặc biệt" },
      { icon: "waves", text: "Miễn phí vé hồ bơi vô cực Sky Pool" },
      { icon: "sparkles", text: "Áo choàng nhung cao cấp & hoa tươi" }
    ]
  },
  {
    id: "junior-suite",
    category: "suite",
    name: "Junior Suite Luxury Balcony",
    subtitle: "Không gian nghỉ dưỡng quý tộc với phòng khách biệt lập sang trọng",
    image: "https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg",
    gallery: [
      "https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg",
      "https://dragonlegendhotel.com/wp-content/uploads/2019/05/Superior-6-e1567590038520.jpg",
      "https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg"
    ],
    price: 1950000,
    originalPrice: 2600000,
    size: "46 - 52 m²",
    bed: "1 Giường Super King Size (2m2)",
    occupancy: "2 Người lớn + 2 Trẻ em",
    view: "Trực diện Vịnh Hạ Long & Đảo Tuần Châu",
    badge: "Thượng lưu",
    rating: 5.0,
    reviewsCount: 96,
    description: "Junior Suite được thiết kế cho những kỳ nghỉ trăng mật lãng mạn hay những chuyến công tác cao cấp. Căn suite sở hữu sofa tiếp khách riêng biệt, bồn tắm ngâm thảo mộc kính view vịnh tuyệt đẹp, cùng hệ thống đệm bông ép tiêu chuẩn 5 sao quốc tế giúp du khách có giấc ngủ sâu trọn vẹn.",
    amenities: [
      { icon: "gem", text: "Đặc quyền check-in ưu tiên tại VIP Lounge" },
      { icon: "coffee", text: "Phục vụ buffet sáng tại phòng nếu yêu cầu" },
      { icon: "bath", text: "Bồn tắm sục Jacuzzi ngắm vịnh" },
      { icon: "wine", text: "Set bánh ngọt & hoa quả nhiệt đới chào đón" },
      { icon: "music", text: "Hệ thống loa Bluetooth Harman Kardon" },
      { icon: "tv", text: "2 Smart TV (Phòng ngủ & Phòng khách)" },
      { icon: "waves", text: "Suất xông hơi đá muối Himalaya miễn phí" },
      { icon: "car", text: "Ưu đãi 15% dịch vụ đưa đón sân bay" }
    ]
  },
  {
    id: "family-suite",
    category: "family",
    name: "Family Grand Suite 2-Bedroom",
    subtitle: "Ngôi nhà ấm cúng lý tưởng cho gia đình đa thế hệ nghỉ dưỡng",
    image: "https://dragonlegendhotel.com/wp-content/uploads/2019/05/Superior-6-e1567590038520.jpg",
    gallery: [
      "https://dragonlegendhotel.com/wp-content/uploads/2019/05/Superior-6-e1567590038520.jpg",
      "https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg",
      "https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg"
    ],
    price: 2650000,
    originalPrice: 3500000,
    size: "68 - 75 m²",
    bed: "1 Giường King + 2 Giường đơn (2 phòng ngủ)",
    occupancy: "4 Người lớn + 2 Trẻ em",
    view: "Toàn cảnh Đảo Rều & Vịnh Bãi Cháy",
    badge: "Dành cho gia đình",
    rating: 4.9,
    reviewsCount: 165,
    description: "Family Grand Suite kết hợp sự riêng tư và gắn kết hoàn hảo cho cả đại gia đình. Gồm 2 không gian phòng ngủ kết nối liên thông, 2 phòng tắm rộng với bồn tắm sục, bàn ăn gia đình và ban công góc ngắm hoàng hôn rực rỡ trên vịnh di sản.",
    amenities: [
      { icon: "users", text: "Thiết kế 2 phòng ngủ thông nhau riêng tư" },
      { icon: "coffee", text: "Bữa sáng buffet cho 4 người lớn & 2 trẻ" },
      { icon: "gamepad", text: "Khu vui chơi trẻ em Kid's Club miễn phí" },
      { icon: "bath", text: "2 phòng tắm cao cấp trang bị bồn tắm riêng" },
      { icon: "utensils", text: "Khu vực bàn ăn gia đình & quầy bar mini" },
      { icon: "sun", text: "Ban công góc rộng nhìn trọn vịnh Bãi Cháy" },
      { icon: "waves", text: "Phao bơi miễn phí cho bé tại Sky Pool" },
      { icon: "sparkles", text: "Dịch vụ giặt ủi ưu đãi 20%" }
    ]
  }
];

const SPECIAL_OFFERS = [
  {
    id: "offer-summer-deal",
    title: "Summer Deal – Combo 2N1Đ Bãi Cháy",
    tag: "Ưu đãi mùa hè",
    badge: "Best Seller",
    priceText: "Chỉ từ 690.000 VNĐ / Khách",
    image: "https://dragonlegendhotel.com/wp-content/uploads/2026/02/fsdffsdfsd-658x492.jpg",
    excerpt: "Gói nghỉ dưỡng 4 sao trọn gói bao gồm 01 đêm phòng Superior sang trọng, buffet sáng hảo hạng, check-in hồ bơi vô cực tầng 12 và trà chiều thư giãn ngắm vịnh.",
    expiry: "Áp dụng đến hết 31/10/2026",
    benefits: [
      "01 đêm phòng hạng Superior hoặc Deluxe",
      "Bữa sáng Buffet Quốc tế hơn 50 món",
      "Miễn phí sử dụng Sky Pool & Fitness tầng 12",
      "Tặng Voucher giảm 10% tại nhà hàng Dragon Cuisine"
    ]
  },
  {
    id: "offer-early-bird",
    title: "Early Bird – Đặt Sớm Giảm Ngay 20%",
    tag: "Tiết kiệm thông minh",
    badge: "Giảm 20%",
    priceText: "Tiết kiệm đến 600.000 VNĐ / đêm",
    image: "https://dragonlegendhotel.com/wp-content/uploads/2026/02/Untitled-1-1-657x492.jpg",
    excerpt: "Lên kế hoạch sớm cho chuyến du ngoạn Hạ Long để nhận ngay mức giá ưu đãi đặc biệt giảm trực tiếp 20% cho tất cả các hạng phòng khi đặt trước 14 ngày.",
    expiry: "Áp dụng quanh năm",
    benefits: [
      "Giảm 20% trên giá niêm yết các hạng phòng",
      "Linh hoạt thay đổi ngày lưu trú miễn phí 1 lần",
      "Nước uống hoa quả & khăn lạnh đón tiếp",
      "Ưu tiên nâng hạng phòng tùy theo tình trạng phòng"
    ]
  },
  {
    id: "offer-mice-gala",
    title: "Gói Sự Kiện – Hội Nghị & Tiệc Gala Đỉnh Cao",
    tag: "Khách đoàn & Doanh nghiệp",
    badge: "Dành cho MICE",
    priceText: "Trọn gói từ 450.000 VNĐ / Suất tiệc",
    image: "https://dragonlegendhotel.com/wp-content/uploads/2026/02/491753343_1190446813091492_8232416561654215596_n-738x492.jpg",
    excerpt: "Giải pháp trọn vẹn cho chương trình hội thảo, team building, tri ân khách hàng và tiệc Gala cuối năm với hội trường hiện đại sức chứa tới 300 khách.",
    expiry: "Ưu đãi theo đoàn thể",
    benefits: [
      "Miễn phí phòng họp với màn hình LED P3 hiện đại",
      "Hệ thống âm thanh ánh sáng sân khấu biểu diễn chuẩn",
      "Thực đơn tiệc 8 món hải sản Hạ Long cao cấp",
      "Tặng gói Teabreak trà bánh sang trọng giữa giờ"
    ]
  }
];

const AMENITIES_DATA = [
  {
    id: "sky-pool",
    title: "Hồ Bơi Vô Cực & Sky Fitness",
    location: "Tầng 12 - Tầng cao nhất",
    image: "https://dragonlegendhotel.com/wp-content/uploads/2015/07/DJI_0081-HDR.jpg",
    description: "Đắm mình trong làn nước trong vắt tại hồ bơi vô cực ngắm trọn vẹn vịnh Hạ Long kỳ vĩ từ tầng 12. Phòng tập Gym trang bị máy móc tập luyện hiện đại chuẩn LifeFitness, mở cửa từ 06:00 đến 22:00.",
    highlights: ["Tầm nhìn Panorama 360°", "Nước ấm 4 mùa", "Quầy Pool Bar phục vụ cocktail"]
  },
  {
    id: "restaurant",
    title: "Nhà Hàng Ẩm Thực Legend Gourmet",
    location: "Tầng 2 & Tầng M",
    image: "https://dragonlegendhotel.com/wp-content/uploads/2019/08/z7329376216471_19994c0c3cb3524cbd0e2b56078cd8ab.jpg",
    description: "Hành trình vị giác thăng hoa với sự kết hợp tinh hoa giữa ẩm thực biển Hạ Long tươi sống và các món ăn Á - Âu đặc sắc chế biến bởi đội ngũ đầu bếp 5 sao.",
    highlights: ["Buffet sáng hơn 60 món Á - Âu", "Hải sản tươi sống bắt tại bể", "Phòng ăn VIP riêng tư sang trọng"]
  },
  {
    id: "ballroom",
    title: "Trung Tâm Hội Nghị & Tiệc Cưới Grand Ballroom",
    location: "Tầng 3",
    image: "https://dragonlegendhotel.com/wp-content/uploads/2026/02/BQ5.jpg",
    description: "Không gian đại tiệc tráng lệ không cột với trần cao 5.5m, đèn chùm pha lê tân cổ điển sang trọng, màn hình LED cong cực lớn và công nghệ âm thanh vòm đỉnh cao.",
    highlights: ["Sức chứa lên tới 350 khách", "Đội ngũ Event Planner chuyên nghiệp", "Hệ thống vách ngăn cách âm thông minh"]
  },
  {
    id: "lotus-spa",
    title: "Lotus Spa & Chăm Sóc Sức Khỏe",
    location: "Tầng 11",
    image: "https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg",
    description: "Không gian thanh tịnh thơm ngát hương tinh dầu sả chanh và sen hồng. Liệu trình massage truyền thống, xông hơi đá muối Himalaya giúp tái tạo năng lượng hoàn hảo.",
    highlights: ["Trị liệu bấm huyệt chuyên sâu", "Phòng xông hơi đá muối khô & ướt", "Sản phẩm thảo mộc 100% hữu cơ"]
  }
];

const TESTIMONIALS_DATA = [
  {
    author: "Dennis Ellul",
    location: "Canada",
    avatar: "https://dragonlegendhotel.com/wp-content/uploads/2026/02/408634589_10226615376023160_1198021278240416737_n.jpg",
    rating: 5,
    date: "Tháng 2, 2026",
    title: "Vượt qua sự mong đợi của tôi!",
    content: "Stopped for three nights at this hotel and was very impressed with the service, the food, and cleanness. The staff was incredibly helpful, especially Ngoc at reception and Huong at the restaurant. Highly recommend this hotel to anyone visiting Ha Long Bay!"
  },
  {
    author: "Ms. Thúy Nga",
    location: "Nam Định",
    avatar: "https://dragonlegendhotel.com/wp-content/uploads/2026/02/557370981_4220393851610917_766684084316907759_n-1.jpg",
    rating: 5,
    date: "Tháng 1, 2026",
    title: "Khách sạn tiêu chuẩn xịn xò, nhân viên rất chu đáo",
    content: "Mình vừa cùng gia đình nghỉ tại Dragon Legend tuần trước. Trải nghiệm rất trọn vẹn, phòng ốc sạch sẽ thơm tho, đồ ăn sáng phong phú và rất vừa miệng. Các bạn nhân viên luôn tươi cười đón tiếp. Chắc chắn sẽ quay lại vào dịp hè!"
  },
  {
    author: "Anh Chung",
    location: "Hà Nội",
    avatar: "https://dragonlegendhotel.com/wp-content/uploads/2026/02/558781329_1495380595087742_988380832516943251_n.jpg",
    rating: 5,
    date: "Tháng 2, 2026",
    title: "Vị trí đẹp, tầng 12 ngắm hoàng hôn cực phẩm",
    content: "Nhà mình đi du lịch 3 ngày 2 đêm ở đây. Vị trí trên đại lộ Marine rất thuận tiện đi dạo, ăn uống và đi sang Sun World. Hồ bơi tầng 12 view vịnh chụp ảnh sống ảo tuyệt đẹp. Chất lượng 4 sao nhưng giá cực kỳ hợp lý."
  },
  {
    author: "Phương Nam",
    location: "Hải Phòng",
    avatar: "https://dragonlegendhotel.com/wp-content/uploads/2026/02/600328087_4307218056261829_8379978694470017721_n.jpg",
    rating: 5,
    date: "Tháng 2, 2026",
    title: "Điểm lưu trú lý tưởng khi đến Hạ Long",
    content: "Dù ở Hải Phòng sang Hạ Long nhiều lần nhưng đây là lần đầu tôi ở Dragon Legend Hotel. Phong cách thiết kế tân cổ điển rất sang trọng, giường đệm ngủ rất êm không bị đau lưng. Dịch vụ chuyên nghiệp đáng khen ngợi."
  }
];

const ATTRACTIONS_DATA = [
  {
    name: "Sun World Hạ Long Complex",
    distance: "1.8 km (4 phút di chuyển)",
    description: "Tổ hợp công viên giải trí hàng đầu Đông Nam Á với Cáp treo Nữ Hoàng và Vòng quay Mặt Trời."
  },
  {
    name: "Bãi tắm Bãi Cháy & Phố đi bộ",
    distance: "1.2 km (3 phút di chuyển)",
    description: "Bờ cát trắng mịn trải dài 3km, làn nước xanh mát cùng các quán cafe biển sôi động về đêm."
  },
  {
    name: "Chợ đêm Hạ Long & Halong Marine Plaza",
    distance: "300 m (Đi bộ 3 phút)",
    description: "Thế giới quà lưu niệm, hải sản sấy khô và khu ẩm thực đường phố sầm uất ngay cạnh khách sạn."
  },
  {
    name: "Cảng tàu khách quốc tế Hạ Long",
    distance: "3.5 km (7 phút di chuyển)",
    description: "Bến xuất phát của các du thuyền thăm quan kỳ quan Vịnh Hạ Long và Đảo Ti Tốp."
  }
];
