<?php 
header("Content-Type: application/json");


$host = "localhost";
$user = "root";
$pass = "";
$db   = "larpdustry_dbms";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["ok" => false, "message" => "Invalid request."]);
    exit;
}

$data = [
    "First name"     => trim($_POST["f_name"] ?? ""),
    "Last name"      => trim($_POST["l_name"] ?? ""),
    "Email"          => trim($_POST["email"] ?? ""),
    "Contact number" => trim($_POST["contact_no"] ?? ""),
    "Subject"        => trim($_POST["subject"] ?? ""),
    "Message"        => trim($_POST["message"] ?? ""),
];


$missing = array_keys(array_filter($data, fn($v) => $v === ""));
if ($data["Email"] !== "" && !filter_var($data["Email"], FILTER_VALIDATE_EMAIL)) {
    $missing[] = "Email (invalid format)";
}
if ($missing) {
    echo json_encode(["ok" => false, "message" => "<strong>Missing or invalid:</strong> " . implode(", ", $missing) . "."]);
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");

    $stmt = $conn->prepare(
        "INSERT INTO contact_info (f_name, l_name, email, `contact_#`, subject, message)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("ssssss",
        $data["First name"], $data["Last name"], $data["Email"],
        $data["Contact number"], $data["Subject"], $data["Message"]
    );
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo json_encode(["ok" => true]);
} catch (Exception $e) {
    echo json_encode([
        "ok" => false,
        "message" => "Could not save your message. Please try again later.",
        "detail" => $e->getMessage() 
    ]);
}
?>