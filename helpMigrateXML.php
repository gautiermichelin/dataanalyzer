<?php
    if($argc == 1) {
        die("Syntaxe : php helpMigrateXML.php fichier.xml\n");
    }

    $iterator = new RecursiveIteratorIterator(new SimpleXMLIterator(file_get_contents($argv[1])));

    $current_node="";
    $counts = [];

    foreach ($iterator as $field => $value) {
        $paths = array();
        foreach (range(0, $iterator->getDepth()) as $depth) {
            $paths[] = $iterator->getSubIterator($depth)->key();
        }
        if(!is_array($value)) {
            if($value != "") {
                foreach ($paths as $key => $path) {
                    //echo "$path>";
                    $current_node .= "/$path";
                }

                //echo "$value\n";
                //$current_node = substr($current_node, 0, -1);
                if(!isset($counts[$current_node])) {
                    $counts[$current_node] = 1;
                } else {
                    $counts[$current_node] ++;
                }
                $current_node = "";
            }
        }
    }
    ksort($counts);
    foreach($counts as $path=>$number) {
        print "'SKIP','".$path."',".$number."\n";
    }