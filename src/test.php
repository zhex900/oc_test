<?php

function printN($n){
    for($i=1; $i<=$n; $i++){
        $multiples = false;
        if( $i % 3 == 0 ){
            $multiples = true;
            echo 'Apple';
        }
        if( $i % 7 == 0){
            $multiples = true;
            echo 'Orange';
        }
        if(!$multiples){
            echo $i;
        }

        if(defined('STDIN') ) {
            echo PHP_EOL;
        }
        else {
            echo "<br>";
        }


    }
}

printN(200);