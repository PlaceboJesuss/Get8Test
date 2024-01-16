<?php

use Helpers\Log;
use Helpers\SpreadsheetHelper;

header('Content-Type: application/json; charset=utf-8');
include "vendor/autoload.php";
include "config.php";

try {
    Log::setPath("logs/file.log");
    Log::debug("Получили данные:", $_POST);

    $spreadsheet = new SpreadsheetHelper();
    $spreadsheet->openSpreadsheet(SPREADSHEET_ID, SPREADSHEET_LIST);
    $datetime = date("Y-m-d H:i:s");
    $errors = [];

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "email";
        Log::debug("Невалидные данные поля email");
    }
    if (!filter_var($_POST["phone"], FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/7[0-9]{10}/"]])) {
        $errors[] = "phone";
        Log::debug("Невалидные данные поля phone");
    }
    if (!filter_var($_POST["name"], FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/^[а-яёА-ЯЁ]+$/u"]])) {
        $errors[] = "name";
        Log::debug("Невалидные данные поля name");
    }

    if (count($errors) > 0) {
        echo json_encode(["result" => "error", "error_fields" => $errors, "message" => "Проверьте корректность выделенных полей"]);
        die;
    }
    Log::debug("Данные валидны");
    $spreadsheet->addRow($_POST["name"], $_POST["email"], $_POST["phone"], $datetime);

    echo json_encode(["result" => "success", "message" => "Запись успешно добавлена"]);
} catch (Throwable $e) {
    Log::error("Получили ошибку: " . $e->getMessage() . " " . $e->getTraceAsString());
    echo json_encode(["result" => "error", "message" => "Ошибка сервера, обратитесь к системному администратору"]);
}
