<?php
require_once("../db.php");

//開始發表意見
if (isset($_POST["able_start"])) {
    upd("project", ["able" => 1], ["id" => $_GET["id"]]);
    header("location:face.php?id={$_GET['id']}");
}

//結束發表意見
if (isset($_POST["able_end"])) {
    upd("project", ["able" => 0], ["id" => $_GET["id"]]);
    header("location:face.php?id={$_GET['id']}");
}

?>
<!-- 專案面向 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php require_once("../nav.php"); ?>

    <form action="" method="post">
        <div class="container">
            <h2>專案內容</h2>

            <!-- 一般成員的連結 -->
            <?php if ($_SESSION["user"]["level"] != 1) { ?>
                <a class="pull-right btn" href="../default/project_main.php">返回</a>
            <?php } ?>

            <a class="pull-right btn btn-inverse" href="plan.php?id=<?= $_GET['id'] ?>">執行方案頁面</a>
            <hr>
            <h3>面向清單</h3>


            <!-- 管理員or組長功能 -->
            <?php
            $member = sel("member", ["project_id" => $_GET["id"], "user_id" => $_SESSION["user"]["id"]]);
            $user = sel("users", ["id" => $_SESSION["user"]["id"]]);
            if ($member["leader"] == 1 || $user["level"] == 1) {
                $project = sel("project", ["id" => $_GET["id"]]);
                if ($project["able"] != 1) {
            ?>
                    <button class="btn" name="able_start" type="submit">開始發表意見</button>
                <?php } else { ?>
                    <button class="btn btn-inverse" name="able_end" type="submit">結束發表意見</button>
            <?php }
            } ?>

            <!-- 面向意見 -->
            <table class="table">
                <thead>
                    <th>編號</th>
                    <th>面向名稱</th>
                    <th>面向說明</th>
                </thead>
                <tbody>
                    <?php
                    $faces = sels("face", ["project_id" => $_GET["id"]]);
                    if (!empty($faces)) {
                        foreach ($faces as $key => $face) {
                    ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= $face["name"] ?></td>
                                <td><?= $face["des"] ?></td>
                                <td><a class="btn btn-info" href="opinion_list.php?id=<?= $face["id"] ?>">意見列表</a></td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>


        </div>
    </form>
</body>

</html>