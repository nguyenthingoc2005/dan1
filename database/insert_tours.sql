-- ============================================================================
-- SAMPLE DATA: TOURS
-- ============================================================================

-- 1. Tour Hạ Long 3N2Đ
INSERT INTO tours (id, code, category_id, name, description, duration_days, duration_nights, departure_location, adult_price, child_price, status, created_by) VALUES
(1, 'TOUR-HL001', 1, 'Hạ Long - Kỳ Quan Thiên Nhiên Thế Giới', 'Khám phá vẻ đẹp hùng vĩ của Vịnh Hạ Long, ngủ đêm trên du thuyền 5 sao, chèo kayak và thăm hang động.', 3, 2, 'Hà Nội', 3500000, 2500000, 'active', 2);

-- Itinerary Hạ Long
INSERT INTO itineraries (tour_id, day_number, title, description, destination_id) VALUES
(1, 1, 'Hà Nội - Hạ Long - Hang Sửng Sốt', 'Xe đón quý khách tại Hà Nội. Di chuyển tới Hạ Long. Ăn trưa trên tàu. Chiều thăm Hang Sửng Sốt.', 1),
(1, 2, 'Đảo Ti Tốp - Chèo Kayak', 'Sáng ngắm bình minh, thăm đảo Ti Tốp. Tự do tắm biển hoặc leo núi. Chiều chèo Kayak khám phá vịnh.', 1),
(1, 3, 'Hạ Long - Hà Nội', 'Thăm làng chài, mua sắm hải sản. Ăn trưa và quay về Hà Nội. Kết thúc chương trình.', 1);

-- Highlights Hạ Long
INSERT INTO tour_highlights (tour_id, highlight) VALUES
(1, 'Du thuyền 5 sao sang trọng'),
(1, 'Thăm Hang Sửng Sốt đẹp nhất vịnh'),
(1, 'Chèo Kayak khám phá hang luồn');

-- Images Hạ Long (Placeholder)
INSERT INTO tour_images (tour_id, image_url, is_primary) VALUES
(1, 'https://images.unsplash.com/photo-1506606401543-2e73709cebb4?auto=format&fit=crop&w=800&q=80', 1),
(1, 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=800&q=80', 0);


-- 2. Tour Thái Lan 5N4Đ
INSERT INTO tours (id, code, category_id, name, description, duration_days, duration_nights, departure_location, adult_price, child_price, status, created_by) VALUES
(2, 'TOUR-BKK002', 2, 'Bangkok - Pattaya: Thiên Đường Mua Sắm', 'Trải nghiệm văn hóa Thái Lan, show diễn Alcazar, đảo Coral và thiên đường mua sắm Bangkok.', 5, 4, 'Hồ Chí Minh', 6990000, 5500000, 'active', 2);

-- Itinerary Thái Lan
INSERT INTO itineraries (tour_id, day_number, title, description, destination_id) VALUES
(2, 1, 'TP.HCM - Bangkok - Pattaya', 'Bay đến Bangkok, xe đưa về Pattaya. Nhận phòng khách sạn, tự do khám phá Walking Street.', 6),
(2, 2, 'Đảo Coral - Trân Bảo Phật Sơn', 'Đi cano ra đảo Coral tắm biển. Chiều thăm Trân Bảo Phật Sơn và vườn nho.', 6),
(2, 3, 'Pattaya - Bangkok', 'Thăm trung tâm vàng bạc đá quý. Quay về Bangkok. Dạo thuyền sông Chaophraya.', 6),
(2, 4, 'Safari World - Shopping', 'Tham quan vườn thú Safari World. Tự do mua sắm tại BigC, Central World.', 6),
(2, 5, 'Bangkok - TP.HCM', 'Thăm chùa Phật Vàng. Ra sân bay về Việt Nam.', 6);

-- Highlights Thái Lan
INSERT INTO tour_highlights (tour_id, highlight) VALUES
(2, 'Tặng vé Buffet 86 tầng Baiyoke Sky'),
(2, 'Massage Thái cổ truyền'),
(2, 'Khách sạn 4 sao trung tâm');

-- Images Thái Lan (Placeholder)
INSERT INTO tour_images (tour_id, image_url, is_primary) VALUES
(2, 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?auto=format&fit=crop&w=800&q=80', 1),
(2, 'https://images.unsplash.com/photo-1563492065599-3520f775eeed?auto=format&fit=crop&w=800&q=80', 0);
