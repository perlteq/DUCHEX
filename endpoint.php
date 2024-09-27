<?php
header('Content-Type: application/json');

if (isset($_GET['crypto']) && isset($_GET['currency'])) {
    $cryptoId = $_GET['crypto'];
    $currency = $_GET['currency'];

    // Function to get the crypto price
    function getCryptoPrice($cryptoId, $currency) {
        $url = "https://api.coingecko.com/api/v3/simple/price?ids={$cryptoId}&vs_currencies={$currency}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    // Fetch and return the price
    $cryptoData = getCryptoPrice($cryptoId, $currency);
    echo json_encode($cryptoData);
} else {
    echo json_encode(["error" => "Please provide crypto and currency parameters."]);
}
?>
