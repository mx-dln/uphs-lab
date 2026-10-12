<#
.SYNOPSIS
    UPHS Computer Laboratory Management System - Graphical User Interface (GUI) Control Panel
.DESCRIPTION
    Standalone Windows Desktop GUI Launcher for 100% offline operation.
    Provides one-click controls to start/stop the local PHP web server, open the web app,
    launch student attendance kiosks, manage the offline database, and export property codes.
#>

Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing

[System.Windows.Forms.Application]::EnableVisualStyles()

# Global process tracking
$script:ServerProcess = $null
$script:ProjectDir = $PSScriptRoot
if (-not $script:ProjectDir) { $script:ProjectDir = Get-Location }

# Main Form
$form = New-Object System.Windows.Forms.Form
$form.Text = "UPHS Computer Laboratory Management System - Offline GUI Control Panel"
$form.Size = New-Object System.Drawing.Size(680, 560)
$form.StartPosition = "CenterScreen"
$form.FormBorderStyle = "FixedDialog"
$form.MaximizeBox = $false
$form.BackColor = [System.Drawing.Color]::FromArgb(248, 250, 252)
$form.Font = New-Object System.Drawing.Font("Segoe UI", 9)

# Header Panel
$headerPanel = New-Object System.Windows.Forms.Panel
$headerPanel.Dock = "Top"
$headerPanel.Height = 85
$headerPanel.BackColor = [System.Drawing.Color]::FromArgb(30, 58, 138) # Navy Blue
$form.Controls.Add($headerPanel)

$headerTitle = New-Object System.Windows.Forms.Label
$headerTitle.Text = "UNIVERSITY OF PERPETUAL HELP SYSTEM"
$headerTitle.Font = New-Object System.Drawing.Font("Segoe UI", 12, [System.Drawing.FontStyle]::Bold)
$headerTitle.ForeColor = [System.Drawing.Color]::White
$headerTitle.AutoSize = $true
$headerTitle.Location = New-Object System.Drawing.Point(20, 15)
$headerPanel.Controls.Add($headerTitle)

$headerSubtitle = New-Object System.Windows.Forms.Label
$headerSubtitle.Text = "Cauayan Campus &middot; Computer Laboratory Management System (Offline Standalone Suite)"
$headerSubtitle.Font = New-Object System.Drawing.Font("Segoe UI", 9)
$headerSubtitle.ForeColor = [System.Drawing.Color]::FromArgb(191, 219, 254)
$headerSubtitle.AutoSize = $true
$headerSubtitle.Location = New-Object System.Drawing.Point(22, 42)
$headerPanel.Controls.Add($headerSubtitle)

# Status Strip / Card
$statusCard = New-Object System.Windows.Forms.Panel
$statusCard.Location = New-Object System.Drawing.Point(20, 100)
$statusCard.Size = New-Object System.Drawing.Size(625, 55)
$statusCard.BackColor = [System.Drawing.Color]::White
$statusCard.BorderStyle = "FixedSingle"
$form.Controls.Add($statusCard)

$lblStatusTitle = New-Object System.Windows.Forms.Label
$lblStatusTitle.Text = "SYSTEM STATUS:"
$lblStatusTitle.Font = New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)
$lblStatusTitle.ForeColor = [System.Drawing.Color]::FromArgb(100, 116, 139)
$lblStatusTitle.Location = New-Object System.Drawing.Point(15, 17)
$lblStatusTitle.AutoSize = $true
$statusCard.Controls.Add($lblStatusTitle)

$lblStatusValue = New-Object System.Windows.Forms.Label
$lblStatusValue.Text = "OFFLINE SERVER STOPPED"
$lblStatusValue.Font = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)
$lblStatusValue.ForeColor = [System.Drawing.Color]::FromArgb(220, 38, 38) # Red
$lblStatusValue.Location = New-Object System.Drawing.Point(125, 16)
$lblStatusValue.AutoSize = $true
$statusCard.Controls.Add($lblStatusValue)

