<?php
$a = [
    "sn",
    "status" => function($model){

    }
];

foreach ($a as $key => $item) {
    echo $key . $item;
}