<?php
    include_once("vendor/autoload.php");
    error_reporting(E_ERROR);

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
    var_dump($resultat);

    /*$filename = $_GET['file']; //"FICHIER-LEQUIN-xls.xlsx";

    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($filename);

    $sheet = $spreadsheet->getSheet(0);
    $sheet_array = $sheet->toArray();
    //var_dump($sheet_array);
    $header = array_shift($sheet_array);
    //var_dump($header);
    //var_dump($$header[0]);
    $num_records = count($sheet_array);*/
?>
<html>
<head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css">
    <style>
        .font-reduced {
            font-size:0.8em;
        }
        .space {
            color:transparent;
        }
    </style>
    <title>IdéesCulture - DataAnalyzer</title>
</head>
<body id="body">
    <div class="container">
    <h1><img src="ideesculture.png" style="height: 48px;"/> <small>Analyse de données</small> <?php print basename($filename); ?></h1>
    <a  href='index.php' class="btn btn-primary">Retour à la page d'accueil</a>
    <h3>Rapport d'analyses des champs des sources de données</h3>


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script><link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css">
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
    <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
    <script src="//cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.3.1/js/buttons.print.min.js"></script>


    <script>
    var displayValues = 0;
    var toggleSpaceDisplay = function() {
        console.log("toggleSpaceDisplay");
        console.log($('.space').first().css("color"));
        if($('.space').first().css("color") == "rgb(128, 128, 128)") {
            $('.space').css("color", "white");
        } else {
            $('.space').css("color", "gray");
        }
    }
    var toggleDisplayValues = function(ref) {
        console.log(ref);
        //jQuery('#'+ref).toggle();
    }

    $(document).ready(function() {
        $('.datatable').DataTable( {
            dom: 'Bfrtip',
            buttons: [
                'csv', 'excel', 'pdf', 'print'
            ],
            "order": [[ 1, 'desc' ]]
        });
    });
</script>
</body>
</html>