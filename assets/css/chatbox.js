document.addEventListener("DOMContentLoaded", () => {

    const input   = document.getElementById("chatbot-text");
    const sendBtn = document.getElementById("chatbot-send");
    const bubble  = document.getElementById("chatbot-bubble");
    const closeBtn= document.getElementById("chatbot-close");
    const box     = document.getElementById("chatbot-box");
    const msgBox  = document.getElementById("chatbot-messages");

    /* ================= OPEN / CLOSE ================= */
    bubble?.addEventListener("click", () => {
        box.classList.add("active");
        input.focus();
    });

    closeBtn?.addEventListener("click", () => {
        box.classList.remove("active");
    });

    /* ================= SEND EVENTS ================= */
    sendBtn?.addEventListener("click", e => {
        e.preventDefault();
        sendMessage();
    });

    input?.addEventListener("keydown", e => {
        if (e.key === "Enter") {
            e.preventDefault();
            sendMessage();
        }
    });

    /* ================= SEND MESSAGE ================= */
    async function sendMessage(textFromButton = null) {

        const text = (textFromButton ?? input.value).trim();
        if (!text) return;

        addUserMessage(text);
        input.value = "";
        showTyping();

        try {
            const res = await fetch("/AureliusWatch/chatbot/agent.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ message: text })
            });

            const raw = await res.text();
            let data;

            try {
                data = JSON.parse(raw);
            } catch {
                throw new Error("Invalid JSON");
            }

            removeTyping();

            if (!data || !data.reply) {
                addBotMessage("⚠️ Hệ thống đang nâng cấp, vui lòng thử lại.");
                return;
            }

            addBotMessage(data.reply, data.quickReplies || []);

        } catch (err) {
            console.error("Chatbot error:", err);
            removeTyping();
            addBotMessage("⚠️ Không thể kết nối máy chủ.");
        }
    }

    /* ================= USER MESSAGE ================= */
    function addUserMessage(text) {
        const div = document.createElement("div");
        div.className = "user-message";
        div.textContent = text; // ✅ text only (an toàn)
        msgBox.appendChild(div);
        scrollBottom();
    }

    /* ================= BOT MESSAGE ================= */
    function addBotMessage(html, quickReplies = []) {

        const wrapper = document.createElement("div");
        wrapper.className = "bot-message";

        // ✅ render HTML cho card, link, button
        wrapper.innerHTML = html;

        msgBox.appendChild(wrapper);

        // Quick replies
        if (Array.isArray(quickReplies) && quickReplies.length) {
            const qrBox = document.createElement("div");
            qrBox.className = "quick-replies";

            quickReplies
                .filter(Boolean)
                .forEach(q => {
                    const btn = document.createElement("button");
                    btn.className = "quick-reply-btn";
                    btn.textContent = q.label;
                    btn.dataset.value = q.value;
                    qrBox.appendChild(btn);
                });

            msgBox.appendChild(qrBox);
        }

        scrollBottom();
    }

    /* ================= EVENT DELEGATION ================= */

    // Click quick reply
    msgBox.addEventListener("click", e => {
        const btn = e.target.closest(".quick-reply-btn");
        if (!btn) return;

        sendMessage(btn.dataset.value);
    });

    // Click chatbot card button
    msgBox.addEventListener("click", e => {
        const link = e.target.closest(".chatbot-btn");
        if (!link) return;

        e.preventDefault();
        window.open(link.getAttribute("href"), "_blank");
    });

    /* ================= TYPING ================= */
    function showTyping() {
        removeTyping();

        const typing = document.createElement("div");
        typing.id = "typing-indicator";
        typing.className = "bot-message typing";
        typing.textContent = "Aurelius AI đang phân tích...";
        msgBox.appendChild(typing);
        scrollBottom();
    }

    function removeTyping() {
        const typing = document.getElementById("typing-indicator");
        if (typing) typing.remove();
    }

    /* ================= SCROLL ================= */
    function scrollBottom() {
        msgBox.scrollTop = msgBox.scrollHeight;
    }

});
