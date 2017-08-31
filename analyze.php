<?php
    include_once("vendor/autoload.php");
    error_reporting(E_ERROR);
    ini_set("memory_limit", "1536M");
    ini_set("max_execution_time", "6000");    

    function makelink($text) {
        $text = preg_replace("#[[:punct:]]#", "", $text);
        $text = preg_replace("#\s#", "_", $text);
        $text = strtolower($text);
        return $text;
    }

    function numeroColonne($num) {
        $num++;
        do {
            $val = ($num % 26) ?: 26;
            $num = ($num - $val) / 26;
            $b26 = chr($val + 64).($b26 ?: '');
        } while (0 < $num);
        return $b26;
    }

    function doubleToString($number) {
        return strval($number);
    }

    function mostPresentValues($array) {

    }

    $filename = $_GET['file']; //"FICHIER-LEQUIN-xls.xlsx";

	$file_info = pathinfo($filename);
	if($file_info["extension"] == "xlsx") {
	    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
	    $spreadsheet = $reader->load($filename);
	
	    $sheet = $spreadsheet->getSheet(0);
	    $sheet_array = $sheet->toArray();
	} elseif($file_info["extension"] == "csv") {
		$fileHandle = fopen($filename, "r");
		//Loop through the CSV rows.
		$rownum=0;
		$sheet_array = [];
		while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
			foreach($row as $numcell=>$cell) {
				$sheet_array[$rownum][$numcell] = $cell;
			}
			$rownum++;
		}
	}
    //var_dump($sheet_array);
    $header = array_shift($sheet_array);
    //var_dump($header);
    //var_dump($$header[0]);
    $num_records = count($sheet_array);
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
    <h3>Rapport d'analyses de données idéesculture</h3>
    <p><i>Seule la feuille 1 est analysée, la première ligne contient les entêtes de colonne.</i></p>

    <p><?php print $num_records; ?> enregistrements, <?php print count($header); ?> colonnes :</p>
<?php
    foreach($header as $ref=>$column) {
        print "<div class=\"col-md-2\"><small>".numeroColonne($ref)."</small> <a href='#".makelink($column)."'>".$column."</a></div>";
    }
