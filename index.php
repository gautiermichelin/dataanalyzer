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
        $array = [];

        // Not good
        foreach ($array_filename as $key => $file) {
            $state = '-';

            foreach ($json as $key_json => $value) {
                // If file has a state
                if($key_json == $file) {
                    $state = $value['state'];
                } 
            }

            $json_data = array("name"=>$file, "state"=>$state);
            $line      = json_encode($json_data);    
            array_push($array, $line); 
        }


        return $array;
    }

    function displayFile($file, $state) {

        $path_parts = pathinfo($file);
        if($path_parts['extension'] == 'xlsx') {
            $filename = basename($file);
            $uniqueId = md5($file);
            ?>
            <li class="list-group-item">
            <form>
                <?php if ($state =="A migrer" || $state == "En attente") : ?>
                <a href="analyze.php?file=<?php print $file; ?>"><?php print $filename; ?> </a><span class="label label-info">Analyse disponible</span>
                <?php else : ?>
                    <?php print $filename; ?>
                <?php endif; ?>
                <select id="<?php print $uniqueId; ?>" class="selectpicker show-menu-arrow pull-right state" data-style="btn-primary" data-width="150px" data-file="<?php print $file; ?>">
                    <option>-</option>
                    <option data-icon="glyphicon glyphicon-ok-circle">A migrer</option>
                    <option data-icon="glyphicon-warning-sign">En attente</option>
                    <option data-icon="glyphicon-ban-circle">Ne pas migrer</option>
                </select>
            </form>
            </li>
            <script>
                $(document).ready(function() {
                    $("#<?php print $uniqueId; ?>").selectpicker({
                        style: 'btn-primary',
                        size: 4
                    });
                    $("#<?php print $uniqueId; ?>").selectpicker("val","<?php print $state; ?>");
                    $("#<?php print $uniqueId; ?>").parent().parent().parent().addClass("<?php print str_replace(" ", "_", strtolower($state)); ?>");
                });
            </script>
            <?php
        }
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>f

    <style>
        .font-reduced {
            font-size:0.8em;
        }
        .space {
            color:transparent;
        }
        button span.filter-option.pull-left {
            color:white;
        }
        li.a_migrer {
            background-color:RGBA(22, 145, 105, 0.10);
        }
        li.en_attente {
            background-color:RGBA(231, 122, 58, 0.20);
        }
        li.ne_pas_migrer {
            background-color:RGBA(0, 0, 0, 0.10);
            color:RGBA(0, 0, 0, 0.40);
        }
    </style>
    <title>IdéesCulture - DataAnalyzer</title>
</head>
<body>

    <div class="container">
        <h1><img src="ideesculture.png" style="height: 48px;"/> <small>Analyse de données</small></h1>
        <h3><button class="btn btn-default" onclick="location.reload();"><i class="glyphicon glyphicon-refresh"></i></button> Ensemble des fichiers disponibles</h3>
        <ul class="list-unstyled">
            <?php
                //
                $previous = '.';

                $index=0;
                foreach ($states as $key => $fileDescriptor) {
                    $fileDescriptor = json_decode($fileDescriptor);
                    console.log($fileDescriptor);
                    $file  = $fileDescriptor->name;
                    $state = $fileDescriptor->state;
                    $fileDir  = dirname($file);

                    // Check if in the same directory
                    if($previous == $fileDir) {
                        $filename = basename($file);
                        displayFile($file, $state);

                    } else {
                        echo '</ul>';
                        echo '<ul class="list-unstyled list-group">';
                        echo '<h3><small>'.$fileDir.'</small></h3>';
                        displayFile($file, $state);?>
                        <?php
                        $previous = $fileDir;
                        $index++;
                    }

                }

            ?>
        </ul>
    </div>

    <script>
        $('select').on('change', function() {
            var file = $(this).attr('data-file');
            console.log(file);
            $.post( "save.php", { id: file, state: this.value});
            console.log(this.id);
            $('#'+this.id).parent().parent().parent()
                .removeClass('a_migrer')
                .removeClass('en_attente')
                .removeClass('ne_pas_migrer')
                .addClass(this.value.replace(/\s/g, '_').toLowerCase());
        });
    </script>
</body>
</html>


