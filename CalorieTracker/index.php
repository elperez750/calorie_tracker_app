<?php

$action = filter_input(INPUT_POST, 'action');
if($action == NULL){
    $action = filter_input(INPUT_GET, 'action');
    if($action == NULL){
        $action = 'home';
    }
}

if($action == 'home'){
    $calorie_goal = 0;
    if($calorie_goal == 0){
        $button_text = "Create Calorie Goal";
    }else{
        $button_text = "Edit Calorie Goal";
    }
    include('./home.php');
    
}

else if($action == 'edit_calorie_goal'){
    include('./view/calorie_goal_editor.php');
}

else if($action == 'update_calorie_goal'){
    $calorie_goal = filter_input(INPUT_POST, 'calorie_goal', FILTER_VALIDATE_INT);
    $button_text = "Edit Calorie Goal";
    include('./home.php');
}

else{
    $calorie_goal = 0;
    if($calorie_goal == 0){
        $button_text = "Create Calorie Goal";
    }else{
        $button_text = "Edit Calorie Goal";
    }
    include('./home.php');
}