$lblDbMode = New-Object System.Windows.Forms.Label
$lblDbMode.Text = "Database: SQLite Offline Engine"
$lblDbMode.Font = New-Object System.Drawing.Font("Segoe UI", 8)
$lblDbMode.ForeColor = [System.Drawing.Color]::FromArgb(71, 85, 105)
$lblDbMode.Location = New-Object System.Drawing.Point(430, 18)
$lblDbMode.AutoSize = $true
$statusCard.Controls.Add($lblDbMode)

# Action Buttons Panel
$btnStart = New-Object System.Windows.Forms.Button
$btnStart.Text = "▶  Start Offline Server"
$btnStart.Location = New-Object System.Drawing.Point(20, 170)
$btnStart.Size = New-Object System.Drawing.Size(195, 42)
$btnStart.BackColor = [System.Drawing.Color]::FromArgb(22, 163, 74) # Emerald
$btnStart.ForeColor = [System.Drawing.Color]::White
$btnStart.FlatStyle = "Flat"
$btnStart.Font = New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)
$btnStart.Cursor = [System.Windows.Forms.Cursors]::Hand
$form.Controls.Add($btnStart)

$btnStop = New-Object System.Windows.Forms.Button
$btnStop.Text = "⏹  Stop Server"
$btnStop.Location = New-Object System.Drawing.Point(235, 170)
$btnStop.Size = New-Object System.Drawing.Size(195, 42)
$btnStop.BackColor = [System.Drawing.Color]::FromArgb(220, 38, 38) # Red
$btnStop.ForeColor = [System.Drawing.Color]::White
$btnStop.FlatStyle = "Flat"
$btnStop.Font = New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)
$btnStop.Enabled = $false
$btnStop.Cursor = [System.Windows.Forms.Cursors]::Hand
$form.Controls.Add($btnStop)

$btnOpenApp = New-Object System.Windows.Forms.Button
$btnOpenApp.Text = "🌐  Open Web Application"
$btnOpenApp.Location = New-Object System.Drawing.Point(450, 170)
$btnOpenApp.Size = New-Object System.Drawing.Size(195, 42)
$btnOpenApp.BackColor = [System.Drawing.Color]::FromArgb(37, 99, 235) # Blue
$btnOpenApp.ForeColor = [System.Drawing.Color]::White
$btnOpenApp.FlatStyle = "Flat"
$btnOpenApp.Font = New-Object System.Drawing.Font("Segoe UI", 9, [System.Drawing.FontStyle]::Bold)
$btnOpenApp.Cursor = [System.Windows.Forms.Cursors]::Hand
$form.Controls.Add($btnOpenApp)

# Row 2 Actions
$btnKiosk = New-Object System.Windows.Forms.Button
$btnKiosk.Text = "📱  Launch Student Kiosk"
$btnKiosk.Location = New-Object System.Drawing.Point(20, 222)
$btnKiosk.Size = New-Object System.Drawing.Size(195, 38)
$btnKiosk.BackColor = [System.Drawing.Color]::White
$btnKiosk.ForeColor = [System.Drawing.Color]::FromArgb(30, 41, 59)
$btnKiosk.FlatStyle = "Flat"
$btnKiosk.Cursor = [System.Windows.Forms.Cursors]::Hand
$form.Controls.Add($btnKiosk)

$btnInitDb = New-Object System.Windows.Forms.Button
$btnInitDb.Text = "🔄  Reset / Seed Offline DB"
$btnInitDb.Location = New-Object System.Drawing.Point(235, 222)
$btnInitDb.Size = New-Object System.Drawing.Size(195, 38)
$btnInitDb.BackColor = [System.Drawing.Color]::White
$btnInitDb.ForeColor = [System.Drawing.Color]::FromArgb(30, 41, 59)
$btnInitDb.FlatStyle = "Flat"
$btnInitDb.Cursor = [System.Windows.Forms.Cursors]::Hand
$form.Controls.Add($btnInitDb)

