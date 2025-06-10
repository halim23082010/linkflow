document.getElementById("authForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    const notification = document.getElementById("notification");
    notification.style.display = "none";
    notification.textContent = "";

    const email = document.getElementById("email").value.trim();

    try {
        const res = await fetch("forgot_password.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email })
        });

        // Hata kodları varsa (örneğin 500, 404), özel olarak işleniyor
        if (!res.ok) {
            const errorText = await res.text(); // JSON olmayabilir
            throw new Error(errorText || "Sunucu hatası");
        }

        const data = await res.json();

        notification.style.display = "block";
        notification.textContent = data.message;

        if (data.status === "success") {
            notification.style.backgroundColor = "#d4edda";
            notification.style.color = "#155724";
        } else {
            notification.style.backgroundColor = "#f8d7da";
            notification.style.color = "#721c24";
        }

        setTimeout(() => {
            notification.style.display = "none";
        }, 5000);

    } catch (error) {
        notification.style.display = "block";
        notification.style.backgroundColor = "#f8d7da";
        notification.style.color = "#721c24";
        notification.textContent = "Bir hata oluştu: " + error.message;

        setTimeout(() => {
            notification.style.display = "none";
        }, 5000);
    }
});


