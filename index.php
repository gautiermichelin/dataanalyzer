<?php
    include_once("vendor/autoload.php");
    error_reporting(E_ERROR);

    $base_directory = 'donnees';
    $files = traverse_hierarchy($base_directory);

    function traverse_hierarchy($path)
    {
        $return_array = array();
        $dir = opendir($path);
        while(($file = readdir($dir)) !== false)
        {
            if($file[0] == '.') continue;
            $fullpath = $path . '/' . $file;
            if(is_dir($fullpath))
                $return_array = array_merge($return_array, traverse_hierarchy($fullpath));
            else 
                $return_array[] = $fullpath;
        }
        return $return_array;
    }

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
<body>
        

    <div class="container">
        <h1>Ensemble des fichiers disponibles</h1>
        <ul class="list-unstyled">
            <?php
                //
                $previous = '.';

                foreach ($files as $key => $file) {

                    $fileDir = dirname($file);
                    $filename = basename($file);

                    // Check if in the same file
                    if($previous == $fileDir) {
                        echo '<li>';
                        echo '<a href="analyze.php?file='.$file.'">'.$filename.'</a>';
                        echo '</li>';
                    } else  if ($previous != $fileDir){
                        // End the previous list
                        echo '</ul>';
                        // Create a new one
                        echo '<ul>';
                        echo '<h1><small>'.$fileDir.'</small></h1>';
                        echo '<li>';
                        echo '<a href="analyze.php?file='.$file.'">'.$filename.'</a>';
                        echo '</li>';
                        $previous = $fileDir;
                    }

                }
            ?>
        </ul>
    </div>
</body>
</html>