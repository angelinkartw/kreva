Add-Type -AssemblyName System.Drawing
$img = [System.Drawing.Image]::FromFile('C:\kreva\public\images\craft1.jpg')
Write-Host "craft1 dimensions: $($img.Width) x $($img.Height)"
$img.Dispose()

