$(document).ready(function () {

    $("#registerForm").on("submit", function (e) {
        e.preventDefault();

        const name = $("#regName").val().trim();
        const email = $("#regEmail").val().trim();
        const password = $("#regPassword").val().trim();
        const confirmPassword = $("#regConfirmPassword").val().trim();

        if (!name || !email || !password || !confirmPassword) {
            showRegisterMessage("Please fill in all fields", "danger");
            return;
        }

        if (password !== confirmPassword) {
            showRegisterMessage("Passwords do not match", "danger");
            return;
        }

        if (password.length < 6) {
            showRegisterMessage("Password should be at least 6 characters", "danger");
            return;
        }

        $.ajax({
            url: "api/register.php",
            method: "POST",
            dataType: "json",
            data: {
                name: name,
                email: email,
                password: password,
            },
            success: function (res) {
                if (res.status === "success") {
                    showRegisterMessage(res.message, "success");
                    $("#registerForm")[0].reset();
                } else {
                    showRegisterMessage(res.message || "Registration failed", "danger");
                }
            },
            error: function (status, error) {
                console.error("AJAX Error:", status, error);
                showRegisterMessage("Server error. Please try again.", "danger");
            },
        });
    });

    function showRegisterMessage(message, type) {
        $("#registerResponse")
            .removeClass("d-none alert-success alert-danger")
            .addClass("alert-" + type)
            .text(message);
    }
});