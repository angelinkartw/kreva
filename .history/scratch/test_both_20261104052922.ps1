$r1 = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/' -UseBasicParsing
$r2 = Invoke-WebRequest -Uri 'http://localhost/kreva/public/' -UseBasicParsing
Write-Host "Port 8000: $($r1.StatusCode) - Length: $($r1.Content.Length)"
Write-Host "XAMPP Apache: $($r2.StatusCode) - Length: $($r2.Content.Length)"

