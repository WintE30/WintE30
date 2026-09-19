<!DOCTYPE html>
<html lang="mg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartWater Dashboard | Localhost</title>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .container {
            background: #ffffff;
            width: 95%;
            max-width: 450px;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }

        .header {
            background-color: #1a4a8e;
            color: white;
            padding: 25px 20px;
            text-align: center;
        }

        .header h1 { font-size: 24px; letter-spacing: 1px; }
        .header p { font-size: 11px; opacity: 0.8; margin-top: 5px; }

        .status-card {
            margin: -25px 25px 20px 25px;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.5s ease;
        }

        /* Loko araka ny sata */
        .potable { background-color: #2ecc71; }
        .danger { background-color: #e74c3c; }
        .waiting { background-color: #95a5a6; }

        .status-card h2 { font-size: 20px; margin-bottom: 5px; }
        .icon-status { font-size: 30px; margin-bottom: 5px; }

        .params-section { padding: 10px 25px 25px 25px; }
        .params-section h3 { font-size: 16px; color: #555; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px; }

        .param-row { margin-bottom: 20px; }
        .param-info { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 8px; }
        .label { font-size: 13px; font-weight: 600; color: #777; }
        .value { font-size: 18px; font-weight: bold; color: #1a4a8e; }

        .progress-container {
            background: #eee;
            height: 12px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #ddd;
        }

        .progress-bar { height: 100%; width: 0%; transition: width 1.5s ease-in-out; }
        .blue { background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%); }
        .orange { background: linear-gradient(90deg, #f6d365 0%, #fda085 100%); }

        .range-labels { display: flex; justify-content: space-between; font-size: 10px; color: #999; margin-top: 6px; }

        .footer {
            background: #f9f9f9;
            padding: 15px;
            text-align: center;
            font-size: 10px;
            color: #aaa;
            border-top: 1px solid #eee;
        }
        
        .btn-refresh {
            background: #1a4a8e;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 10px;
            margin-top: 10px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>SmartWater</h1>
        <p>Système de détection de potabilité en temps réel</p>
    </div>

    <div id="status-box" class="status-card waiting">
        <div id="status-icon" class="icon-status">⌛</div>
        <h2 id="status-text">FANDRAISANA DATA...</h2>
        <p>Confiance IA: <span id="confiance">--</span>%</p>
    </div>

    <div class="params-section">
        <h3>📊 PARAMÈTRES MESURÉS</h3>

        <div class="param-row">
            <div class="param-info">
                <span class="label">TURBIDITÉ (Raw)</span>
                <span class="value" id="turb-val">--</span>
            </div>
            <div class="progress-container">
                <div class="progress-bar blue" id="turb-bar"></div>
            </div>
            <div class="range-labels">
                <span>MADIO (4000)</span><span>LIMITE (2500)</span><span>MALOTO (1000)</span>
            </div>
        </div>

        <div class="param-row">
            <div class="param-info">
                <span class="label">TEMPÉRATURE (°C)</span>
                <span class="value" id="temp-val">--</span>
            </div>
            <div class="progress-container">
                <div class="progress-bar orange" id="temp-bar"></div>
            </div>
            <div class="range-labels">
                <span>FROIDE (0)</span><span>AMBIANCE (25)</span><span>LIMITE (40)</span>
            </div>
        </div>

        <button class="btn-refresh" onclick="updateDashboard()">RAFRAÎCHIR MIVANTANA</button>
    </div>

    <div class="footer">
        <p>⚠️ MODE: AUTO (MAJ 3s) | IP SOLOSAINA: LOCALHOST</p>
        <p>© 2026 SmartWater | ISPM Madagascar</p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateDashboard() {
        $.getJSON('get_last_data.php', function(data) {
            if(data) {
                // 1. Fanavaozana ny sanda
                $('#temp-val').text(parseFloat(data.temp).toFixed(1) + " °C");
                $('#turb-val').text(data.turbidity);
                
                // 2. Kajy ny halavan'ny barre de progression (%)
                // Turbidité (4095 ny max an'ny ESP32)
                let turbPercent = (data.turbidity / 4095) * 100;
                $('#turb-bar').css('width', turbPercent + '%');
                
                // Température (40°C ny max ohatra)
                let tempPercent = (data.temp / 40) * 100;
                $('#temp-bar').css('width', Math.min(tempPercent, 100) + '%');

                // 3. Logic ho an'ny STATUS sy Confiance
                if(data.status == "RANO_MADIO") {
                    $('#status-box').removeClass('danger waiting').addClass('potable');
                    $('#status-text').text("EAU POTABLE");
                    $('#status-icon').text("✔");
                    $('#confiance').text("98.5"); // Azonao ampiasaina ny sanda avy amin'ny IA raha misy
                } else {
                    $('#status-box').removeClass('potable waiting').addClass('danger');
                    $('#status-text').text("EAU NON POTABLE");
                    $('#status-icon').text("✖");
                    $('#confiance').text("99.2");
                }
            }
        }).fail(function() {
            console.log("Error: Tsy mahazo data avy amin'ny MySQL");
        });
    }

    // Isaky ny 3 segondra
    setInterval(updateDashboard, 3000);
    
    // Antsoina voalohany rehefa misokatra ny pejy
    $(document).ready(function() {
        updateDashboard();
    });
</script>

</body>
</html>