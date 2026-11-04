$c = [System.IO.File]::ReadAllText('resources/views/welcome.blade.php')
$openAuth = ([regex]::Matches($c, '@auth')).Count
$closeAuth = ([regex]::Matches($c, '@endauth')).Count
$openIf = ([regex]::Matches($c, '@if')).Count
$closeIf = ([regex]::Matches($c, '@endif')).Count
Write-Host "auth tags: $openAuth open, $closeAuth close"
Write-Host "if tags: $openIf open, $closeIf close"

$files = @(
    'public/images/kreva_logo.svg',
    'public/images/kreva_logo_white.svg',
    'public/images/landing/hero-composition.png',
    'public/images/landing/product-batik.png',
    'public/images/landing/product-tas.png',
    'public/images/landing/product-keripik.png',
    'public/images/landing/product-cangkir.png',
    'public/images/landing/banner-umkm.png',
    'public/images/landing/banner-survey.png',
    'public/images/landing/seller-srikandi.png',
    'public/images/landing/seller-dapoer.png',
    'public/images/landing/seller-anyaman.png',
    'public/images/landing/seller-lestari.png',
    'public/images/landing/footer-watermark.png'
)
foreach ($f in $files) {
    Write-Host "$f : $(Test-Path $f)"
}
