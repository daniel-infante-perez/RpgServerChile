<?php

include("Accesor.php");
include("FieldCalculator.php");

$Cal = new Field_Calculate();
echo $Cal->calculate("1D6");
?>