$btnPropertyPdf = New-Object System.Windows.Forms.Button
$btnPropertyPdf.Text = "📄  Export Property Tag PDF"
$btnPropertyPdf.Location = New-Object System.Drawing.Point(450, 222)
$btnPropertyPdf.Size = New-Object System.Drawing.Size(195, 38)
$btnPropertyPdf.BackColor = [System.Drawing.Color]::White
$btnPropertyPdf.ForeColor = [System.Drawing.Color]::FromArgb(30, 41, 59)
$btnPropertyPdf.FlatStyle = "Flat"
$btnPropertyPdf.Cursor = [System.Windows.Forms.Cursors]::Hand
$form.Controls.Add($btnPropertyPdf)

# Activity Log Box
$lblLog = New-Object System.Windows.Forms.Label
$lblLog.Text = "SYSTEM ACTIVITY LOG:"
$lblLog.Font = New-Object System.Drawing.Font("Segoe UI", 8, [System.Drawing.FontStyle]::Bold)
$lblLog.ForeColor = [System.Drawing.Color]::FromArgb(100, 116, 139)
$lblLog.Location = New-Object System.Drawing.Point(20, 275)
$lblLog.AutoSize = $true
$form.Controls.Add($lblLog)

$txtLog = New-Object System.Windows.Forms.TextBox
$txtLog.Multiline = $true
$txtLog.ReadOnly = $true
$txtLog.ScrollBars = "Vertical"
$txtLog.Location = New-Object System.Drawing.Point(20, 295)
$txtLog.Size = New-Object System.Drawing.Size(625, 175)
$txtLog.BackColor = [System.Drawing.Color]::FromArgb(15, 23, 42) # Slate 900
$txtLog.ForeColor = [System.Drawing.Color]::FromArgb(241, 245, 249) # Slate 100
$txtLog.Font = New-Object System.Drawing.Font("Consolas", 8.5)
$form.Controls.Add($txtLog)

# Footer Info
$lblFooter = New-Object System.Windows.Forms.Label
$lblFooter.Text = "UPHS Offline Suite &bull; Port: 8000 &bull; Local Host Standalone &bull; Built with PHP & SQLite"
$lblFooter.Font = New-Object System.Drawing.Font("Segoe UI", 7.5)
$lblFooter.ForeColor = [System.Drawing.Color]::FromArgb(148, 163, 184)
$lblFooter.Location = New-Object System.Drawing.Point(20, 485)
$lblFooter.AutoSize = $true
$form.Controls.Add($lblFooter)

function Write-GuiLog {
    param([string]$message)
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $txtLog.AppendText("[$timestamp] $message`r`n")
    $txtLog.SelectionStart = $txtLog.Text.Length
    $txtLog.ScrollToCaret()
}

# Event Handlers
$btnStart.Add_Click({
    try {
        if ($script:ServerProcess -and -not $script:ServerProcess.HasExited) {
            Write-GuiLog "Server is already running on http://localhost:8000"
            return
        }

        # Check existing process listening on port 8000
        $existingPort = Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue
        if ($existingPort) {
            Write-GuiLog "Notice: Port 8000 is already active. Checking application availability..."
            $lblStatusValue.Text = "RUNNING (PORT 8000)"
            $lblStatusValue.ForeColor = [System.Drawing.Color]::FromArgb(22, 163, 74)
            $btnStart.Enabled = $false
            $btnStop.Enabled = $true
            return
        }

        $pinfo = New-Object System.Diagnostics.ProcessStartInfo
        $pinfo.FileName = "php"
        $pinfo.Arguments = "-S localhost:8000 -t public"
        $pinfo.WorkingDirectory = $script:ProjectDir
        $pinfo.WindowStyle = [System.Diagnostics.ProcessWindowStyle]::Hidden
        $pinfo.CreateNoWindow = $true
        $pinfo.UseShellExecute = $false

        $script:ServerProcess = [System.Diagnostics.Process]::Start($pinfo)
        Start-Sleep -Milliseconds 800

        if (-not $script:ServerProcess.HasExited) {
            $lblStatusValue.Text = "RUNNING (http://localhost:8000)"
            $lblStatusValue.ForeColor = [System.Drawing.Color]::FromArgb(22, 163, 74)
            $btnStart.Enabled = $false
            $btnStop.Enabled = $true
            Write-GuiLog "Offline web server started successfully on http://localhost:8000"
            Write-GuiLog "Standalone mode: 100% offline without external internet or CDN requirements."
        } else {
            Write-GuiLog "Error: PHP server process exited immediately."
        }
    } catch {
        Write-GuiLog "Error starting server: $($_.Exception.Message)"
    }
})

