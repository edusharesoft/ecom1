<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error | 500</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* বেসিক রিসেট */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .error-container {
            padding: 40px;
            max-width: 600px;
            width: 90%;
            background: #fff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border-radius: 10px;
        }

        /* আইকন এবং কোড */
        .illustration svg {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
        }

        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: #e5e7eb;
            line-height: 1;
            margin-bottom: 10px;
            position: relative;
        }
        
        .error-code span {
            color: #6366f1; /* আপনার ব্র্যান্ড কালার */
        }

        /* টেক্সট */
        h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #1f2937;
        }

        p {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        /* বাটন */
        .btn-home {
            display: inline-block;
            background-color: #6366f1; /* আপনার ব্র্যান্ড কালার */
            color: #fff;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-home:hover {
            background-color: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
        }
    </style>
</head>
<body>

    <div class="error-container">
        <div class="illustration">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-7v2h2v-2h-2zm0-8v6h2V7h-2z" fill="#6366f1"/>
            </svg>
        </div>

        <div class="error-code">
            5<span>0</span>0
        </div>

        <h2>Internal Server Error</h2>
        
        <p>
            দুঃখিত! সার্ভারে কোনো একটি সমস্যা হয়েছে।<br>
            দয়া করে কিছুক্ষণ পর আবার চেষ্টা করুন।
        </p>
        
        <a href="{{ url('/') }}" class="btn-home">
            <i class="fa-solid fa-house"></i> হোম পেজে ফিরে যান
        </a>
    </div>

</body>
</html>