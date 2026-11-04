Add-Type -AssemblyName System.Drawing
if (-not (Test-Path 'public/images/landing')) {
    New-Item -ItemType Directory -Force -Path 'public/images/landing'
}

$srcPath = 'C:/Users/Angelina Eka Pratiwi/.gemini/antigravity/brain/e130ea3f-256b-4e42-822d-8fc56214d0bd/.user_uploaded/media_1793696303951_463e3ecc.jpg'
$src = [System.Drawing.Image]::FromFile($srcPath)

function Save-Crop([int]$x, [int]$y, [int]$w, [int]$h, [string]$dest) {
    $bmp = New-Object System.Drawing.Bitmap $w, $h
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $srcRect = New-Object System.Drawing.Rectangle $x, $y, $w, $h
    $destRect = New-Object System.Drawing.Rectangle 0, 0, $w, $h
    $g.DrawImage($src, $destRect, $srcRect, [System.Drawing.GraphicsUnit]::Pixel)
    $bmp.Save($dest, [System.Drawing.Imaging.ImageFormat]::Png)
    $g.Dispose()
    $bmp.Dispose()
    Write-Host "Saved $dest ($w x $h)"
}

# Image is 682 x 1024
# Hero image area: right side of hero section
# x: approx 240 to 682, y: approx 60 to 256
Save-Crop 240 60 440 196 'public/images/landing/hero-composition.png'

# Product cards images:
# Card 1: Batik Tulis Parang - around x: 29, y: 420, w: 148, h: 104
Save-Crop 29 420 148 104 'public/images/landing/product-batik.png'
# Card 2: Tas Anyaman Rotan - around x: 190, y: 420, w: 148, h: 104
Save-Crop 190 420 148 104 'public/images/landing/product-tas.png'
# Card 3: Keripik Tempe - around x: 349, y: 420, w: 148, h: 104
Save-Crop 349 420 148 104 'public/images/landing/product-keripik.png'
# Card 4: Cangkir Keramik Motif - around x: 508, y: 420, w: 148, h: 104
Save-Crop 508 420 148 104 'public/images/landing/product-cangkir.png'

# Banner 1 UMKM Woman: around x: 260, y: 610, w: 145, h: 125
Save-Crop 260 610 145 125 'public/images/landing/banner-umkm.png'
# Banner 2 Survey: around x: 566, y: 624, w: 100, h: 110
Save-Crop 566 610 100 125 'public/images/landing/banner-survey.png'

# Sellers logos:
# Seller 1: Srikandi Craft - circle around x: 28, y: 788, w: 42, h: 42
Save-Crop 28 788 42 42 'public/images/landing/seller-srikandi.png'
# Seller 2: Dapoer Nusantara - around x: 192, y: 788, w: 42, h: 42
Save-Crop 192 788 42 42 'public/images/landing/seller-dapoer.png'
# Seller 3: Anyaman Kita - around x: 355, y: 788, w: 42, h: 42
Save-Crop 355 788 42 42 'public/images/landing/seller-anyaman.png'
# Seller 4: Batik Lestari - around x: 518, y: 788, w: 42, h: 42
Save-Crop 518 788 42 42 'public/images/landing/seller-lestari.png'

# Logo: top-left
Save-Crop 20 8 120 30 'public/images/landing/logo-crop.png'

# Watermark batik bottom right
Save-Crop 500 900 182 124 'public/images/landing/footer-watermark.png'

$src.Dispose()
Write-Host 'All crops done successfully!'
