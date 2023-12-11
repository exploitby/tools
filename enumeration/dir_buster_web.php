<?php
ob_implicit_flush(true);
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetUrl = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);
    $wordlistUrl = filter_input(INPUT_POST, 'wordlist', FILTER_VALIDATE_URL);

    if ($targetUrl && $wordlistUrl) {
        $targetUrl = rtrim($targetUrl, '/');
        $wordlist = file($wordlistUrl, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!$wordlist) {
            die('Failed to load the wordlist.');
        }

        function enumerateDirectories($url, $wordlist) {
            foreach ($wordlist as $entry) {
                $fullUrl = $url . '/' . $entry;
                $ch = curl_init($fullUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
                curl_close($ch);

                // Output results for specified HTTP codes
                if (in_array($httpCode, [200, 301, 302, 403])) {
                    echo "<tr><td><a href='$fullUrl' target=_blank>$fullUrl</a></td><td>$httpCode</td><td>$contentLength</td></tr>";
                    echo str_pad('', 4096);  // Flush the output buffer
                    ob_flush();
                    flush();
                }

                // Recursion for directories
                if ($httpCode === 200 && is_dir($fullUrl)) {
                    enumerateDirectories($fullUrl, $wordlist);
                }
            }
        }

        // Start enumeration
        echo '<table border="1">';
        echo '<tr><th>URL</th><th>Response Code</th><th>Response Length</th></tr>';
        enumerateDirectories($targetUrl, $wordlist);
        echo '</table>';
    } else {
        echo 'Invalid URL inputs.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directory Enumeration</title>
</head>
<body>
    <h1>Directory Enumeration</h1>

    <form method="post" action="">
        <label for="url">Target URL:</label>
        <input type="url" name="url" placeholder="Enter URL" required>
        <br>

        <label for="wordlist">Wordlist URL:</label>
        <input type="url" name="wordlist" placeholder="Enter Wordlist URL" required>
        <br>

        <button type="submit">START</button>
    </form><hr>
    Useful dictionaries:
    <ul>
        <li><a href="https://raw.githubusercontent.com/Bo0oM/fuzz.txt/master/fuzz.txt" target="_blank">fuzz.txt</a></li>
        <li><a href="https://raw.githubusercontent.com/v0re/dirb/master/wordlists/common.txt" target="_blank">Common</a></li>
        <li><a href="https://raw.githubusercontent.com/xajkep/wordlists/master/discovery/backup_files_only.txt" target="_blank">Backup Files</a></li>
        <li><a href="https://raw.githubusercontent.com/xajkep/wordlists/master/discovery/directory_only_one.small.txt" target="_blank">Directories</a></li>
    </ul>
</body>
</html>
