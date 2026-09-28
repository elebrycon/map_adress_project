<?php

// Load the connection and repository classes once, relative to this file's folder.
require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/UserRepository.php';

// Send JSON for JavaScript to read, rather than a complete HTML page.
header('Content-Type: application/json; charset=utf-8');
// Ask browsers and other caches not to store this response, so the list stays fresh.
header('Cache-Control: no-store');

// This read-only endpoint accepts GET; other methods receive 405 (method not allowed).
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    http_response_code(405);
    echo json_encode(['message' => 'Use GET to load users.']);
    // Stop here instead of querying the database for an unsupported request.
    exit;
}

try {
    // Pass the database connection into the class that reads user records.
    $repository = new UserRepository(Database::getConnection());
    // Put the rows under users, then encode them as JSON; encoding failures throw an exception.
    echo json_encode(['users' => $repository->findAll()], JSON_THROW_ON_ERROR);
} catch (PDOException | JsonException $exception) {
    // Handle either a database failure or a JSON encoding failure, logging details privately.
    error_log($exception->getMessage());
    // Send a generic failure response that users.js can handle without database details.
    http_response_code(500);
    echo json_encode(['message' => 'Unable to load saved users. Please refresh to try again.']);
}
