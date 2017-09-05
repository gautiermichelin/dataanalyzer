#!/usr/local/bin/php
<?php
    /**
     *
     * dataAnalyzer
     *
     * 2017, idéesculture.
     *
     * This file : created on 05/09/2017 (16:37) by Gautier MICHELIN, gm@ideesculture.com
     * Contributions by : (add your name here, separated with commas)
     */

    function stripInvalidXml($value) {
        $ret = "";
        if (empty($value))
        {
            return $ret;
        }

        $length = strlen($value);
        for ($i=0; $i < $length; $i++)
        {
            $current = ord($value{$i});
            if (($current == 0x9) ||
                ($current == 0xA) ||
                ($current == 0xD) ||
                (($current >= 0x20) && ($current <= 0xD7FF)) ||
                (($current >= 0xE000) && ($current <= 0xFFFD)) ||
                (($current >= 0x10000) && ($current <= 0x10FFFF)))
            {
                $ret .= chr($current);
            }
            else
            {
                $ret .= " ";
            }
        }
        return $ret;
    }

    $content = file_get_contents($argv[1]);
    $content = stripInvalidXml($content);
    //$content = "<test(aaa)>ici () </test>";


    // Retrait des parenthèses dans les tags XML
    $pattern_start = '/<([^<>]*)';
    $pattern_end = '([^<>]*)>/i';
    $replacement = '<$1$2>';
    $content = preg_replace($pattern_start."'".$pattern_end, $replacement, $content)."\n";
    $content = preg_replace($pattern_start."°".$pattern_end, $replacement, $content)."\n";
    $content = preg_replace($pattern_start."\(".$pattern_end, $replacement, $content)."\n";
    $content = preg_replace($pattern_start."\(".$pattern_end, $replacement, $content)."\n";
    $content = preg_replace($pattern_start."\(".$pattern_end, $replacement, $content)."\n";
    $content = preg_replace($pattern_start."\)".$pattern_end, $replacement, $content)."\n";
    $content = preg_replace($pattern_start."\)".$pattern_end, $replacement, $content)."\n";
    $content = preg_replace($pattern_start."\)".$pattern_end, $replacement, $content)."\n";
    file_put_contents($argv[2], $content);

