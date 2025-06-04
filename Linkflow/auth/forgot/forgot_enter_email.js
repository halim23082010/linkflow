document.getElementById("authForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    const notification = document.getElementById("notification");
    notification.style.display = "none";
    notification.textContent = "";

    const email = document.getElementById("email").value;

    try {
        const res = await fetch("forgot_password.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email })
        });

        const data = await res.json();

        notification.style.display = "block";
        notification.textContent = data.message;

        if (data.status === "success") {
            notification.style.backgroundColor = "#d4edda";  // yeşil arka plan
            notification.style.color = "#155724";            // yeşil yazı
        } else {
            notification.style.backgroundColor = "#f8d7da";  // kırmızı arka plan
            notification.style.color = "#721c24";            // kırmızı yazı
        }

        // 5 saniye sonra mesajı gizle
        setTimeout(() => {
            notification.style.display = "none";
        }, 5000);

    } catch (error) {
        notification.style.display = "block";
        notification.style.backgroundColor = "#f8d7da";
        notification.style.color = "#721c24";
        notification.textContent = "Sunucu ile bağlantı kurulamadı.";
        setTimeout(() => {
            notification.style.display = "none";
        }, 5000);
    }
});

