<?php

function calSensitivity($arrayOfty)
{
    static $_senseMap = array(1=>'LOW',10=>'MODERATE',100=>'HIGH');
    $_senseRevMap = array_flip($_senseMap);
    $value = 0;
    foreach( $arrayOfty as $a ){
        if( in_array($a, $_senseMap) ){
            $value += $_senseRevMap[$a];
        }else{
            throw new fisma_Exception('Wrong sensitivity calculation:'.
                        var_export($arrayOfty,true));
        }
    }
    if( intval($value/100)>0 ) {
        return 'HIGH';
    }else if ( intval($value/10)>0 ){
        return 'MODERATE';
    }else{
        return 'LOW';
    }
}
/*
** Generate an array of HTML colors to populate table cell backgrounds.
** Array is meant to be passed to the templates driving the generation
** of RAF impact/risk tables.
** Array consists of N-1 default background color values and one
** highlight value at index M.
**
** Input:
**  num_cells - number of elements in return array
**  highlight_index - zero-based index of cell to receive highlight_color
**  default_color - the color of ordinary cells
**  highlight_color - the color of the highlighted cell
*/

function cell_background_colors ($num_cells, $highlight_index,
$default_color='FFFFFF', $highlight_color='CCCCCC') 
{

// Initialize array with regular cell color values
$colors = array_fill(0, $num_cells, $default_color);

// Set highlighted cell with highlight color value
$colors[$highlight_index] = $highlight_color;

return $colors;
}


function calcImpact($sense, $criti)
{
$_senseMap = array('LOW'=>1,'MODERATE'=>10,'HIGH'=>100);
$ret = min($_senseMap[$sense],$_senseMap[$criti]);
$_revMap = array_flip($_senseMap);
return $_revMap[$ret];
}

function calcThreat($threat, $countmeasure)
{
$_senseMap = array('LOW'=>1,'MODERATE'=>10,'HIGH'=>100);
$ret = $_senseMap[$threat]-$_senseMap[$countmeasure];
if( $ret <= 0 ) {
    $ret =1;
}else if( $ret > 90 ) {
    $ret =100;
}else {
    $ret =10;
}
$_revMap = array_flip($_senseMap);
return $_revMap[$ret];
}

