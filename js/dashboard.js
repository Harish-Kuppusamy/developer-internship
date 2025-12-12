$(document).ready(function () {
    const token = localStorage.getItem("session_token");
    if (!token) {
        window.location.href = "index.html";
        return;
    }

    $.ajax({
        url: "api/me.php",
        method: "GET",
        headers: {
            "X-Session-Token": token
        },
        success: function (res) {
            if (res.status === "success" && res.user) {
                $("#userName").text(`Hello, ${res.user.name} (${res.user.email})`);
            } else {
                forceLogout();
            }
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                forceLogout();
            }
        }
    });



    $("#logoutBtn").on("click", function () {
        $.ajax({
            url: "api/logout.php",
            method: "POST",
            headers: {
                "X-Session-Token": token
            },
            complete: function () {
                forceLogout();
            }
        });
    });


    function forceLogout() {
        localStorage.removeItem("session_token");
        localStorage.removeItem("user_id");
        localStorage.removeItem("user_email");
        window.location.href = "login.html";
    }

    $("#viewProfile").on("click", function () {
        window.location.href = "profile.html";
    });
});
