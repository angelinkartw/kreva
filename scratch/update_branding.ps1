$files = Get-ChildItem -Path 'resources/views/*.blade.php'
foreach ($f in $files) {
    $content = [System.IO.File]::ReadAllText($f.FullName)
    $newContent = $content.Replace('images/sisehat_logo.svg', 'images/kreva_logo.svg')
    $newContent = $newContent.Replace('alt="SiSehat"', 'alt="KREVA"')
    $newContent = $newContent.Replace('SISEHAT', 'KREVA')
    $newContent = $newContent.Replace('SiSehat - Analitik UMKM', 'KREVA - Platform UMKM')
    if ($content -ne $newContent) {
        [System.IO.File]::WriteAllText($f.FullName, $newContent)
        Write-Host "Updated $($f.Name)"
    }
}
Write-Host "Finished branding update."

