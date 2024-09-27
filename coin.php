<?php
// Set up headers for cross-origin and content-type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

// Function to get coin prices from CoinGecko
function getCryptoPrices($cryptoIds, $currency = 'usd') {
    // Convert array of coin IDs into a comma-separated string
    $cryptoIdsString = implode(',', $cryptoIds);

    // API URL to get prices and 24h change percentage
    $url = "https://api.coingecko.com/api/v3/simple/price?ids={$cryptoIdsString}&vs_currencies={$currency}&include_24hr_change=true";

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo json_encode(["error" => curl_error($ch)]);
        curl_close($ch);
        return null;
    }

    curl_close($ch);

    // Decode JSON response
    $data = json_decode($response, true);

    // Return the data or null if there was an error
    return $data ? $data : null;
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Check if a coin is being searched for
    if (isset($_GET['search'])) {
        $search = strtolower(trim($_GET['search'])); // Sanitize input

        // Fetch price for the searched coin
        $cryptoPrices = getCryptoPrices([$search]);

        if ($cryptoPrices && isset($cryptoPrices[$search])) {
            $price = $cryptoPrices[$search]['usd'];
            $change = $cryptoPrices[$search]['usd_24h_change'];
            $changeFormatted = number_format($change, 2);
            $changeSign = $change > 0 ? "+" : "";

            echo json_encode([
                "coin" => ucfirst($search),
                "price" => $price,
                "change" => $changeSign . $changeFormatted . "%"
            ]);
        } else {
            echo json_encode(["error" => "Coin not found"]);
        }
    } else {
        // Return a list of predefined coins if no search query is provided
        $predefinedCoins = ['bitcoin', 'ethereum', 'tether', 'binancecoin', 'usd-coin', 'dogecoin'];
        $cryptoPrices = getCryptoPrices($predefinedCoins);

        if ($cryptoPrices) {
            $result = [];
            foreach ($predefinedCoins as $coin) {
                if (isset($cryptoPrices[$coin])) {
                    $price = $cryptoPrices[$coin]['usd'];
                    $change = $cryptoPrices[$coin]['usd_24h_change'];
                    $changeFormatted = number_format($change, 2);
                    $changeSign = $change > 0 ? "+" : "";

                    $result[] = [
                        "coin" => ucfirst($coin),
                        "price" => $price,
                        "change" => $changeSign . $changeFormatted . "%"
                    ];
                }
            }

            echo json_encode($result);
        } else {
            echo json_encode(["error" => "Failed to retrieve coin prices"]);
        }
    }
} else {
    echo json_encode(["error" => "Invalid request method. Only GET is allowed."]);
}
?>
