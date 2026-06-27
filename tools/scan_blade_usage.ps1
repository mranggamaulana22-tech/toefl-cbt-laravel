$root = Resolve-Path .\
$bladeFiles = Get-ChildItem -Path resources\views -Recurse -Filter *.blade.php
$searchPaths = @("$($root.Path)\app\**\*.php","$($root.Path)\routes\**\*.php","$($root.Path)\resources\views\**\*.blade.php")
$results = @()
foreach($f in $bladeFiles){
    $rel = $f.FullName.Substring($root.Path.Length+1) -replace '\\','/'
    $dot = $rel -replace '^resources/views/','' -replace '/','.' -replace '\.blade\.php$',''
    $foundPatterns = @()
    # Simple exact-string search for the dot-path across controllers, routes, and views
    if (Select-String -Path $searchPaths -Pattern $dot -SimpleMatch -Quiet){
        $foundPatterns += 'dot-match'
    }
    # Also check for component tag usage when applicable (components.foo -> <x-foo>)
    if ($dot -match '^components\.(.+)$'){
        $comp = $matches[1] -replace '\.','-'
        $xpat = "<x-$comp"
        if (Select-String -Path $searchPaths -Pattern $xpat -SimpleMatch -Quiet){
            $foundPatterns += $xpat
        }
    }
    $referenced = if ($foundPatterns.Count -gt 0) { 'Yes' } else { 'No' }
    $results += [pscustomobject]@{
        file = $rel
        dot = $dot
        referenced = $referenced
        found = ($foundPatterns -join ';')
    }
}
 $results = $results | Sort-Object -Property referenced -Descending
 $results = $results | Sort-Object -Property file
 $results | Export-Csv blade_unused_report.csv -NoTypeInformation -Encoding UTF8
Write-Output ('WROTE '+(Get-Item blade_unused_report.csv).FullName)
