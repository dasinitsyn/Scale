<?php
include "Scale.php";


$test_scale=new Scale();
try{
//$test_scale->initialize("MERA","V100","TCP","192.168.1.4","1010");
$test_scale->initialize("TEST","TEST1","TCP","192.168.1.4","1010");
echo nl2br("Measured weight: ".$test_scale->getWeight().PHP_EOL);
echo "\r\n";
echo $test_scale->GetScaleDescription();
}
catch(Exception $e)
{
    echo $e->getMessage();
};




