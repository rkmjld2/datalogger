<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Datalogger Home</title>

    <style>

        /* Full page background and centering */
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background: linear-gradient(
                135deg,
                #74ebd5,
                #9face6
            );
        }

        /* Main content card */
        .container {
            width: 85%;
            max-width: 600px;
            padding: 35px;
            text-align: center;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
        }

        /* Main heading */
        h1 {
            color: #173b75;
            font-size: 32px;
            margin-bottom: 15px;
        }

        /* Description */
        p {
            color: #555;
            font-size: 18px;
            line-height: 1.6;
        }

        /* Section heading */
        h2 {
            color: #00796b;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        /* Links area */
        .links {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        /* Buttons */
        .btn {
            display: block;
            width: 100%;
            max-width: 300px;
            padding: 14px 10px;
            box-sizing: border-box;
            color: white;
            text-decoration: none;
            font-size: 17px;
            font-weight: bold;
            border-radius: 10px;
            transition: 0.3s;
        }

        /* Date-wise logs button */
        .btn-date {
            background-color: #007bff;
        }

        .btn-date:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Get logs button */
        .btn-api {
            background-color: #28a745;
        }

        .btn-api:hover {
            background-color: #1e7e34;
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            color: #777;
            font-size: 13px;
        }

        /* Mobile display */
        @media (max-width: 480px) {
            .container {
                width: 92%;
                padding: 25px 18px;
            }

            h1 {
                font-size: 26px;
            }

            p {
                font-size: 16px;
            }
        }

    </style>

</head>

<body>

    <div class="container">

        <h1>📊 Datalogger Project</h1>

        <p>
            Your datalogger web application
            is running successfully.
        </p>

        <h2>Available Pages</h2>

        <div class="links">

            <a href="view3_datewise.php"
               class="btn btn-date">
                📅 View Date-wise Logs
            </a>

            <a href="get_logs.php"
               class="btn btn-api">
                📋 Get Logs API
            </a>

        </div>

        <div class="footer">
            ESP8266 / ESP32 Datalogger System
        </div>

    </div>

</body>

</html>

