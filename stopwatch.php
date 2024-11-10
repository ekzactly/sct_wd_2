<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attractive Interactive Stopwatch</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #ecf0f1;
            --text-color: #34495e;
            --shadow-color: rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .stopwatch {
            background-color: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px var(--shadow-color);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stopwatch:hover {
            transform: translateY(-5px);
        }

        #display {
            font-size: 64px;
            font-weight: 300;
            margin-bottom: 30px;
            color: var(--primary-color);
            text-shadow: 2px 2px 4px var(--shadow-color);
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        button {
            padding: 12px 25px;
            font-size: 18px;
            cursor: pointer;
            border: none;
            border-radius: 50px;
            background-color: var(--primary-color);
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px var(--shadow-color);
        }

        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px var(--shadow-color);
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px var(--shadow-color);
        }

        button:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        #lapButton {
            background-color: var(--secondary-color);
        }

        #lapButton:hover {
            background-color: #27ae60;
        }

        #lapTimes {
            max-height: 200px;
            overflow-y: auto;
            border-top: 2px solid var(--background-color);
            padding-top: 20px;
            margin-top: 20px;
        }

        #lapTimes h3 {
            margin-top: 0;
            color: var(--primary-color);
        }

        #lapTimes ol {
            padding-left: 20px;
            margin: 0;
        }

        #lapTimes li {
            margin-bottom: 10px;
            font-size: 16px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="stopwatch">
        <div id="display">00:00:00.000</div>
        <div class="buttons">
            <button id="startPause">Start</button>
            <button id="reset" disabled>Reset</button>
            <button id="lap" disabled>Lap</button>
        </div>
        <div id="lapTimes"></div>
    </div>

    <script>
        class Stopwatch {
            constructor(display, startPauseButton, resetButton, lapButton, lapTimesDiv) {
                this.display = display;
                this.startPauseButton = startPauseButton;
                this.resetButton = resetButton;
                this.lapButton = lapButton;
                this.lapTimesDiv = lapTimesDiv;

                this.startTime = 0;
                this.elapsedTime = 0;
                this.timerInterval = null;
                this.running = false;
                this.laps = [];

                this.startPauseButton.addEventListener('click', () => this.startPause());
                this.resetButton.addEventListener('click', () => this.reset());
                this.lapButton.addEventListener('click', () => this.lap());
            }

            startPause() {
                if (!this.running) {
                    this.startTime = Date.now() - this.elapsedTime;
                    this.timerInterval = setInterval(() => this.updateDisplay(), 10);
                    this.startPauseButton.textContent = 'Pause';
                    this.resetButton.disabled = false;
                    this.lapButton.disabled = false;
                } else {
                    clearInterval(this.timerInterval);
                    this.startPauseButton.textContent = 'Resume';
                }
                this.running = !this.running;
            }

            reset() {
                clearInterval(this.timerInterval);
                this.elapsedTime = 0;
                this.display.textContent = '00:00:00.000';
                this.startPauseButton.textContent = 'Start';
                this.resetButton.disabled = true;
                this.lapButton.disabled = true;
                this.running = false;
                this.laps = [];
                this.updateLapTimes();
            }

            lap() {
                if (this.running) {
                    this.laps.push(this.elapsedTime);
                    this.updateLapTimes();
                }
            }

            updateDisplay() {
                this.elapsedTime = Date.now() - this.startTime;
                this.display.textContent = this.formatTime(this.elapsedTime);
            }

            formatTime(time) {
                let date = new Date(time);
                let minutes = date.getUTCMinutes().toString().padStart(2, '0');
                let seconds = date.getUTCSeconds().toString().padStart(2, '0');
                let milliseconds = date.getUTCMilliseconds().toString().padStart(3, '0');
                return `${minutes}:${seconds}.${milliseconds}`;
            }

            updateLapTimes() {
                this.lapTimesDiv.innerHTML = '<h3>Lap Times</h3>';
                if (this.laps.length === 0) {
                    this.lapTimesDiv.innerHTML += '<p>No laps recorded</p>';
                } else {
                    let lapList = '<ol>';
                    this.laps.forEach((lapTime, index) => {
                        lapList += `<li>Lap ${index + 1}: ${this.formatTime(lapTime)}</li>`;
                    });
                    lapList += '</ol>';
                    this.lapTimesDiv.innerHTML += lapList;
                }
            }
        }

        // Initialize the stopwatch when the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', () => {
            const display = document.getElementById('display');
            const startPauseButton = document.getElementById('startPause');
            const resetButton = document.getElementById('reset');
            const lapButton = document.getElementById('lap');
            const lapTimesDiv = document.getElementById('lapTimes');

            new Stopwatch(display, startPauseButton, resetButton, lapButton, lapTimesDiv);
        });
    </script>
</body>
</html>
