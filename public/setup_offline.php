<?php
/**
 * Script to automatically download CDN assets to local storage for offline mode.
 * 
 * Instructions:
 * 1. Open this file in your browser: e.g. http://localhost/setup_offline.php or https://your-ngrok.dev/setup_offline.php
 * 2. Wait for it to say "Success".
 */

set_time_limit(120); // allow 2 mins

$baseDir = __DIR__ . '/assets';
$cssDir = $baseDir . '/css';
$jsDir = $baseDir . '/js';
$fontsDir = $cssDir . '/fonts';

// Create directories
@mkdir($cssDir, 0777, true);
@mkdir($jsDir, 0777, true);
@mkdir($fontsDir, 0777, true);

function downloadFile($url, $destination) {
    if (file_exists($destination)) {
        return "Already exists: " . basename($destination);
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // Some endpoints (like Google Fonts) return different formats based on User-Agent.
    // We send a modern Chrome user agent to get WOFF2 formats.
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    
    $data = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($error || $httpCode !== 200 || !$data) {
        return "Failed to download $url - Error: $error - HTTP Code: $httpCode";
    }
    
    file_put_contents($destination, $data);
    return "Downloaded: " . basename($destination);
}

$logs = [];

// 1. Download JS files
$logs[] = downloadFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', $jsDir . '/bootstrap.bundle.min.js');
$logs[] = downloadFile('https://cdn.jsdelivr.net/npm/chart.js', $jsDir . '/chart.min.js');
$logs[] = downloadFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', $jsDir . '/sweetalert2.all.min.js');

// 2. Download CSS files
$logs[] = downloadFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', $cssDir . '/bootstrap.min.css');

// Bootstrap Icons CSS needs special handling because it references fonts
$biCssUrl = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css';
$biCssPath = $cssDir . '/bootstrap-icons.min.css';
if (!file_exists($biCssPath)) {
    $biCss = file_get_contents($biCssUrl);
    if ($biCss) {
        // Find urls like "./fonts/bootstrap-icons.woff2"
        preg_match_all('/url\((.*?)\)/', $biCss, $matches);
        foreach ($matches[1] as $fontUrl) {
            $fontUrl = trim($fontUrl, "'\"");
            // fontUrl is relative, e.g. "./fonts/bootstrap-icons.woff2?856008caa5eb66df68595e734e59580d"
            $cleanFontUrl = explode('?', $fontUrl)[0]; // remove query string
            $fontFile = basename($cleanFontUrl); // bootstrap-icons.woff2
            
            // Build absolute URL for download
            $absoluteFontUrl = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/' . $fontFile;
            $logs[] = downloadFile($absoluteFontUrl, $fontsDir . '/' . $fontFile);
        }
        
        file_put_contents($biCssPath, $biCss);
        $logs[] = "Downloaded & Processed: bootstrap-icons.min.css";
    } else {
        $logs[] = "Failed to download Bootstrap Icons CSS";
    }
} else {
    $logs[] = "Already exists: bootstrap-icons.min.css";
}

// 3. Download Google Fonts (Inter)
$interCssUrl = 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap';
$interCssPath = $cssDir . '/inter.css';

if (!file_exists($interCssPath)) {
    // Must use cURL with specific User-Agent to ensure WOFF2 response
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $interCssUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    $interCss = curl_exec($ch);
    curl_close($ch);

    if ($interCss) {
        preg_match_all('/url\((https:\/\/fonts\.gstatic\.com\/.*?)\)/', $interCss, $matches);
        foreach ($matches[1] as $fontUrl) {
            $fontFile = basename(parse_url($fontUrl, PHP_URL_PATH));
            $logs[] = downloadFile($fontUrl, $fontsDir . '/' . $fontFile);
            
            // Replace absolute URL with local relative URL
            $interCss = str_replace($fontUrl, './fonts/' . $fontFile, $interCss);
        }
        
        file_put_contents($interCssPath, $interCss);
        $logs[] = "Downloaded & Processed: inter.css";
    } else {
        $logs[] = "Failed to download Google Fonts CSS";
    }
} else {
    $logs[] = "Already exists: inter.css";
}

echo "<h2>Setup Offline Mode: Complete!</h2>";
echo "<ul>";
foreach ($logs as $log) {
    $color = str_contains($log, 'Failed') ? 'red' : 'green';
    echo "<li style='color: {$color}'>$log</li>";
}
echo "</ul>";
echo "<p>Next step: Return to the AI so it can update your Blade templates to use these local files!</p>";
