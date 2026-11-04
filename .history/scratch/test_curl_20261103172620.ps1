try {
    $res = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/' -UseBasicParsing
    Write-Host "Status Code: $($res.StatusCode)"
    Write-Host "Content Length: $($res.Content.Length)"
    if ($res.Content -match '<title>(.*?)</title>') {
        Write-Host "Page Title: $($matches[1])"
    }
    if ($res.Content -match 'Discover Creativity') {
        Write-Host "Headline Verified: Discover Creativity, Support Local"
    }
} catch {
    Write-Host "Error occurred: $_"
}

$imgRes = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/images/kreva_logo.svg' -UseBasicParsing
Write-Host "Logo Status: $($imgRes.StatusCode)"
$prodRes = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/images/landing/product-batik.png' -UseBasicParsing
Write-Host "Product Image Status: $($prodRes.StatusCode)"

try {
    $resApache = Invoke-WebRequest -Uri 'http://localhost/kreva/public/' -UseBasicParsing -TimeoutSec 3
    Write-Host "Apache Status: $($resApache.StatusCode)"
} catch {
    Write-Host "Apache note: $_"
}
