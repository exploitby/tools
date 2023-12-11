<?php
function fetchProxies($sourceUrls) {
    $proxies = [];

    foreach ($sourceUrls as $url) {
        $content = file_get_contents($url);

        preg_match_all('/(\d+\.\d+\.\d+\.\d+:\d+)/', $content, $matches);
        
        if (!empty($matches[1])) {
            $proxies = array_merge($proxies, $matches[1]);
        }
    }

    return $proxies;
}

function generateProxyList() {
    $sourceUrls = [
        'https://www.proxy-list.download/HTTP',
    ];

    $proxies = fetchProxies($sourceUrls);

    // Output the proxy list
    foreach ($proxies as $proxy) {
        echo $proxy . '<br>';
    }
}

if (isset($_POST['generateProxyList'])) {
    generateProxyList();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proxy List Generator</title>
</head>
<body>
    <h1>Proxy List Generator</h1>
    <form method="post" action="">
        <button type="submit" name="generateProxyList">Generate Proxy List</button>
    </form>
</body>
</html>
