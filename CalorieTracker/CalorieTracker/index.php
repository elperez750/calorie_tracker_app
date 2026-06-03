<?php

$action = filter_input(INPUT_POST, 'action');
if($action == NULL){
    $action = filter_input(INPUT_GET, 'action');
    if($action == NULL){
        $action = 'home';
    }
}

if($action == 'home'){
    include('./home.php');
}

else{
    include('./home.php');
}
