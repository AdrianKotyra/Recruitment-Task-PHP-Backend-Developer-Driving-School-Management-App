<?php
/**
 * -------------------------------------------------------------------
 * API backend dla aplikacji PlanJazd.pl
 * -------------------------------------------------------------------
 * Obsługuje żądania AJAX (fetch) wysyłane z frontendu (index.php).
 *
 * Dostępne akcje:
 *  - GET (?action=get)        → pobiera listę wszystkich jazd
 *  - POST (?action=add)       → dodaje nową jazdę
 *  - DELETE (?action=delete)  → usuwa jazdę po ID
 *
 * Wymagania:
 *  - Połączenie z bazą MySQL przez PDO (patrz: config.php)
 *  - Walidacja danych wejściowych
 *  - Zwracanie odpowiedzi w formacie JSON
 * -------------------------------------------------------------------
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // tylko w celach testowych
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php'; // Łączy z bazą ($pdo)

/**
 * Funkcja pomocnicza – wysyła odpowiedź JSON i kończy skrypt.
 *
 * @param string $status  'success' lub 'error'
 * @param string $message Wiadomość do użytkownika
 * @param mixed  $data    (opcjonalnie) dane zwracane przez API
 */
function respond(string $status, string $message, $data = null): void {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Waliduje dane wejściowe przesłane przy dodawaniu nowej jazdy.
 *
 * @param array $data Dane przesłane z formularza (JSON)
 * @return array [status => bool, message => string]
 */
function validateDrive(array $data): array {
    if (empty($data['date']) || empty($data['time']) || empty($data['instructor']) || empty($data['student'])) {
        return ['status' => false, 'message' => 'Wszystkie pola są wymagane.'];
    }

    // Walidacja daty
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date'])) {
        return ['status' => false, 'message' => 'Niepoprawny format daty.'];
    }

    // Walidacja godziny (HH:MM)
    if (!preg_match('/^\d{2}:\d{2}$/', $data['time'])) {
        return ['status' => false, 'message' => 'Niepoprawny format godziny.'];
    }

    // Walidacja imion/nazwisk – tylko litery i spacje
    foreach (['instructor', 'student'] as $field) {
        if (!preg_match('/^[A-Za-zĄąĆćĘęŁłŃńÓóŚśŹźŻż\s\-]+$/u', $data[$field])) {
            return ['status' => false, 'message' => ucfirst($field) . ' zawiera niepoprawne znaki.'];
        }
    }

    return ['status' => true, 'message' => 'OK'];
}

$action = $_GET['action'] ?? '';

switch ($action) {

    /**
     * ----------------------------------------------------------------
     * GET – Pobierz listę wszystkich jazd
     * ----------------------------------------------------------------
     */
    case 'get':
        try {
            $stmt = $pdo->query("SELECT id, date, time, instructor, student FROM drives ORDER BY date, time");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($rows);
        } catch (PDOException $e) {
            http_response_code(500);
            respond('error', 'Błąd bazy danych: ' . $e->getMessage());
        }
        break;


    /**
     * ----------------------------------------------------------------
     * POST – Dodaj nową jazdę
     * ----------------------------------------------------------------
     */
    case 'add':
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            respond('error', 'Nieprawidłowe dane wejściowe.');
        }

        $validation = validateDrive($input);
        if (!$validation['status']) {
            http_response_code(400);
            respond('error', $validation['message']);
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO drives (date, time, instructor, student) VALUES (:date, :time, :instructor, :student)");
            $stmt->execute([
                ':date' => $input['date'],
                ':time' => $input['time'],
                ':instructor' => htmlspecialchars(trim($input['instructor'])),
                ':student' => htmlspecialchars(trim($input['student'])),
            ]);

            $newDrive = [
                'id' => $pdo->lastInsertId(),
                'date' => $input['date'],
                'time' => $input['time'],
                'instructor' => $input['instructor'],
                'student' => $input['student']
            ];

            respond('success', 'Jazda została dodana.', $newDrive);
        } catch (PDOException $e) {
            http_response_code(500);
            respond('error', 'Błąd bazy danych: ' . $e->getMessage());
        }
        break;


    /**
     * ----------------------------------------------------------------
     * DELETE – Usuń jazdę po ID
     * ----------------------------------------------------------------
     */
    case 'delete':
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            respond('error', 'Nieprawidłowe ID jazdy.');
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM drives WHERE id = :id");
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                respond('error', 'Nie znaleziono jazdy o podanym ID.');
            }

            respond('success', 'Jazda została usunięta.');
        } catch (PDOException $e) {
            http_response_code(500);
            respond('error', 'Błąd bazy danych: ' . $e->getMessage());
        }
        break;


    /**
     * ----------------------------------------------------------------
     * Inne / nieznane akcje
     * ----------------------------------------------------------------
     */
    default:
        http_response_code(400);
        respond('error', 'Nieznana akcja API.');
}
