<?php
require_once("../db.php");
// member.php的

//新增專案成員
if (isset($_GET["user_id"])) {
    if (empty(sel("member", ["user_id" => $_GET["user_id"], "project_id" => $_GET["project_id"]]))) {
        ins("member", ["user_id" => $_GET["user_id"], "leader" => 0, "project_id" => $_GET["project_id"]]);
    }
}

//刪除專案成員
if (isset($_GET["member_id"])) {
    $member = sel("member", ["id" => $_GET["member_id"]]);
    if (!empty($member)) {
        $user = sel("users", ["id" => $member["user_id"]]);
        if ($member["leader"] != 1 && $user["level"] != 1) {
            del("member", ["id" => $_GET["member_id"]]);
        } else {
            echo ("不能刪除管理員或是組長");
        }
    }
}
