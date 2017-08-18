<?php
    include_once("vendor/autoload.php");
    error_reporting(E_ERROR);
    ini_set(memory_limit, "4096M");
    ini_set(max_execution_time, "6000");

    function makelink($text) {
        $text = preg_replace("#[[:punct:]]#", "", $text);
        $text = preg_replace("#\s#", "_", $text);
        $text = strtolower($text);
        return $text;
    }

    function traverse_hierarchy($path, $extensions) {
        $return_array = array();
        $dir = opendir($path);
        while(($file = readdir($dir)) !== false) {
            if ($file[0] == '.') continue;
            $fullpath = $path . '/' . $file;
            if (is_dir($fullpath)) {
                $return_array = array_merge($return_array, traverse_hierarchy($fullpath, $extensions));
            } else {
                $path_parts = pathinfo($file);
                if(in_array($path_parts["extension"], $extensions)) {
                    $return_array[] = $fullpath;
                }
            }
        }
        return $return_array;
    }

    $resultat = traverse_hierarchy("donnees", array("xlsx"));
    $headers = [];
    foreach($resultat as $filename) {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($filename);

        $sheet = $spreadsheet->getSheet(0);
        $sheet_array = $sheet->toArray();
        //var_dump($sheet_array);
        $header = array_filter($sheet_array[0], function($value) {
            return ($value !== null && $value !== "");
        });

        // No headers detected on first line, skipping...
        if(!sizeof($header)) {
            continue;
        }
        $headers[$filename] = $header;
        $path_parts = pathinfo($filename);
        file_put_contents($path_parts["dirname"]."/".$path_parts["filename"]."_headers.json", json_encode($header));
    }
