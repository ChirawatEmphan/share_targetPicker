<?php
// สร้างการเชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'username', 'password', 'database');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// เพิ่มจำนวนผู้เข้าชม
$conn->query("UPDATE visitor_count SET count = count + 1 WHERE id = 1");

// ดึงจำนวนผู้เข้าชม
$result = $conn->query("SELECT count FROM visitor_count WHERE id = 1");
$row = $result->fetch_assoc();
$visitor_count = $row['count'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Flex JSON</title>
    <link rel="icon" href="img/logo.jpg" type="image/jpg">

    <style>
        body { 
            padding: 20px; 
            font-family: Arial, sans-serif; 
            background-color: #fff5e6; 
            color: #333; 
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        .login-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        textarea { 
            width: 100%; 
            height: 300px; 
            margin-bottom: 20px; 
            border: 1px solid #ffa500; 
            padding: 10px; 
            border-radius: 5px; 
            resize: vertical;
        }
        button { 
            padding: 10px 20px; 
            background-color: #ff8c00; 
            color: white; 
            border: none; 
            cursor: pointer; 
            border-radius: 5px; 
            width: 100%; 
            max-width: 300px; 
            margin: 0 auto;
            display: block;
        }
        button:hover { 
            background-color: #e67300; 
        }
        .support-section {
            text-align: center;
            margin-top: 30px;
            font-size: 18px; /* Larger font size */
        }
        .support-section p {
            margin: 15px 0; /* Increase margin */
            font-size: 18px; /* Larger font size */
            color: #333;
        }
        .support-section a {
            color: #ff8c00;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px; /* Larger font size */
        }
        .support-section a:hover {
            text-decoration: underline;
        }
        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 50px; /* Increase gap */
            margin-top: 30px;
        }
        .qr-container img {
            width: 280px; /* Larger images */
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        .swal2-popup {
            width: 700px !important; /* Increase popup width */
            padding: 30px !important; /* Increase padding */
            border-radius: 15px !important; /* Rounder corners */
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.3) !important; /* Add shadow */
        }
        .reference {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 12px;
            color: #666;
        }
        .reference a {
            color: #ffa500;
            text-decoration: underline;
        }
        .reference a:hover {
            text-decoration: underline;
        }
        .visitor-counter {
    position: fixed;
    bottom: 10px;
    left: 10px;
    background-color: rgba(255, 255, 255, 0.8);
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    font-size: 14px;
    color: #333;
}
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <script>
        // Disable right-click globally
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        }, false);

    </script>
</head>
<body>
<div class="header">
        <img src="img/logo.jpg" alt="Logo">
    </div>

    <div id="loginContainer" class="login-container">
        <button id="btnLogin" onclick="login()">Log In with LINE</button>
    </div>

    <div id="formContainer" style="display: none;">
        <h2 style="text-align: center;">Share Target Picker</h2>
        <p style="text-align: center; font-size: 14px;">
    Flex simulator: <a href="https://developers.line.biz/flex-simulator/" target="_blank">https://developers.line.biz/flex-simulator/</a>
</p>
        <form id="flexJsonForm">
            <textarea name="flex_json" id="flexJsonInput" placeholder="Paste your Flex JSON Ctrl+V here "></textarea>
            <button type="submit">Submit and Share</button>
        </form>
    </div>
    <div class="reference">
        Reference: <a href="https://medium.com/linedevth/share-target-picker-ฟีเจอร์ใหม่ใน-liff-line-front-end-framework-27b480681b5b" target="_blank">https://medium.com/linedevth/share-target-picker-ฟีเจอร์ใหม่ใน-liff-line-front-end-framework-27b480681b5b</a>
    </div>

    <div class="visitor-counter">
        Visitors: <?php echo $visitor_count; ?>
    </div>
    <script>
        let flexData = null;

        async function login() {
            try {
                await liff.init({ liffId: "2005625542-8DlDqJMz" });
                if (!liff.isLoggedIn()) {
                    await liff.login();
                }
                document.getElementById("loginContainer").style.display = "none";
                document.getElementById("formContainer").style.display = "block";

                // Hide URL query parameters after login
                if (window.history.replaceState) {
                    const url = new URL(window.location.href);
                    url.search = ''; // Clear query parameters
                    window.history.replaceState({}, document.title, url.toString());
                }
            } catch (error) {
                console.error('Login failed', error);
                Swal.fire('Error', 'Login failed. Please try again.', 'error');
            }
        }

        document.getElementById('flexJsonForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const flexJson = document.getElementById('flexJsonInput').value.trim();

            if (!flexJson) {
                Swal.fire('Error', 'Please input the Flex JSON.', 'error');
                return;
            }

            try {
                flexData = JSON.parse(flexJson);
            } catch (error) {
                Swal.fire('Error', 'Invalid JSON input. Please make sure your input is valid JSON.', 'error');
                return;
            }

            sendShare();
        });

        async function sendShare() {
            if (!flexData) {
                Swal.fire('Error', 'Invalid JSON input. Please make sure your input is valid JSON.', 'error');
                return;
            }

            const flexMessage = [
                {
                    "type": "flex",
                    "altText": "Flex Message",
                    "contents": flexData
                }
            ];

            try {
                const result = await liff.shareTargetPicker(flexMessage);

                if (result) {
                    showSupportPopup();
                } else {
                    Swal.fire('Cancelled', 'ShareTargetPicker was canceled or failed.', 'warning');
                }
            } catch (error) {
                console.error('Error sharing message', error);
                Swal.fire('Error', 'An error occurred while sharing.', 'error');
            }
        }

        function showSupportPopup() {
            Swal.fire({
                title: 'ช่องทางการสนับสนุน',
                html: `
                    <div class="support-section">
                        <p><strong>YouTube:</strong> <a href="https://www.youtube.com/c/CodingDuck" target="_blank">https://www.youtube.com/c/CodingDuck</a></p>
                        <p><strong>Facebook:</strong> <a href="https://www.facebook.com/codingduckth" target="_blank">https://www.facebook.com/codingduckth</a></p>
                        <div class="qr-container">
                            <div>
                                <p><strong>เพิ่มเพื่อน LINE:</strong></p>
                                <img src="img/qr.png" alt="QR Code LINE">
                            </div>
                            <div>
                                <p><strong>โดเนท PromptPay:</strong></p>
                                <img src="img/promptpay.jpg" alt="QR Code PromptPay">
                            </div>
                        </div>
                    </div>
                `,
                confirmButtonText: 'ปิด',
                confirmButtonColor: '#ff8c00',
                willClose: () => {
                    location.reload(); // Refresh the page when the popup is closed
                }
            });
        }

        // Automatically check if the user is logged in
        (async function() {
            try {
                await liff.init({ liffId: "2005625542-8DlDqJMz" });
                if (liff.isLoggedIn()) {
                    document.getElementById("loginContainer").style.display = "none";
                    document.getElementById("formContainer").style.display = "block";

                    // Hide URL query parameters
                    if (window.history.replaceState) {
                        const url = new URL(window.location.href);
                        url.search = ''; // Clear query parameters
                        window.history.replaceState({}, document.title, url.toString());
                    }
                } else {
                    document.getElementById("loginContainer").style.display = "block";
                }
            } catch (error) {
                console.error('LIFF initialization failed', error);
                Swal.fire('Error', 'Failed to initialize LIFF.', 'error');
            }
        })();
    </script>
</body>
</html>
