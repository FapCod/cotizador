<?php
require_once 'app/config.php';

try{
    if(!isset($_POST['action']) && !isset($_GET['action'])){
        throw new Exception('El acceso no esta autorizado');
    }
    $action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];
    $action = str_replace('-','_',$action);
    $function = sprintf('hook_%s',$action);
    if(!function_exists($function)){
        throw new Exception('El acceso no esta autorizado');
    }
    $function();
}catch(Exception $e){
    json_output(json_build(403,null, $e->getMessage()));
}