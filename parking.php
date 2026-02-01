<?php
// Start session safely (no duplicate warning)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize parking slots if not set (10 slots)
if (!isset($_SESSION['parking'])) {
    $_SESSION['parking'] = array_fill(1, 10, "Empty");
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $car    = trim($_POST['car'] ?? '');

    // For park/remove we need a car number
    if ($action !== 'display' && $action !== 'next' && $car === '') {
        $msg = "Please enter a car number.";
    } else {
        if ($action === 'park') {
            foreach ($_SESSION['parking'] as $slot => $value) {
                if ($value === "Empty") {
                    $_SESSION['parking'][$slot] = $car;
                    $msg = "Car $car parked at Slot $slot.";
                    break;
                }
            }
            if ($msg === "") {
                $msg = "No empty slots!";
            }

        } elseif ($action === 'remove') {
            foreach ($_SESSION['parking'] as $slot => $value) {
                if ($value === $car) {
                    $_SESSION['parking'][$slot] = "Empty";
                    $msg = "Car $car removed from Slot $slot.";
                    break;
                }
            }
            if ($msg === "") {
                $msg = "Car not found!";
            }

        } elseif ($action === 'display') {
            $parts = [];
            foreach ($_SESSION['parking'] as $slot => $value) {
                $parts[] = "Slot $slot: $value";
            }
            $msg = implode(" | ", $parts);

        } elseif ($action === 'next') {
            $found = false;
            foreach ($_SESSION['parking'] as $slot => $value) {
                if ($value === "Empty") {
                    $msg = "Next empty slot is Slot $slot.";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $msg = "No empty slots!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parking System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top, #281b75 0%, #050b28 40%, #020415 100%);
            color: #fff;
        }

        .container {
            width: 100%;
            max-width: 900px;
            padding: 40px 24px;
        }

        .top-input {
            width: 100%;
            max-width: 760px;
            margin: 0 auto 40px auto;
            border-radius: 18px;
            border: 2px solid #8b3dff;
            padding: 18px 22px;
            font-size: 28px;
            color: #d56bff;
            background: transparent;
            outline: none;
            box-shadow: 0 0 24px rgba(139, 61, 255, 0.35);
        }

        .top-input::placeholder {
            color: #7a52ff;
        }

        .buttons-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(180px, 1fr));
            gap: 26px 40px;
            max-width: 760px;
            margin: 0 auto;
        }

        .btn {
            border: none;
            border-radius: 18px;
            font-size: 28px;
            font-weight: 600;
            color: #fff;
            padding: 26px 10px;
            cursor: pointer;
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.4);
            transition: transform 0.09s ease, box-shadow 0.09s ease, filter 0.15s ease;
        }

        .btn:active {
            transform: translateY(2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.5);
            filter: brightness(0.95);
        }

        .btn-park {
            background: linear-gradient(135deg, #ff2e92, #f94b5e);
        }

        .btn-remove {
            background: linear-gradient(135deg, #ff9800, #ff6a00);
        }

        .btn-display {
            background: linear-gradient(135deg, #2979ff, #00b0ff);
        }

        .btn-next {
            background: linear-gradient(135deg, #00c853, #00b248);
        }

        .message {
            margin-top: 26px;
            text-align: center;
            font-size: 16px;
            color: #f5f5f5;
            min-height: 20px;
        }

        @media (max-width: 720px) {
            .top-input {
                font-size: 20px;
                padding: 14px 18px;
            }

            .btn {
                font-size: 20px;
                padding: 18px 10px;
            }

            .buttons-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <form action="" method="post">
        <input
            type="text"
            name="car"
            class="top-input"
            placeholder="Enter car number"
        >

        <div class="buttons-grid">
            <button type="submit" name="action" value="park" class="btn btn-park">Park</button>
            <button type="submit" name="action" value="remove" class="btn btn-remove">Remove Car</button>
            <button type="submit" name="action" value="display" class="btn btn-display">Display</button>
            <button type="submit" name="action" value="next" class="btn btn-next">Next Empty Slot</button>
        </div>
    </form>

    <div class="message">
        <?php if ($msg !== "") echo htmlspecialchars($msg); ?>
    </div>
</div>
</body>
</html>
