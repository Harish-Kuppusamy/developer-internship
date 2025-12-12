$(document).ready(function () {
    const token = localStorage.getItem("session_token");
    const userEmail = localStorage.getItem("user_email");

    if (!token) {
        window.location.href = "index.html";
        return;
    }

    if (userEmail) {
        $("#email").val(userEmail);
    }

    $.ajax({
        url: "api/getProfile.php",
        method: "GET",
        headers: {
            "X-Session-Token": token,
        },
        success: function (res) {
            if (res.status === "success" && res.profile) {
                const p = res.profile;

                $("#fullName").val(p.fullName || "");
                $("#age").val(p.age || "");
                $("#gender").val(p.gender || "");
                $("#phone").val(p.phone || "");
                $("#skills").val(p.skills ? p.skills.join(", ") : "");
                $("#bio").val(p.bio || "");
            }
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                forceLogout();
            } else {
                console.error("Error loading profile:", xhr);
            }
        },
    });

    $("#profileForm").on("submit", function (e) {
        e.preventDefault();

        const fullName = $("#fullName").val().trim();
        const age = $("#age").val().trim();
        const gender = $("#gender").val();
        const phone = $("#phone").val().trim();
        const skillsRaw = $("#skills").val().trim();
        const bio = $("#bio").val().trim();

        if (!fullName) {
            showProfileMessage("Full name is required", "danger");
            return;
        }

        let skills = [];
        if (skillsRaw) {
            skills = skillsRaw
                .split(",")
                .map((s) => s.trim())
                .filter((s) => s.length > 0);
        }

        $.ajax({
            url: "api/updateProfile.php",
            method: "POST",
            headers: {
                "X-Session-Token": token,
            },
            dataType: "json",
            data: {
                fullName,
                age,
                gender,
                phone,
                bio,
                skills: JSON.stringify(skills), 
            },
            success: function (res) {
                if (res.status === "success") {
                    showProfileMessage("Profile saved successfully!", "success");
                } else {
                    showProfileMessage(res.message || "Failed to save profile", "danger");
                }
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    forceLogout();
                } else {
                    console.error("Error saving profile:", xhr);
                    showProfileMessage("Server error. Try again.", "danger");
                }
            },
        });
    });

    function showProfileMessage(message, type) {
        $("#profileResponse")
            .removeClass("d-none alert-success alert-danger")
            .addClass("alert-" + type)
            .text(message);
    }

    function forceLogout() {
        localStorage.removeItem("session_token");
        localStorage.removeItem("user_id");
        localStorage.removeItem("user_email");
        window.location.href = "index.html";
    }
});