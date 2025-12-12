$(document).ready(function () {
    const token = localStorage.getItem("session_token");

    if (token) {
        $.ajax({
            url: "api/me.php",
            method: "GET",
            headers: { "X-Session-Token": token },
            success: function (res) {
                if (res.status === "success") {
                    window.location.href = "dashboard.html";
                }
            }
        });
    }

    $("#loginForm").on("submit", function (e) {
        e.preventDefault();

        const email = $("#loginEmail").val().trim();
        const password = $("#loginPassword").val().trim();

        if (!email || !password) {
            showLoginMessage("Please enter email and password", "danger");
            return;
        }

        $.ajax({
            url: "api/login.php",
            method: "POST",
            dataType: "json",
            data: {
                email: email,
                password: password,
            },
            success: function (res) {
                if (res.status === "success") {
                    localStorage.setItem("session_token", res.token);
                    localStorage.setItem("user_id", res.user.id);
                    localStorage.setItem("user_email", res.user.email);

                    setTimeout(() => {
                        window.location.href = "dashboard.html";
                    }, 800);
                }
                else {
                    showLoginMessage(res.message || "Login failed", "danger");
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
                showLoginMessage("Server error. Please try again.", "danger");
            },
        });
    });

    function showLoginMessage(message, type) {
        $("#loginResponse")
            .removeClass("d-none alert-success alert-danger")
            .addClass("alert-" + type)
            .text(message);
    }
});