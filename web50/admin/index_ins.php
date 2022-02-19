<?php
require_once("../db.php");


if (!empty($_POST)) {

    //新增
    if (isset($_POST["insert"])) {
        ins("index", ["name" => $_POST["name"], "project_id" => $_GET["id"]]);
    }

    //修改
    if (isset($_POST["edit"])) {
        upd("index", ["name" => $_POST["edit_name"]], ["id" => $_POST["edit"]]);
    }

    //刪除
    if (isset($_POST["delete"])) {
        del("index", ["id" => $_POST["delete"]]);
    }

    header("location:index_ins.php?id={$_GET['id']}");
}
?>
<!-- 評分指標頁面 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php require_once("../nav.php"); ?>

    <form action="" method="post">
        <div class="container">
            <h2>建立評分指標</h2>
            <a class="pull-left btn btn-inverse" href="plan.php?id=<?= $_GET['id'] ?>">返回</a>

            <div class="pull-right">
                <input name="name" type="text">
                <button name="insert" class="btn btn-info" type="submit">建立</button>
            </div>

            <hr>
            <h3>指標名稱</h3>

            <?php
            $indexs = sels("index", ["project_id" => $_GET["id"]]);
            if (!empty($indexs)) {
                foreach ($indexs as $index) { ?>
                    <input name="edit_name" value="<?= $index["name"] ?>" type="text" required>
                    <button name="edit" class="btn btn-primary" value="<?= $index["id"] ?>" type="submit">修改</button>
                    <button name="delete" class="btn btn-danger" value="<?= $index["id"] ?>" type="submit">刪除</button><br>
            <?php }
            } ?>

        </div>
    </form>
</body>

</html>