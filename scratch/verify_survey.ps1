$r = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/' -UseBasicParsing
Write-Host "Status: $($r.StatusCode)"
if ($r.Content -match 'surveyOpen') { Write-Host "Survey state: FOUND" } else { Write-Host "Survey state: NOT FOUND" }
if ($r.Content -match 'Kuesioner UMKM') { Write-Host "Survey modal: FOUND" } else { Write-Host "Survey modal: NOT FOUND" }
if ($r.Content -match 'surveyStep') { Write-Host "Survey steps: FOUND" } else { Write-Host "Survey steps: NOT FOUND" }

