<?php
// ---------------------------------------------------------------
//  Simple Backend for School Project
//  Features: Users, Inventory, Promo Codes, Checkout
// ---------------------------------------------------------------

header("Content-Type: application/json");

// ---------------------------------------------------------------
//  Load + Save helpers
// ---------------------------------------------------------------

const DB_PATH = __DIR__ . "/database.json";

function load_db()
{
    return json_decode(file_get_contents(DB_PATH), true);
}

function save_db($data)
{
    file_put_contents(DB_PATH, json_encode($data, JSON_PRETTY_PRINT));
}

// ---------------------------------------------------------------
//  Helper: return JSON + exit
// ---------------------------------------------------------------

function respond($data)
{
    echo json_encode($data);
    exit();
}

// ---------------------------------------------------------------
//  Routing
// ---------------------------------------------------------------

$action = $_GET["action"] ?? null;
$db = load_db();

switch ($action) {
    // -----------------------------------------------------------
    // USERS
    // -----------------------------------------------------------
    case "register":
        $email = $_POST["email"] ?? "";
        $password = $_POST["password"] ?? "";

        foreach ($db["users"] as $u) {
            if ($u["email"] === $email) {
                respond(["success" => false, "msg" => "Email taken"]);
            }
        }

        $id = count($db["users"]) + 1;

        $db["users"][] = [
            "id" => $id,
            "email" => $email,
            "password" => password_hash($password, PASSWORD_DEFAULT),
        ];

        save_db($db);
        respond(["success" => true, "user_id" => $id]);

    case "login":
        $email = $_POST["email"] ?? "";
        $password = $_POST["password"] ?? "";

        foreach ($db["users"] as $u) {
            if (
                $u["email"] === $email &&
                password_verify($password, $u["password"])
            ) {
                respond(["success" => true, "user" => $u]);
            }
        }
        respond(["success" => false, "msg" => "Invalid credentials"]);

    // -----------------------------------------------------------
    // INVENTORY
    // -----------------------------------------------------------
    case "inventory":
        respond($db["inventory"]);

    case "add_item":
        $name = $_POST["name"] ?? "";
        $price = floatval($_POST["price"] ?? 0);
        $stock = intval($_POST["stock"] ?? 0);

        $id = count($db["inventory"]) + 1;

        $db["inventory"][] = [
            "id" => $id,
            "name" => $name,
            "price" => $price,
            "stock" => $stock,
        ];

        save_db($db);
        respond(["success" => true, "item_id" => $id]);

    // -----------------------------------------------------------
    // PROMO CODES
    // -----------------------------------------------------------
    case "validate_promo":
        $code = strtoupper($_POST["code"] ?? "");

        foreach ($db["promos"] as $p) {
            if ($p["code"] === $code) {
                respond(["valid" => true, "discount" => $p["discount"]]);
            }
        }

        respond(["valid" => false]);

    // -----------------------------------------------------------
    // CHECKOUT
    // -----------------------------------------------------------
    case "checkout":
        $user_id = intval($_POST["user_id"] ?? 0);

        if ($user_id === 0) {
            respond(["success" => false, "msg" => "Invalid user"]);
        }

        $cart = json_decode($_POST["cart"] ?? "[]", true);
        $promo_code = strtoupper($_POST["promo"] ?? "");
        $fake_card = $_POST["card"] ?? "";

        if (strlen($fake_card) < 8) {
            respond([
                "success" => false,
                "msg" => "Fake payment validation failed",
            ]);
        }

        // Compute subtotal
        $subtotal = 0;

        foreach ($cart as $item) {
            foreach ($db["inventory"] as &$inv) {
                if ($inv["id"] == $item["id"]) {
                    if ($inv["stock"] < $item["qty"]) {
                        respond([
                            "success" => false,
                            "msg" => "Insufficient stock",
                        ]);
                    }
                    $subtotal += $inv["price"] * $item["qty"];
                    $inv["stock"] -= $item["qty"]; // subtract stock
                }
            }
        }

        // Apply promo if valid
        $discount = 0;

        foreach ($db["promos"] as $p) {
            if ($p["code"] === $promo_code) {
                $discount = $subtotal * $p["discount"];
            }
        }

        $total = $subtotal - $discount;

        // Save order
        $order_id = count($db["orders"]) + 1;

        $db["orders"][] = [
            "id" => $order_id,
            "user_id" => $user_id,
            "items" => $cart,
            "subtotal" => $subtotal,
            "discount" => $discount,
            "total" => $total,
            "timestamp" => time(),
        ];

        save_db($db);

        respond([
            "success" => true,
            "order_id" => $order_id,
            "total_paid" => $total,
        ]);

    default:
        respond(["error" => "Invalid action"]);
}