?>
        <div style="clear:both;"></div>
        <h3>Afficher</h3>

        <button type="button" class="btn btn-default" onClick="jQuery('#analysis').hide();jQuery('#datas').show();">Données</button>
        <button type="button" class="btn btn-primary" onClick="jQuery('#datas').hide();jQuery('#analysis').show();">Analyse texte</button>
        <button type="button" class="btn btn-primary" onClick="jQuery('.dataTables_wrapper').toggle();">Tableaux de valeurs</button>
    </div>
    <div id="datas" style="display:none;">
        <hr/>
        <div class="container-fluid">
        <table class="table table-hover table-bordered font-reduced">
            <tr><td></td>
                <?php

                    foreach($header as $ref=>$column) {
                        print "<th>".numeroColonne($ref)."</th>";
                    }
                ?>
            </tr>
            <tr><th>1</th>
                <?php
                    foreach($header as $ref=>$column) {
                        print "<td>".$column."</td>";
                    }
                ?>
            </tr>
            <?php
                foreach($sheet_array as $numrow=>$row) {
                    print "<tr><th>".($numrow+2)."</th>";
                    foreach($row as $cell) {
                        print "<td>".$cell."</td>";
                    }
                    print "</tr>";
                }
            ?>
        </table>
        </div>
    </div>
    <div id="analysis">
        <form>
            <div class="form-group">
    <?php

    foreach($header as $ref=>$column) {
        print "<div id=\"analysis-".makelink($column)."\">";
        $$column = array_column($sheet_array, $ref);
        print "<hr/>";

        print "<div class=\"container\">\n";
        print "<h3 id='".makelink($column)."' style='width: 100%;'><small>".numeroColonne($ref)."</small> ".$column."<span class='pull-right'><a href='#body'>↑</a></span></h3>\n";

        $values = array_count_values($$column);

        // Retraitement des données en chaîne si données de type double
        if(count($values) == 0) {
            $$column = array_map("doubleToString", $$column);
            $values = array_count_values($$column);
        }
        if(count($values) == $num_records) {
            print "<p>Une valeur par ligne, probablement numérotation automatique ou numéro d'inventaire.</p>\n";
            print "<p>10 premières valeurs : ";
            $column_values = $$column;
            for($i=0;$i<10;$i++) {
                print $column_values[$i].($i==9 ? "" : ", ");
            }
            print "</p>";
            print "<textarea  class=\"form-control\" name='".makelink($column)."' placeholder='Commentaire'></textarea>\n";
        } else {
            switch(count($values)) {
                case 1 :
                    print "<p>Une seule valeur : ".$$column[0]."</p>";
                    print "<textarea  class=\"form-control\" name='".makelink($column)."' placeholder='Commentaire'></textarea>\n";
                    break;
                case 0 :
                    print "<p><i>VIDE ou type différent de ceux reconnus pour array_count_values (string ou int), veuillez vérifier les données.</i></p>";

                    $column_values = $$column;
                    print "<p> Premières valeurs : ";
                    for($i=0;$i<10;$i++) {
                        print $column_values[$i]." (type : ".gettype($column_values[$i]).")".($i==9 ? "" : ", ");
                    }
                    print "</p>";
                    print "<textarea  class=\"form-control\" name='".makelink($column)."' placeholder='Commentaire'></textarea>\n";
                    break;
                default :
                    // More than 1 value, display the value table
                    print "<p>".count($values)." valeurs différentes "
                        ."<button class='btn btn-sm btn-default' onclick='toggleSpaceDisplay();'>Afficher les espaces</button>"
                        //."<button class='btn btn-sm btn-default' onclick='toggleDisplayValues(\"table-".makelink($column)."\");'>Afficher les valeurs</button>"
                        ."</p>\n";
                    $values_for_computing = array_values($values);
                    $mean = array_sum($values_for_computing)/count($values_for_computing);

                    $lower_limit = count($values)*5/100;
                    $valeurs_sur_representees = array_filter(
                        $values,
                        function ($value) use ($lower_limit, $mean) {
                            return ($value >= $lower_limit && $value >= $mean);
                        }
                    );
                    sort($valeurs_sur_representees);
                    $valeurs_sur_representees = array_reverse($valeurs_sur_representees);
                    $tables_valeurs = array_flip($values);
                    switch(count($valeurs_sur_representees)) {

                        case 0 :
                            print "<p>Aucune valeur sur-représentée.</p>";
                            break;
                        case 1 :
                            print "<p><b>Une seule valeur sur-représentée</b> : ".$tables_valeurs[reset($valeurs_sur_representees)]." (".reset($valeurs_sur_representees).")</p>";
                            break;
                        default :
                            print "<p><b>".count($valeurs_sur_representees)." valeurs sur-représentées</b> (plus de 5% des valeurs, plus de la moitié de la fréquence moyenne, soit ".round($mean,2).") : ";
                            //print implode(", ", $valeurs_sur_representees);
                            foreach($valeurs_sur_representees as $valeur_sur_representee) {
                                print $tables_valeurs[$valeur_sur_representee]." <small>(".$valeur_sur_representee.")</small> ";
                            }
                            print "</p>\n";
                            break;
                    }
                    print "<textarea style=\"margin-bottom:14px;\" class=\"form-control\" name='".makelink($column)."' placeholder='Commentaire'></textarea>\n";
                    arsort($values);
                    print "<table id=\"table-".makelink($column)."\" class=\"table datatable table-bordered values\">";
                    print "<thead><tr><th>Valeur</th><th>Nombre</th></tr></thead>";
                    foreach($values as $value=>$count) {
                        print "<tr><td>".str_replace(" ","<span class='space'>·</span>",$value)."</td><td>$count</td></tr>\n";
                    }
                    print "</table>";
                    break;
            }
        }

        print "</div></div>\n";

    }
    //var_dump(array_count_values($$column0));
    ?>
            </div>
        </form>
    </div>

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