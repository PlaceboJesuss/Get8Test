<?php

namespace Helpers;

use Exception;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class SpreadsheetHelper
{

    private Sheets $service;
    private string $spreadsheet_id;
    private string $sheet;
    private array $index_columns;
    private const FIELDS = ["name" => "Имя", "phone" => "Телефон", "email" => "Email", "time" => "Время записи"];

    public function __construct(string $credentials_path = 'credentials/credentials.json')
    {
        $client = new Client();
        $client->setApplicationName('Google Sheets API');
        $client->setScopes([Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig($credentials_path);
        $client->setLogger(Log::getLogger());
        $this->service = new Sheets($client);
    }

    public function openSpreadsheet(string $spreadsheet_id, string $sheet)
    {
        Log::debug("Открываем лист и ищем шапку");
        $head = $this->service->spreadsheets_values->get($spreadsheet_id, "$sheet!A1:Z1")->values[0];
        //Определяем индексы колонок, в которые будем записывать данные
        foreach (static::FIELDS as $key => $field) {
            if (($index = array_search($field, $head)) !== false) {
                $this->index_columns[$index] = $key;
            } else {
                throw new Exception("На листе нет необходимых столбцов");
            }
        }
        $this->spreadsheet_id = $spreadsheet_id;
        $this->sheet = $sheet;
    }

    public function addRow(string $name, string $email, string $phone, string $time)
    {
        $data = [];
        $max_index = max(array_keys($this->index_columns));
        for ($i = 0; $i <= $max_index; $i++) {
            if (isset($this->index_columns[$i])) {
                $field = $this->index_columns[$i];
                $data[$i] = $$field;
            } else {
                $data[$i] = "";
            }
        }
        Log::debug("Тело добавления строки в файл", $data);
        $options = ['valueInputOption' => 'USER_ENTERED'];
        $value_range = new ValueRange();
        $value_range->setValues([$data]);
        $this->service->spreadsheets_values->append($this->spreadsheet_id, $this->sheet, $value_range, $options);
    }
}
