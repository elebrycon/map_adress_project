<?php

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/UserRepository.php';

//Tells the browser that this response contains JSON.
header('Content-Type: application/json; charset=utf-8');

//Accept only POST submissions 405 means this HTTP method is not allowed.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);

    //Convert the PHP array to JSON text and send it to the browser.
    echo json_encode(['message' => 'Please submit the form.']);
    exit;
}

//Match form names to readable labels and the database columns.
$fields = [
    'first_name' => ['label' => 'First Name', 'max' => 100],
    'last_name' => ['label' => 'Last Name', 'max' => 100],
    'street_number' => ['label' => 'Street / Number', 'max' => 255],
    'city' => ['label' => 'City', 'max' => 100],
    'country' => ['label' => 'Country', 'max' => 100]
];

$data = [];
$errors = [];

//Check every expected field on the server.
foreach ($fields as $name => $rules) {
    $value = $_POST[$name] ?? '';

    if (!is_string($value)) {
        $errors[$name] = 'Please enter ' . $rules['label'];
        continue;
    }

    $value = trim($value);

    if ($value === '') {
        $errors[$name] = 'Please enter ' . $rules['label'];
        continue;
    }

    //Count Unicode code points: u enables UTF-8 and s lets the dot include newlines.
    //This avoids counting each byte of an international letter as a separate character.
    $length = preg_match_all('/./us', $value);

    if ($length === false) {
        $errors[$name] = 'Please enter valid text';
        continue;
    }

    //Reject text longer than what the database allows.
    if ($length > $rules['max']) {
        $errors[$name] = $rules['max'] . ' characters maximum';
        continue;
    }

    //This field passed all checks.
    $data[$name] = $value;
}

//If any field failed, return 422 (invalid submitted data).
if ($errors !== []) {
    http_response_code(422);

    echo json_encode(['errors' => $errors]);
    exit;
}

try {
    //Give the repository the PDO connection, then insert one user.
    $repository = new UserRepository(Database::getConnection());
    $repository->create($data);

    //Saved => true lets JavaScript reset the form and refresh the table.
    http_response_code(201);
    echo json_encode(['saved' => true]);
} catch (PDOException $exception) {
    //Keep technical details in the server log.
    error_log($exception->getMessage());

    //Return a general server-error message.
    http_response_code(500);
    echo json_encode([
        'message' => 'Unable to save the user. Please try again later.'
    ]);
}
