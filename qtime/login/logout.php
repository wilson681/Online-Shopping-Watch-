<?php
require '../base.php';

// 🔥 彻底清除所有登录信息
session_unset();
session_destroy();

// ✅ 输出 HTML 页面用于展示弹窗 + 跳转
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logging Out...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .modal {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            max-width: 400px;
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .modal-content button {
            margin-top: 20px;
            padding: 10px 20px;
            background: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content button:hover {
            background: #333;
        }
    </style>
</head>
<body>

<script>
    function showError(message) {
        let countdown = 5;
        let modal = document.createElement("div");
        modal.classList.add("modal");
        modal.innerHTML = `
            <div class="modal-content">
                <h3>Goodbye!</h3>
                <p id="logout-msg">${message} <span id="countdown">${countdown}</span> seconds...</p>
                <button onclick="redirectNow()">Return to Homepage</button>
            </div>
        `;
        document.body.appendChild(modal);

        // ⏳ 每 1 秒更新倒计时
        const interval = setInterval(() => {
            countdown--;
            document.getElementById("countdown").innerText = countdown;

            if (countdown === 0) {
                clearInterval(interval);
                redirectNow();
            }
        }, 1000);
    }

    function redirectNow() {
        window.location.href = "../homepage/home.php";
    }

    // 🚀 执行弹窗
    showError("You have been logged out. Redirecting to homepage in");
</script>

</body>
</html>
