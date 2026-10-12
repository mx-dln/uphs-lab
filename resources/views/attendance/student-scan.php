<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Scan QR - <?= escape($student['full_name']) ?></title>
    <script src="/assets/js/tailwind.min.js"></script>
    <script src="/assets/js/lucide.min.js"></script>
    <script src="/assets/js/html5-qrcode.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: { 50: '#eff6ff', 100: '#dbeafe', 500: '#2563eb', 600: '#1d4ed8' } },
                    fontFamily: { sans: ['-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; -webkit-tap-highlight-color: transparent; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 16px); }
        #qr-reader { width: 100% !important; border: none !important; }
        #qr-reader video { border-radius: 16px !important; }
        #qr-reader__dashboard_section_csr { display: none !important; }
        #qr-reader__dashboard_section { padding: 0 !important; }
        #qr-reader__scan_region { background: transparent !important; }
        #qr-reader img { display: none !important; }
        .animate-scan { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-200 px-4 py-2 flex items-center sticky top-0 z-30">
        <button onclick="history.back()" class="mr-3 text-gray-500">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </button>
        <img src="/assets/brand/Logo2025.png" alt="UPHS" class="h-8 mr-2">
        <span class="font-semibold text-gray-900 text-sm">Scan QR Code</span>
    </header>

    <main class="flex-1 overflow-y-auto pb-20 p-4">
        <div class="text-center mb-4">
            <p class="text-xs text-gray-500">Point your camera at a laboratory QR code</p>
        </div>

        <div id="qr-reader" class="bg-black rounded-2xl overflow-hidden shadow-lg mb-4 aspect-square max-w-sm mx-auto"></div>

        <div id="scanStatus" class="text-center hidden">
            <div class="animate-scan inline-flex items-center space-x-2 px-4 py-2 bg-blue-50 rounded-full">
                <i data-lucide="loader" class="w-4 h-4 text-primary-500 animate-spin"></i>
                <span class="text-sm text-primary-600">Processing...</span>
            </div>
        </div>

        <div id="scanResultCard" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-5 max-w-sm mx-auto">
            <div id="scanResultSuccess" class="hidden text-center">
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="check" class="w-7 h-7 text-green-600"></i>
                </div>
                <h3 class="font-bold text-gray-900">Attendance Recorded!</h3>
                <p id="scanLabName" class="text-sm text-gray-500 mt-1"></p>
                <p id="scanTime" class="text-xs text-gray-400 mt-1"></p>
                <button onclick="resetScanner()" class="mt-4 px-4 py-2 bg-primary-500 text-white rounded-xl text-sm font-medium">Scan Again</button>
            </div>
            <div id="scanResultError" class="hidden text-center">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="x" class="w-7 h-7 text-red-600"></i>
                </div>
                <h3 class="font-bold text-gray-900">Scan Failed</h3>
                <p id="scanErrorMessage" class="text-sm text-gray-500 mt-1"></p>
                <button onclick="resetScanner()" class="mt-4 px-4 py-2 bg-primary-500 text-white rounded-xl text-sm font-medium">Try Again</button>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-xs text-gray-400">You can also enter your student number at the lab room</p>
        </div>
    </main>

    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 pb-safe z-40">
        <div class="flex items-center justify-around h-16">
            <a href="/student/dashboard" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">Home</span>
            </a>
            <a href="/student/scan" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-primary-500 border-t-3 border-primary-500">
                <i data-lucide="camera" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5 font-medium">Scan</span>
            </a>
            <a href="/student/history" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400">
                <i data-lucide="clock" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">History</span>
            </a>
            <a href="/student/profile" class="flex flex-col items-center justify-center flex-1 h-full pt-1 text-gray-400">
                <i data-lucide="user" class="w-5 h-5"></i>
                <span class="text-xs mt-0.5">Profile</span>
            </a>
        </div>
    </nav>

    <script>
        lucide.createIcons();
        let html5QrCode = null;
        let isScanning = false;

        document.addEventListener('DOMContentLoaded', function() {
            startScanner();
        });

        function startScanner() {
            if (isScanning) return;
            isScanning = true;

            html5QrCode = new Html5Qrcode("qr-reader");

            Html5Qrcode.getCameras().then(devices => {
                const backCam = devices.find(d =>
                    d.label.toLowerCase().includes('back') ||
                    d.label.toLowerCase().includes('environment') ||
                    d.label.toLowerCase().includes('rear')
                );
                const cameraId = backCam ? backCam.id : devices[0]?.id;

                if (cameraId) {
                    html5QrCode.start(
                        { deviceId: cameraId },
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        onScanSuccess,
                        onScanFailure
                    ).catch(() => fallbackScanner());
                } else {
                    fallbackScanner();
                }
            }).catch(() => fallbackScanner());
        }

        function fallbackScanner() {
            html5QrCode.start(
                { facingMode: { exact: "environment" } },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                onScanFailure
            ).catch(() => {
                document.getElementById('scanResultCard').classList.remove('hidden');
                document.getElementById('scanResultError').classList.remove('hidden');
                document.getElementById('scanErrorMessage').textContent = 'Camera not available.';
                isScanning = false;
            });
        }

        function onScanSuccess(decodedText) {
            stopScanner();
            document.getElementById('scanStatus').classList.remove('hidden');
            document.getElementById('qr-reader').classList.add('hidden');

            const match = decodedText.match(/\/scan\/laboratory\/(\d+)/);
            if (match) {
                const labId = match[1];
                fetch('/attendance/scan/' + labId, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'student_number=<?= $student['student_number'] ?>'
                })
                .then(r => r.json())
                .then(data => {
                    document.getElementById('scanStatus').classList.add('hidden');
                    document.getElementById('scanResultCard').classList.remove('hidden');
                    if (data.success) {
                        document.getElementById('scanResultSuccess').classList.remove('hidden');
                        if (data.action === 'logout') {
                            document.querySelector('#scanResultSuccess h3').textContent = 'Timed Out!';
                            document.getElementById('scanLabName').textContent = data.message;
                            document.getElementById('scanTime').textContent = '';
                        } else {
                            document.querySelector('#scanResultSuccess h3').textContent = 'Timed In!';
                            document.getElementById('scanLabName').textContent = data.message;
                            document.getElementById('scanTime').textContent = '';
                        }
                        setTimeout(() => { window.location.replace('/student/dashboard'); }, 2000);
                    } else {
                        document.getElementById('scanResultError').classList.remove('hidden');
                        document.getElementById('scanErrorMessage').textContent = data.message;
                    }
                })
                .catch(() => {
                    document.getElementById('scanStatus').classList.add('hidden');
                    document.getElementById('scanResultCard').classList.remove('hidden');
                    document.getElementById('scanResultError').classList.remove('hidden');
                    document.getElementById('scanErrorMessage').textContent = 'Connection error.';
                });
            } else {
                document.getElementById('scanStatus').classList.add('hidden');
                document.getElementById('scanResultCard').classList.remove('hidden');
                document.getElementById('scanResultError').classList.remove('hidden');
                document.getElementById('scanErrorMessage').textContent = 'Not a valid lab QR code.';
            }
        }

        function onScanFailure(err) {}

        function stopScanner() {
            if (html5QrCode) {
                try {
                    html5QrCode.stop().then(() => {
                        html5QrCode.clear();
                        html5QrCode = null;
                        isScanning = false;
                    });
                } catch (e) {
                    html5QrCode = null;
                    isScanning = false;
                }
            }
        }

        function resetScanner() {
            document.getElementById('scanResultCard').classList.add('hidden');
            document.getElementById('scanResultSuccess').classList.add('hidden');
            document.getElementById('scanResultError').classList.add('hidden');
            document.getElementById('qr-reader').classList.remove('hidden');
            startScanner();
        }
    </script>
</body>
</html>
