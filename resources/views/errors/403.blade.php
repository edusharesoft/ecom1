<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied | 403</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #fef2f2; /* হালকা লালচে ব্যাকগ্রাউন্ড */
            color: #333;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }

        .container {
            padding: 40px;
            max-width: 600px;
            width: 90%;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.1); /* লাল আভা */
            border-bottom: 4px solid #ef4444;
        }

        .icon-box svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            fill: #ef4444; /* লাল রং */
            animation: pulse 2s infinite;
        }

        .error-code {
            font-size: 5rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 10px;
            line-height: 1;
        }

        .error-code span {
            color: #ef4444;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .message {
            font-size: 1.1rem;
            color: #4b5563;
            margin-bottom: 30px;
            line-height: 1.6;
            background: #fef2f2;
            padding: 15px;
            border-radius: 8px;
            border: 1px dashed #fca5a5;
        }

        /* Button */
        .btn-home {
            display: inline-block;
            padding: 12px 35px;
            background-color: #ef4444;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }

        .btn-home:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(239, 68, 68, 0.4);
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="icon-box">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 1C8.676 1 6 3.676 6 7v3H4v14h16V10h-2V7c0-3.324-2.676-6-6-6zm0 2c2.276 0 4 1.724 4 4v3H8V7c0-2.276 1.724-4 4-4zm0 10c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"/>
            </svg>
        </div>

        <div class="error-code">
            4<span>0</span>3
        </div>

        <h2>Access Forbidden</h2>

        <p class="message">
            আপনার গতিবিধী সন্দেহ জনক<br>
            আমাদের সাপোর্টে দয়া করে কথা বলুন।
        </p>

        <a href="{{ url('/') }}" class="btn-home">
            <i class="fa-solid fa-house"></i> হোম পেজে যান
        </a>
    </div>

</body>
</html>