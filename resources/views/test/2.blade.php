<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Web Terminal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background-color: #1a1a1a;
            color: #00ff00;
            height: 100vh;
            overflow: hidden;
        }

        .terminal-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .terminal-header {
            background-color: #333;
            padding: 10px;
            border-bottom: 1px solid #555;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .terminal-title {
            color: #fff;
            font-weight: bold;
        }

        .terminal-controls {
            display: flex;
            gap: 5px;
        }

        .control-btn {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
        }

        .close { background-color: #ff5f56; }
        .minimize { background-color: #ffbd2e; }
        .maximize { background-color: #27ca3f; }

        .terminal-output {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #000;
            font-size: 14px;
            line-height: 1.4;
        }

        .terminal-line {
            margin-bottom: 5px;
        }

        .terminal-prompt {
            color: #00ff00;
        }

        .terminal-command {
            color: #ffffff;
        }

        .terminal-output-text {
            color: #cccccc;
            white-space: pre-wrap;
        }

        .terminal-error {
            color: #ff6b6b;
        }

        .terminal-input-container {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: #000;
            border-top: 1px solid #333;
        }

        .terminal-prompt-symbol {
            color: #00ff00;
            margin-right: 10px;
        }

        .terminal-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #ffffff;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            outline: none;
        }

        .terminal-input::placeholder {
            color: #666;
        }

        .loading {
            color: #ffbd2e;
        }

        .cursor {
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        .history-item {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="terminal-container">
    <div class="terminal-header">
        <div class="terminal-title">Web Terminal - {{ config('app.name') }}</div>
        <div class="terminal-controls">
            <button class="control-btn close" onclick="window.close()"></button>
            <button class="control-btn minimize"></button>
            <button class="control-btn maximize" onclick="toggleFullscreen()"></button>
        </div>
    </div>

    <div class="terminal-output" id="terminal-output">
        <div class="terminal-line">
            <span class="terminal-output-text">Welcome to Web Terminal</span>
        </div>
        <div class="terminal-line">
            <span class="terminal-output-text">Type 'help' to see available commands</span>
        </div>
        <div class="terminal-line">
            <span class="terminal-output-text">Working directory: {{ base_path() }}</span>
        </div>
        <br>
    </div>

    <div class="terminal-input-container">
        <span class="terminal-prompt-symbol">$</span>
        <input type="text" class="terminal-input" id="terminal-input" placeholder="Enter command..." autocomplete="off">
    </div>
</div>

<script>
    class WebTerminal {
        constructor() {
            this.output = document.getElementById('terminal-output');
            this.input = document.getElementById('terminal-input');
            this.commandHistory = [];
            this.historyIndex = -1;
            this.isProcessing = false;

            this.setupEventListeners();
            this.focusInput();
        }

        setupEventListeners() {
            this.input.addEventListener('keydown', (e) => this.handleKeyDown(e));
            document.addEventListener('click', () => this.focusInput());

            // Prevent page refresh on F5
            document.addEventListener('keydown', (e) => {
                if (e.key === 'F5') {
                    e.preventDefault();
                }
            });
        }

        handleKeyDown(e) {
            if (this.isProcessing) return;

            switch (e.key) {
                case 'Enter':
                    e.preventDefault();
                    this.executeCommand();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.navigateHistory(-1);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    this.navigateHistory(1);
                    break;
                case 'Tab':
                    e.preventDefault();
                    // Could implement tab completion here
                    break;
                case 'l':
                    if (e.ctrlKey) {
                        e.preventDefault();
                        this.clearScreen();
                    }
                    break;
            }
        }

        navigateHistory(direction) {
            if (this.commandHistory.length === 0) return;

            if (direction === -1) {
                this.historyIndex = Math.min(this.historyIndex + 1, this.commandHistory.length - 1);
            } else {
                this.historyIndex = Math.max(this.historyIndex - 1, -1);
            }

            if (this.historyIndex === -1) {
                this.input.value = '';
            } else {
                this.input.value = this.commandHistory[this.commandHistory.length - 1 - this.historyIndex];
            }
        }

        async executeCommand() {
            const command = this.input.value.trim();

            if (!command) return;

            // Add to history
            this.commandHistory.push(command);
            this.historyIndex = -1;

            // Display command in output
            this.addToOutput(`$ ${command}`, 'terminal-command');

            // Handle built-in commands
            if (this.handleBuiltInCommands(command)) {
                this.input.value = '';
                return;
            }

            this.isProcessing = true;
            this.input.disabled = true;
            this.addToOutput('Processing...', 'loading');

            try {
                const response = await fetch('{{ route('exc') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ command: command })
                });

                const result = await response.json();

                // Remove loading message
                this.removeLastLine();

                if (result.error) {
                    this.addToOutput(result.output, 'terminal-error');
                } else {
                    this.addToOutput(result.output, 'terminal-output-text');
                }

            } catch (error) {
                this.removeLastLine();
                this.addToOutput(`Error: ${error.message}`, 'terminal-error');
            }

            this.isProcessing = false;
            this.input.disabled = false;
            this.input.value = '';
            this.focusInput();
        }

        handleBuiltInCommands(command) {
            const cmd = command.toLowerCase().trim();

            switch (cmd) {
                case 'help':
                    this.addToOutput(`Available commands:
- ls: List directory contents
- pwd: Show current directory
- php artisan: Run Laravel Artisan commands
- composer: Run Composer commands
- git: Git commands
- cat: Display file contents
- clear: Clear the terminal
- help: Show this help message

Note: Some commands are restricted for security reasons.`, 'terminal-output-text');
                    return true;

                case 'clear':
                    this.clearScreen();
                    return true;

                default:
                    return false;
            }
        }

        addToOutput(text, className = 'terminal-output-text') {
            const line = document.createElement('div');
            line.className = `terminal-line ${className}`;
            line.textContent = text;
            this.output.appendChild(line);
            this.scrollToBottom();
        }

        removeLastLine() {
            const lastLine = this.output.lastElementChild;
            if (lastLine) {
                lastLine.remove();
            }
        }

        clearScreen() {
            this.output.innerHTML = '';
            this.addToOutput('Terminal cleared', 'terminal-output-text');
        }

        scrollToBottom() {
            this.output.scrollTop = this.output.scrollHeight;
        }

        focusInput() {
            this.input.focus();
        }
    }

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    }

    // Initialize terminal when page loads
    document.addEventListener('DOMContentLoaded', () => {
        new WebTerminal();
    });
</script>
</body>
</html>
