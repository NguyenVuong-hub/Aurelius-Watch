<?php
function getAdminReplyByRating(int $rating): string
{
    switch ($rating) {
        case 5:
            return "Aurelius Watch xin chân thành cảm ơn quý khách đã dành thời gian chia sẻ trải nghiệm.\n\n"
                 . "Chúng tôi rất vui khi sản phẩm đã đáp ứng được kỳ vọng của quý khách về chất lượng, thiết kế và độ hoàn thiện. "
                 . "Sự hài lòng của quý khách là động lực để Aurelius Watch không ngừng nâng cao dịch vụ và mang đến những mẫu đồng hồ tinh tế hơn nữa trong tương lai.\n\n"
                 . "Rất mong được tiếp tục đồng hành cùng quý khách trong những lần mua sắm tiếp theo.\n"
                 . "Trân trọng.";

        case 4:
            return "Aurelius Watch xin cảm ơn quý khách đã gửi đánh giá cho sản phẩm.\n\n"
                 . "Chúng tôi ghi nhận những góp ý của quý khách và sẽ tiếp tục cải thiện để mang đến trải nghiệm hoàn thiện hơn trong thời gian tới.\n\n"
                 . "Rất mong sẽ tiếp tục nhận được sự tin tưởng và ủng hộ của quý khách.\n"
                 . "Trân trọng.";

        default:
            return "Aurelius Watch xin chân thành cảm ơn quý khách đã phản hồi.\n\n"
                 . "Chúng tôi rất tiếc khi trải nghiệm của quý khách chưa thực sự trọn vẹn. "
                 . "Những góp ý của quý khách là cơ sở quan trọng để chúng tôi cải thiện chất lượng sản phẩm và dịch vụ.\n\n"
                 . "Bộ phận chăm sóc khách hàng sẽ liên hệ với quý khách trong thời gian sớm nhất để hỗ trợ tốt hơn.\n"
                 . "Trân trọng.";
    }
}
