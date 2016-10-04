<?php
require_once "vendor/autoload.php";

// načtení konfiguračního souboru
$dataDir    = getenv("KBC_DATADIR").DIRECTORY_SEPARATOR;
$configFile = $dataDir."config.json";
$config     = json_decode(file_get_contents($configFile), true);
//$konstanta = $config["parameters"]["konstanta"];

// vytvoření výstupních souborů
$out_fields = new \Keboola\Csv\CsvFile
    ($dataDir."out".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."out_fields.csv");
$out_fieldValues = new \Keboola\Csv\CsvFile
    ($dataDir."out".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."out_fieldValues.csv");

// zápis hlaviček do výstupních souborů
$out_fields      -> writeRow(["idfield", "title", "idinstance"]);
$out_fieldValues -> writeRow(["idfieldvalue", "idrecord", "idfield", "value"]);

// načtení vstupních souborů
$in_records = new Keboola\Csv\CsvFile($dataDir."in".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."in_records.csv");

// zápis záznamů do výstupních souborů
$idInstance = 1;
$startId = 13;          // ID sloupce v tabulce 'records', kde začínají hodnoty formulářových polí (číslováno od 0)
$idfieldvalue = 1;      // inkrementální index
foreach ($in_records as $rowNum => $row) {
    if ($rowNum == 0) {        
        $colsNum = count($row);     // počet sloupců tabulky 'records'
        for ($i = $startId; $i < $colsNum; $i++) {
            $out_fields -> writeRow([
                $i - $startId + 1,      // idfield = 1,2,3,...
                $row[$i],     // názvy formulářových polí
                $idInstance
            ]);
        }
    } else {
        for ($i = $startId; $i < $colsNum; $i++) {
            if (strlen($row[$i]) > 0) {                         // formulářové pole má vyplněnou hodnotu
                $out_fieldValues -> writeRow([
                    $idfieldvalue,                              // uměle vytvořený inkrementální index (1,2,3,...)
                    $row[0],                                    // idrecord
                    $i - $startId + 1,                          // idfield = 1,2,3,...
                    $row[$i]                                    // value (hodnota vyplněná ve formulářovém poli)
                ]);
                $idfieldvalue++;
            }
        }
    }
}