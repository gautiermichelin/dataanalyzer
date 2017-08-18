<?php
    include_once("vendor/autoload.php");
    error_reporting(E_ERROR);

    $base_directory = 'donnees';
    $data_file = 'data.json';

    $files          = traverse_hierarchy($base_directory);
    $states         = compare_to_json($data_file, $files);

    /***
        Get all the files in the hierarchy
    **/
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

    /**
    *   Get all the sates of the files
    **/
    function compare_to_json($json_file, $array_filename) {
        $json = json_decode(file_get_contents($json_file),  TRUE);
        $json_filenames = [];
        $array = [];

        foreach ($json as $key => $value) {
            array_push($json_filenames, $key);
        }

        // Not good
        foreach ($array_filename as $key => $file) {

            if(in_array($key, $json_filenames)) {
                $state;
                foreach ($json as $key_json => $value) {
                    if($key_json == $key) {
                         $state = $value['state'];
                         break;
                    }
                }
                
                $json_data = array("name"=>$file, "state"=>$state);
                $line      = json_encode($json_data);
                array_push($array, $line);
            } else {
                $json_data = array("name"=>$file, "state"=>'-');
                $line      = json_encode($json_data);
                array_push($array, $line);
            }
        }
        return $array;
    }   

?>


<html>
<head>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

  <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css">

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
        <h1><img src="ideesculture.png" style="height: 48px;"/> <small>Analyse de données</small></h1>
        <h3>Ensemble des fichiers disponibles</h3>
        <ul class="list-unstyled">
            <?php
                //
                $previous = '.';

                foreach ($states as $key => $fileDescriptor) {
                    $fileDescriptor = json_decode($fileDescriptor);
                    $file  = $fileDescriptor->name;
                    $state = $fileDescriptor->state;
                    $fileDir  = dirname($file);

                    // Check if in the same file
                    if($previous == $fileDir) {
                        $filename = basename($file);
                        displayFile($file, $key, $state);

                    } else {
                        echo '</ul>';
                        echo '<ul class="list-unstyled list-group">';
                        echo '<h3><small>'.$fileDir.'</small></h3>';
                        displayFile($file, $key, $state);
                        $previous = $fileDir;
                    }
                }

                function displayFile($file, $index, $state) {
                    $filename = basename($file);

                    echo '<li class="list-group-item">';
                    echo '<form>';
                    echo $index.'-';
                    echo '<a href="analyze.php?file='.$file.'">'.$filename.'</a>';
                    echo '<input type="hidden" name="filename" value="'.$file.'">';
                    echo '<select class="selectpicker show-menu-arrow pull-right state" data-style="btn-primary" data-width="150px" id="'.$file.'" title="'.$state.'">
                            <option data-icon="glyphicon glyphicon-ok-circle">-</option>
                            <option data-icon="glyphicon glyphicon-ok-circle">Ok</option>
                            <option data-icon="glyphicon-warning-sign">Warning</option>
                            <option data-icon="glyphicon-ban-circle">Stop</option>
                        </select>';
                    echo '</form>';
                    echo '</li>';
                } 
            ?>
        </ul>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>f
    <script>
        $('.selectpicker').selectpicker({
          style: 'btn-primary',
          size: 4
        });

        $('select').on('change', function() {
            var id = $(this).attr('id');
            $.post( "save.php", { id: id, state: this.value}); 
        });
    </script>



</body>
</html>


