<?php
require_once("../db.php");
$opinion = sel("opinion", ["id" => $_GET["id"]]);

?>
<!-- 查看意見的檔案 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php
    if ($_SESSION["user"]["level"] == 1) {
        require_once("../nav.php");
    } else { ?>
        <br><br>
    <?php } ?>

    <div class="container">
        <h2>查看檔案</h2>

        <?php if (!isset($_GET["reply_id"])) { ?>
            <a class="btn pull-right" href="opinion_list.php?id=<?= $_GET['face_id'] ?>">返回</a>
        <?php } else { ?>
            <a class="btn pull-right" href="opinion_original.php?face_id=<?= $_GET['face_id'] ?>&id=<?= $_GET["reply_id"] ?>">返回</a>
        <?php } ?>
        <hr>

        <!-- 影片 -->
        <?php if ($opinion["upload_type"] == "video") { ?>

            <video controls src="../upload/<?= $opinion["upload"] ?>"></video>

        <?php } ?>

        <!-- 圖片 -->
        <?php if ($opinion["upload_type"] == "image") { ?>
            <img src="../upload/<?= $opinion["upload"] ?>" alt="">
        <?php } ?>

        <!-- 聲音 -->
        <?php if ($opinion["upload_type"] == "audio") { ?>
            <audio controls src="../upload/<?= $opinion["upload"] ?>"></audio>
        <?php } ?>

    </div>
</body>

</html>