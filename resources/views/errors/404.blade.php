<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found | 404</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- General Styles --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #1f2937;
        }

        .container {
            text-align: center;
            position: relative;
            z-index: 10;
        }

        /* --- Ghost Animation (CSS Only) --- */
        .ghost-container {
            position: relative;
            width: 150px;
            height: 180px;
            margin: 0 auto 30px;
            animation: float 3s ease-in-out infinite;
        }

        .ghost {
            width: 100%;
            height: 100%;
            background: #fff;
            border-radius: 75px 75px 0 0;
            position: relative;
            box-shadow: -5px 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .face {
            position: absolute;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            display: flex;
            justify-content: space-between;
        }

        .eye {
            width: 18px;
            height: 18px;
            background: #333;
            border-radius: 50%;
        }

        .mouth {
            width: 14px;
            height: 14px;
            background: #333;
            border-radius: 50%;
            position: absolute;
            top: 25px;
            left: 50%;
            transform: translateX(-50%);
        }

        .blush {
            width: 12px;
            height: 8px;
            background: #ffb7b2;
            border-radius: 50%;
            position: absolute;
            top: 15px;
        }
        .blush.left { left: 0; }
        .blush.right { right: 0; }

        /* Ghost tail (wavy bottom) */
        .ghost-bottom {
            display: flex;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        .ghost-bottom div {
            flex-grow: 1;
            height: 20px;
            background: #fff;
            border-radius: 0 0 50% 50%;
            position: relative;
            top: 10px;
        }

        .shadow {
            width: 120px;
            height: 20px;
            background: rgba(0,0,0,0.1);
            border-radius: 50%;
            margin: -10px auto 0;
            animation: shrink 3s ease-in-out infinite;
        }

        /* --- Typography --- */
        h1 {
            font-size: 8rem;
            line-height: 1;
            font-weight: 900;
            color: #fff;
            text-shadow: 0 10px 20px rgba(0,0,0,0.1);
            margin-bottom: -20px;
            position: relative;
            z-index: -1;
            /* Text with image clipping effect (Optional style) */
            -webkit-text-stroke: 2px #e5e7eb;
            color: transparent; 
            background: linear-gradient(to bottom, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #374151;
        }

        p {
            color: #6b7280;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        /* --- Button --- */
        .btn-home {
            background: linear-gradient(to right, #6366f1, #8b5cf6);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.5);
        }

        /* --- Animations --- */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes shrink {
            0%, 100% { transform: scale(1); opacity: 0.1; }
            50% { transform: scale(0.8); opacity: 0.05; }
        }

        @media (max-width: 600px) {
            h1 { font-size: 6rem; }
            h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="ghost-container">
            <div class="ghost">
                <div class="face">
                    <div class="eye"></div>
                    <div class="eye"></div>
                    <div class="mouth"></div>
                    <div class="blush left"></div>
                    <div class="blush right"></div>
                </div>
                <div class="ghost-bottom">
                    <div></div><div></div><div></div><div></div><div></div>
                </div>
            </div>
            <div class="shadow"></div>
        </div>

        <h1>404</h1>
        <h2>Oops! Page Not Found</h2>
        <p>দুঃখিত, আপনি যে পেজটি খুঁজছেন তা হারিয়ে গেছে <br> অথবা এই মুহূর্তে লিংকটি কাজ করছে না।</p>

        <a href="{{ url('/') }}" class="btn-home">
            <i class="fa-solid fa-house"></i> হোম পেজে ফিরে যান
        </a>

    </div>

</body>
</html>