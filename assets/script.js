document.addEventListener("DOMContentLoaded", function () {
    let toast = document.querySelector(".toast");
    if (toast) {
        setTimeout(() => {
            toast.classList.add("fade");
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
});
$(document).ready(function () {
    $("#resetForm").submit(function (event) {
        event.preventDefault(); // Ngăn chặn tải lại trang

        let email = $("#email").val(); // Lấy giá trị email từ input

        $.ajax({
            url: "forgot_password.php",
            type: "POST",
            data: { email: email },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message); // Hiển thị thông báo
                    window.location.href = response.redirect; // Chuyển hướng sang nhập mã
                } else {
                    alert(response.message); // Hiển thị lỗi
                }
            },
            error: function () {
                alert("Đã xảy ra lỗi, vui lòng thử lại!");
            }
        });
    });
});
document.addEventListener("DOMContentLoaded", function() {
    console.log("Trang đã tải xong!");
});
