<?php

function dateFormat_1($date){
    if($date == null || $date == ""){
        return null;
    }else{
        $date = Date('Y-Md', strtotime($date));
        return $date;
    }
}

function dateFormat_2($date){
    if($date == null || $date == ""){
        return null;
    }else{
        $date = Date('Y-Md h:iA', strtotime($date));
        return $date;
    }
}

function dateFormat_3($date){
    if($date == null || $date == ""){
        return null;
    }else{
        $date = Date('h:iA', strtotime($date));
        return $date;
    }
}

function dateFormat_4($date){
    if($date == null || $date == ""){
        return null;
    }else{
        $date = Date('dM-Y', strtotime($date));
        return $date;
    }
}
