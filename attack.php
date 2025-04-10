<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target = $_POST["target"];
    $requests = $_POST["requests"];
    
    for ($i = 0; $i < $requests; $i++) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        echo $output;
    }
}
?>
