<?php
session_start();
require_once 'db_connect.php';

if(!isset($_SESSION['id'])){
    echo '<script type="text/javascript">location.href = "../login.html";</script>'; 
    exit();
}

if (isset($_POST['id'])) {
    $weightId = $_POST['id'];
    $modified_by = $_SESSION['username'];

    if (empty($_POST["ticketDo"])) {
        $ticketDo = null;
    } else {
        $ticketDo = trim($_POST["ticketDo"]);
    }

    if (empty($_POST["reduceWeight"])) {
        $reduceWeight = null;
    } else {
        $reduceWeight = floatval(trim($_POST["reduceWeight"]))*1000;
    }

    if (empty($_POST["finalWeight"])) {
        $finalWeight = null;
    } else {
        $finalWeight = floatval(trim($_POST["finalWeight"]))*1000;
    }

    if (empty($_POST["rejectWeight"])) {
        $rejectWeight = null;
    } else {
        $rejectWeight = floatval(trim($_POST["rejectWeight"]))*1000;
    }

    if (empty($_POST["grader"])) {
        $grader = null;
    } else {
        $grader = trim($_POST["grader"]);
    }

    if (empty($_POST['mspoPerc'])){
        $mspoPerc = 0;
    } else {
        $mspoPerc = trim($_POST['mspoPerc']);
    }

    if (empty($_POST['mspoWeight'])){
        $mspoWeight = 0;
    } else {
        $mspoWeight = trim($_POST['mspoWeight']);
    }

    if (empty($_POST['nonMspoPerc'])){
        $nonMspoPerc = 0;
    } else {
        $nonMspoPerc = trim($_POST['nonMspoPerc']);
    }

    if (empty($_POST['nonMspoWeight'])){
        $nonMspoWeight = 0;
    } else {
        $nonMspoWeight = trim($_POST['nonMspoWeight']);
    }

    if (empty($_POST['bunchSize25'])){
        $bunchSize25 = 0;
    } else {
        $bunchSize25 = trim($_POST['bunchSize25']);
    }

    if (empty($_POST['bunchSize10'])){
        $bunchSize10 = 0;
    } else {
        $bunchSize10 = trim($_POST['bunchSize10']);
    }

    if (empty($_POST['bunchSize9_10'])){
        $bunchSize9_10 = 0;
    } else {
        $bunchSize9_10 = trim($_POST['bunchSize9_10']);
    }

    if (empty($_POST['bunchSize8_9'])){
        $bunchSize8_9 = 0;
    } else {
        $bunchSize8_9 = trim($_POST['bunchSize8_9']);
    }

    if (empty($_POST['bunchSize7_8'])){
        $bunchSize7_8 = 0;
    } else {
        $bunchSize7_8 = trim($_POST['bunchSize7_8']);
    }

    if (empty($_POST['bunchSize6_7'])){
        $bunchSize6_7 = 0;
    } else {
        $bunchSize6_7 = trim($_POST['bunchSize6_7']);
    }

    if (empty($_POST['bunchSize5_6'])){
        $bunchSize5_6 = 0;
    } else {
        $bunchSize5_6 = trim($_POST['bunchSize5_6']);
    }

    if (empty($_POST['bunchSize5'])){
        $bunchSize5 = 0;
    } else {
        $bunchSize5 = trim($_POST['bunchSize5']);
    }

    if (empty($_POST['totalPercent'])){
        $totalPercent = 0;
    } else {
        $totalPercent = trim($_POST['totalPercent']);
    }

    if (empty($_POST['unripe'])){
        $unripe = 0;
    } else {
        $unripe = trim($_POST['unripe']);
    }

    if (empty($_POST['underripe'])){
        $underripe = 0;
    } else {
        $underripe = trim($_POST['underripe']);
    }

    if (empty($_POST['emptyBunch'])){
        $emptyBunch = 0;
    } else {
        $emptyBunch = trim($_POST['emptyBunch']);
    }

    if (empty($_POST['rottenBunch'])){
        $rottenBunch = 0;
    } else {
        $rottenBunch = trim($_POST['rottenBunch']);
    }

    if (empty($_POST['longStalks'])){
        $longStalks = 0;
    } else {
        $longStalks = trim($_POST['longStalks']);
    }

    if (empty($_POST['dirtyBunch'])){
        $dirtyBunch = 0;
    } else {
        $dirtyBunch = trim($_POST['dirtyBunch']);
    }

    if (empty($_POST['duraBunch'])){
        $duraBunch = 0;
    } else {
        $duraBunch = trim($_POST['duraBunch']);
    }

    if (empty($_POST['oldBunch'])){
        $oldBunch = 0;
    } else {
        $oldBunch = trim($_POST['oldBunch']);
    }

    if (empty($_POST['totalQualityFactor'])){
        $totalQualityFactor = 0;
    } else {
        $totalQualityFactor = trim($_POST['totalQualityFactor']);
    }

    // Build grading detail array
    $gradingDetail = array(
        'mspo_perc' => $mspoPerc,
        'mspo_weight' => $mspoWeight,
        'non_mspo_perc' => $nonMspoPerc,
        'non_mspo_weight' => $nonMspoWeight,
        'bunch_size_25' => $bunchSize25,
        'bunch_size_10' => $bunchSize10,
        'bunch_size_9_10' => $bunchSize9_10,
        'bunch_size_8_9' => $bunchSize8_9,
        'bunch_size_7_8' => $bunchSize7_8,
        'bunch_size_6_7' => $bunchSize6_7,
        'bunch_size_5_6' => $bunchSize5_6,
        'bunch_size_5' => $bunchSize5,
        'total_percent' => $totalPercent,
        'unripe' => $unripe,
        'underripe' => $underripe,
        'empty_bunch' => $emptyBunch,
        'rotten_bunch' => $rottenBunch,
        'long_stalks' => $longStalks,
        'dirty_bunch' => $dirtyBunch,
        'dura_bunch' => $duraBunch,
        'old_bunch' => $oldBunch,
        'total_quality_factor' => $totalQualityFactor
    );
    $gradingDetailJson = json_encode($gradingDetail);

    // var_dump($ticketDo, $grader, $rejectWeight, $reduceWeight, $finalWeight, $gradingDetailJson, $modified_by, $weightId);exit;
    if ($update_stmt = $db->prepare("UPDATE Weight SET delivery_no=?, grader_id=?, reject_weight=?, reduce_weight=?, final_weight=?, grade_detail=?, modified_by=? WHERE id=?")) 
    {
        $update_stmt->bind_param('ssssssss', $ticketDo, $grader, $rejectWeight, $reduceWeight, $finalWeight, $gradingDetailJson, $modified_by, $weightId);

        // Execute the prepared query.
        if (! $update_stmt->execute()) {
            echo json_encode(
                array(
                    "status"=> "failed", 
                    "message"=> $update_stmt->error
                )
            );
        }
        else{
            $update_stmt->close();
            $db->close();

            echo json_encode(
                array(
                    "status"=> "success", 
                    "message"=> "Updated Successfully!!" 
                )
            );
        }
    }
}else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}

// Helper to handle nullable numeric inputs
function parseNullableFloat($key) {
    if (isset($_POST[$key]) && $_POST[$key] !== '') {
        return (float) $_POST[$key];
    }
    return 0;
}
?>