<?php
require_once("../db.php");
$face = sel("face", ["id" => $_GET["id"]]);
$project = sel("project", ["id" => $face["project_id"]]);

// 新增意見
if (isset($_POST["submit"])) {

    //如果有延伸意見的話
    if (isset($_GET["reply_id"])) {
        $new_opinion = ins("opinion", ["name" => $_POST["name"], "des" => $_POST["des"], "time" => date("Y-m-d H:m:s"), "reply_id" => $_GET["reply_id"], "user_id" => $_SESSION["user"]["id"], "face_id" => $face["id"], "project_id" => $project["id"]]);
    } else {
        $new_opinion = ins("opinion", ["name" => $_POST["name"], "des" => $_POST["des"], "time" => date("Y-m-d H:m:s"), "user_id" => $_SESSION["user"]["id"], "face_id" => $face["id"], "project_id" => $project["id"]]);
    }


    //如果有上傳檔案的話
    if (isset($_FILES)) {
        //移動檔案從暫存到upload
        move_uploaded_file($_FILES["file"]["tmp_name"], "../upload/" . $_FILES["file"]["name"]);
        //新增upload
        $upd_opinion = upd("opinion", ["upload" => $_FILES["file"]["name"]], ["id" => $new_opinion]);

        //副檔名
        $exten = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);

        // 新增檔案類型 upload_type
        if ($exten == "jpg") {
            upd("opinion", ["upload_type" => "image"], ["id" => $new_opinion]); //圖檔
        } elseif ($exten == "mp3") {
            upd("opinion", ["upload_type" => "audio"], ["id" => $new_opinion]); //音樂檔
        } elseif ($exten == "mp4") {
            upd("opinion", ["upload_type" => "video"], ["id" => $new_opinion]); //視訊檔
        }
    }

    header("location:opinion_list.php?id={$_GET['id']}");
}
?>
<!-- 發表意見 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php require_once("../nav.php"); ?>
    <div class="container">
        <form action="" method="post" enctype="multipart/form-data">
            <h2>發表意見</h2>

            <button name="submit" class="btn btn-inverse pull-right" type="submit">送出</button>
            <a class="btn pull-right" href="opinion_list.php?id=<?= $_GET['id'] ?>">返回</a>
            <hr>

            意見標題:<input name="name" type="text" required><br>
            意見說明:<textarea name="des" id="" cols="5" rows="3" required></textarea><br>
            上傳檔案:<input type="file" accept=".jpg,.mp3,.mp4" name="file" id="">

        </form>
    </div>
</body>

</html>