$btnStop.Add_Click({
    try {
        if ($script:ServerProcess -and -not $script:ServerProcess.HasExited) {
            $script:ServerProcess.Kill()
            $script:ServerProcess = $null
        }

        # Stop any orphan PHP process on port 8000
        $portConn = Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue
        if ($portConn) {
            $pids = $portConn | Select-Object -ExpandProperty OwningProcess -Unique
            foreach ($p in $pids) {
                Stop-Process -Id $p -Force -ErrorAction SilentlyContinue
            }
        }

        $lblStatusValue.Text = "OFFLINE SERVER STOPPED"
        $lblStatusValue.ForeColor = [System.Drawing.Color]::FromArgb(220, 38, 38)
        $btnStart.Enabled = $true
        $btnStop.Enabled = $false
        Write-GuiLog "Offline web server stopped."
    } catch {
        Write-GuiLog "Error stopping server: $($_.Exception.Message)"
    }
})

$btnOpenApp.Add_Click({
    Write-GuiLog "Opening web application in default browser..."
    Start-Process "http://localhost:8000"
})

$btnKiosk.Add_Click({
    Write-GuiLog "Opening Student Attendance Kiosk in browser..."
    Start-Process "http://localhost:8000/student/login"
})

$btnPropertyPdf.Add_Click({
    Write-GuiLog "Exporting Property Tag Inventory PDF..."
    Start-Process "http://localhost:8000/equipment/export-pdf"
})

$btnInitDb.Add_Click({
    Write-GuiLog "Initializing / Reseeding offline database..."
    try {
        $p = Start-Process -FilePath "php" -ArgumentList "database/setup_offline.php" -WorkingDirectory $script:ProjectDir -NoNewWindow -PassThru -Wait
        if ($p.ExitCode -eq 0) {
            Write-GuiLog "Offline database migrated and verified successfully with demo data!"
        } else {
            Write-GuiLog "Warning: setup_offline.php returned exit code $($p.ExitCode)"
        }
    } catch {
        Write-GuiLog "Error running DB setup: $($_.Exception.Message)"
    }
})

# Form closing event: clean up background processes
$form.Add_FormClosing({
    if ($script:ServerProcess -and -not $script:ServerProcess.HasExited) {
        $ans = [System.Windows.Forms.MessageBox]::Show(
            "Do you want to stop the local offline PHP server before exiting?",
            "Exit UPHS Offline Suite",
            [System.Windows.Forms.MessageBoxButtons]::YesNoCancel,
            [System.Windows.Forms.MessageBoxIcon]::Question
        )

        if ($ans -eq [System.Windows.Forms.DialogResult]::Cancel) {
            $_.Cancel = $true
            return
        }

        if ($ans -eq [System.Windows.Forms.DialogResult]::Yes) {
            $script:ServerProcess.Kill()
        }
    }
})

# Initial Log Entry & Welcome
Write-GuiLog "UPHS Offline Suite GUI Control Panel loaded."
Write-GuiLog "Current Working Directory: $script:ProjectDir"

# Check if already running
$check = Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue
if ($check) {
    $lblStatusValue.Text = "RUNNING (PORT 8000)"
    $lblStatusValue.ForeColor = [System.Drawing.Color]::FromArgb(22, 163, 74)
    $btnStart.Enabled = $false
    $btnStop.Enabled = $true
    Write-GuiLog "Detected active server already running on port 8000."
} else {
    Write-GuiLog "Ready to start. Click 'Start Offline Server' to begin."
}

# Run Form
[void]$form.ShowDialog()
