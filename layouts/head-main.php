<?php
require_once ("lang.php");

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    require_once dirname(__DIR__, 1) . '/php/db_connect.php';
    // Language
    $language = $_SESSION['language'];

    // Load message
    $db->set_charset("utf8mb4");
    $message_resource = $db->query("SELECT * FROM message_resource");
    $languageArray = Array();

    while($row=mysqli_fetch_assoc($message_resource)){
        $languageArray[$row['message_key_code']] = array("en"=>$row['en'],"zh"=>$row['zh'],"my"=>$row['my'],"ne"=>$row['ne']);
    }

    $_SESSION['languageArray'] = $languageArray;

    // Get Company Detail
    $stmt = $db->prepare("SELECT * from Company WHERE id = 1");
    $stmt->execute();
    $result = $stmt->get_result();

    $package = 'Standard';
    
    if(($row = $result->fetch_assoc()) !== null){
        $package = $row['package'] ?? 'Standard';
    }

    $_SESSION['package'] = $package;
}

$isScssconverted = false;

require_once ("scssphp/scss.inc.php");

use ScssPhp\ScssPhp\Compiler;

if($isScssconverted){

    global $compiler;
    $compiler = new Compiler();

    $compine_css = "assets/css/app.min.css";

    $source_scss = "assets/scss/config/default/app.scss";

    $scssContents = file_get_contents($source_scss);

    $import_path = "assets/scss/config/default";
    $compiler->addImportPath($import_path);
    $target_css = $compine_css;

    $css = $compiler->compileString($scssContents);

    if (!empty($css) && is_string($css)) {
        file_put_contents($target_css, $css);
    }
}
?>
<!DOCTYPE html>
<html lang="<?=$language?>" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">