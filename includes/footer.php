<link rel="stylesheet" href="/AureliusWatch/assets/css/main.css">

<footer class="site-footer">
    <div class="footer-top">

        <!-- LOGO BÊN TRÁI -->
        <div class="footer-logo">
            <a href="/AureliusWatch/index.php">
            <img src="/AureliusWatch/assets/images/logo.png" alt="Aurelius Watch">
</a>
        </div>

        <!-- 3 CỘT Ở GIỮA -->
        <div class="footer-columns">

    <!-- CỘT 1: SẢN PHẨM -->
    <div class="footer-col">
        <h4>Sản phẩm</h4>
        <ul>
            <li>
                <a href="/AureliusWatch/pages/product/list.php?gender=1">
                    Đồng hồ nam
                </a>
            </li>
            <li>
                <a href="/AureliusWatch/pages/product/list.php?gender=2">
                    Đồng hồ nữ
                </a>
            </li>
            <li>
                <a href="/AureliusWatch/pages/product/list.php?gender=3">
                    Đồng hồ Unisex
                </a>
            </li>
        </ul>
    </div>

    <!-- CỘT 2: AURELIUS WATCH -->
    <div class="footer-col">
        <h4>Aurelius Watch</h4>
        <ul>
            <li><a href="/AureliusWatch/pages/about.php">Về chúng tôi</a></li>
            <li><a href="/AureliusWatch/pages/blog/list.php">Bài viết</a></li>
            <li><a href="/AureliusWatch/pages/contact/contact.php">Liên hệ</a></li>
        </ul>
    </div>

    <!-- CỘT 3: POLICY -->
    <div class="footer-col">
        <h4>Chính sách</h4>
        <ul>
            <li>
                <a href="/AureliusWatch/pages/policy/warranty_policy.php">
                    Chính sách bảo hành
                </a>
            </li>
            <li>
                <a href="/AureliusWatch/pages/policy/return_policy.php">
                    Chính sách đổi trả
                </a>
            </li>
            <li>
                <a href="/AureliusWatch/pages/policy/privacy_policy.php">
                    Chính sách bảo mật
                </a>
            </li>
            <li>
                <a href="/AureliusWatch/pages/policy/terms.php">
                    Điều khoản sử dụng
                </a>
            </li>
        </ul>
    </div>

</div>      
    </div>

    <!-- LINE TRẮNG -->
    <div class="footer-divider"></div>

    <!-- COPYRIGHT -->
    <div class="footer-bottom">
        <i class="fa-regular fa-copyright"></i>
        <span>2025 Aurelius Watch</span>
        <span class="footer-sep">|</span>
        <span class="footer-slogan">Aurelius Watch – A Legacy of Time</span>
    </div>
    
    </footer>

    <script>
window.addEventListener("scroll", () => {
    document
        .querySelector(".site-header")
        .classList.toggle("scrolled", window.scrollY > 5);
});
</script>

<!-- ================= CHATBOT ================= -->

<div id="chatbot-bubble">
    <img src="/AureliusWatch/assets/images/chatbot.png" alt="Chatbot">
</div>

<div id="chatbot-box">
    <div class="chatbot-header">
        <span>Aurelius AI</span>
        <button id="chatbot-close">✕</button>
    </div>

    <div class="chatbot-messages" id="chatbot-messages">
        <div class="bot-message">
            Xin chào 👋 Tôi có thể giúp bạn tư vấn đồng hồ.
        </div>
    </div>

    <div class="chatbot-input">
        <input type="text" id="chatbot-text" placeholder="Nhập câu hỏi...">
        <button id="chatbot-send">➤</button>
    </div>
</div>

<!-- ================= SCRIPT ================= -->
<script>
document.addEventListener("DOMContentLoaded", () => {

    const bubble   = document.getElementById("chatbot-bubble");
    const box      = document.getElementById("chatbot-box");
    const closeBtn = document.getElementById("chatbot-close");
    const sendBtn  = document.getElementById("chatbot-send");
    const input    = document.getElementById("chatbot-text");
    const messages = document.getElementById("chatbot-messages");

    /* Open / Close */
    bubble.onclick = () => box.style.display = "flex";
    closeBtn.onclick = () => box.style.display = "none";

    /* Send */
    sendBtn.onclick = sendMessage;
    input.addEventListener("keypress", e => {
        if (e.key === "Enter") sendMessage();
    });

    function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        appendMessage(text, "user");
        input.value = "";

        fetch("/AureliusWatch/chatbot/agent.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            appendMessage(data.reply || "Không có phản hồi", "bot");
        })
        .catch(() => {
            appendMessage("Xin lỗi, hệ thống đang bận.", "bot");
        });
    }

    function appendMessage(text, type) {
    const div = document.createElement("div");
    div.className = type === "user" ? "user-message" : "bot-message";

    if (type === "bot") {
        // ✅ BOT: render HTML
        div.innerHTML = text;
    } else {
        // ✅ USER: an toàn
        div.textContent = text;
    }

    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
}
});
</script>