<?php
require_once("../db.php");
$project = sel("project", ["id" => $_GET["id"]]);
$faces = sels("face", ["project_id" => $_GET["id"]]);


//修改專案
if (isset($_POST["update"])) {
    upd("project", ["name" => $_POST["name"], "des" => $_POST["des"]], ["id" => $_POST["update"]]);
    if (!empty($_POST["face"])) {
        del("face", ["project_id" => $_GET["id"]]);
        foreach ($_POST["face"] as $face) {
            ins("face", ["name" => $face["name"], "des" => $face["des"], "project_id" => $_GET["id"]]);
        }
    }
    header("location:project_main.php");
}


//刪除面向
if (isset($_POST["delete_face"])) {
    del("face", ["id" => $_POST["delete_face"]]);
    header("location:project_update.php?id={$_GET['id']}");
}

?>
<!-- 修改專案面向 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>

    <?php require_once("../nav.php"); ?>
    <form action="" method="post">
        <div class="container">

            <h2>專案修改</h2>
            <button class="btn btn-inverse pull-right" value="<?= $_GET["id"] ?>" name="update" type="submit">確認修改</button>
            <button onclick="insert_face()" class="btn btn pull-right" type="button">新增面向</button>
            <hr>

            <div class="row">

                <div class="span4">
                    <h4>專案名稱:<input value="<?= $project["name"] ?>" name="name" type="text" required></h4>
                    <h4>專案說明:<textarea name="des" cols="3" rows="3" required><?= $project["des"] ?></textarea></h4>
                </div>

                <div class="span8">
                    <table class="table">

                        <thead>
                            <tr>
                                <th>面相名稱</th>
                                <th>面相說明</th>
                            </tr>
                        </thead>

                        <tbody id="place">
                            <?php foreach ($faces as $key => $face) { ?>
                                <tr>
                                    <td><input value="<?= $face["name"] ?>" name="face[<?= $key ?>][name]" type="text" required></td>
                                    <td><input value="<?= $face["des"] ?>" name="face[<?= $key ?>][des]" type="text" required></td>
                                    <?php if ($key != 0) { ?>
                                        <td><button name="delete_face" value="<?= $face["id"] ?>" class="btn btn-danger" type="submit">刪除</button></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>
                </div>

            </div>

        </div>
    </form>

</body>

</html>
<!-- 新增、刪除面向 -->
<script>
    var count = <?= count($faces) ?>;

    function insert_face() {
        if (count < 10) {
            id = `<tr>
                        <td><input name="face[` + count + `][name]" type="text" required></td>
                        <td><input name="face[` + count + `][des]" type="text" required></td>
                        <td><button type="button" onclick="remove_face(event)" class="btn btn-danger">刪除</button></td>
                      </tr>`
            $("#place").append(id);
            count++;
        }
    }

    function remove_face(event) {
        count--;
        $(event.target).parents("tr").remove();
    }
</script>