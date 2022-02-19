<?php
require_once("../db.php");
?>
<!-- 一般人專案介面 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/jquery.js"></script>
    <script src="../js/jquery-ui.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/highcharts.js"></script>
    <script src="../js/js.js"></script>
</head>

<body>
    <?php require_once("../nav.php"); ?>
    <div class="container">
        <h2>個人專案</h2>
        <hr>
        <?php
        $user = sel("users", ["id" => $_SESSION["user"]["id"]]);
        $members = sels("member", ["user_id" => $_SESSION["user"]["id"]]);
        if (!empty($members)) {
            foreach ($members as $member) {
                $project = sel("project", ["id" => $member["project_id"]]);
        ?>
                <h3><?= $project["name"] ?>&nbsp;<a class="btn" href="../admin/face.php?id=<?= $member["project_id"] ?>">查看</a></h3>
            <?php } ?>
        <?php } else { ?>
            <h3>此成員沒有專案</h3>
        <?php } ?>
    </div>
</body>

</html>