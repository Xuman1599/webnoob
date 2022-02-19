<?php
require_once("../db.php");
// 使用者意見發表統計 統計資料
// user_total
if ($_GET["mode"] == "user_total") {
    if (isset($_GET["opinion_count"])) { //總意見
        echo json_encode(array_map('intval', explode(',', $_GET["opinion_count"])));
    }
    if (isset($_GET["name_data"])) {
        foreach (explode(",", $_GET["name_data"]) as $id) { //名字      
            $names[] = implode(array_values(query("SELECT `users`.`name` FROM `users` WHERE `id` = $id")));
        }
        echo json_encode($names);
    }
}
// 使用者評1~5分個別次數 統計資料
if ($_GET["mode"] == "user_each_score") {
    for ($i = 1; $i <= 5; $i++) {
        for ($inside = 0; $inside < 2; $inside++) {
            $opinion_scores_count[0] = "第" . $i . "次評分";
            $opinion_scores_count[1] = count(sels("opinion_score", ["score" => $i, "user_id" => $_GET["user_id"]]));
        }
        $count[] = $opinion_scores_count;
    }
    echo json_encode($count);
}


if ($_GET["mode"] == "project") {
    $projects = sels("project");

    if (isset($_GET["name"])) {
        foreach ($projects as $project) {
            $names[] = $project["name"];
        }
        echo json_encode($names);
    }
    if (isset($_GET["count"])) {
        foreach ($projects as $project) {
            $count[] = count(sels("opinion", ["project_id" => $project["id"]]));
        }
        echo json_encode($count);
    }
}
if ($_GET["mode"] == "face") {
    foreach (sels("face", ["project_id" => $_GET["project_id"]]) as $face) {
        for ($i=0; $i < 2; $i++) { 
            $data[0] = $face["name"];
            $data[1] = count(sels("opinion", ["face_id" => $face["id"]]));
        }
        $datas[] = $data;
    }
    echo json_encode($datas);
}
