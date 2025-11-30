<?php

// returns an object of all inventory
function fetch_inventory()
{
    $db_file = __DIR__ . "/../var/database.json";
    $contents = file_get_contents($db_file);

    $db = json_decode($contents);
    foreach ($db as $k => $v) {
        if ($k == "inventory") {
            return $v;
        }
    }

    return null;
}

// Validate request
$ok = true; // future proof

// error
if (!$ok) {
    http_response_code(400);
    die();
}

// output json inventory
$inv = fetch_inventory();
echo json_encode($inv);

header("Content-Type: application/json; charset=UTF-8");
