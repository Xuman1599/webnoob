<?php
require_once("../db.php");

if (isset($_POST["insert"])) { //新增
    $project_id = ins("project", ["name" => $_POST["name"], "des" => $_POST["des"]]);
    ins("member", ["user_id" => $_SESSION["user"]["id"], "project_id" => $project_id, "leader" => 1]);
    if (isset($_POST["face"])) {
        foreach ($_POST["face"] as $face) {
            ins("face", ["name" => $face["name"], "des" => $_POST["des"], "project_id" => $project_id]);
        }
    }
    header("location:project_main.php");
}

?>
<!-- 新增專案 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php require_once("../nav.php"); ?>
    <form action="" method="post">
        <div class="container">
            <h2>新增專案</h2>
            <button class="btn btn-inverse pull-right" name="insert" type="submit">新增專案</button>
            <button type="button" class="btn pull-right" onclick="insert_face()">新增面相</button>
            <hr>

            <div class="row">

                <div class="span4">
                    <h4>專案名稱:<input name="name" type="text" required></h4>
                    <h4>專案說明:<textarea name="des" id="" cols="3" rows="3" required></textarea></h4>
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
                            <tr>
                                <td><input name="face[0][name]" type="text" required></td>
                                <td><input name="face[0][des]" type="text" required></td>
                            </tr>
                        </tbody>

                    </table>
                </div>

            </div>

        </div>
    </form>

    <!-- 新增面向 -->
    <script>
        var count = 1;

        function insert_face() {
            if (count < 10) {
                id = `<tr>
                        <td><input name="face[` + count + `][name]" type="text" required></td>
                        <td><input name="face[` + count + `][des]" type="text" required></td>
                        <td><button type="button" onclick="del_face(event)" class="btn btn-danger">刪除</button></td>
                      </tr>`
                $("#place").append(id);
                count++;
            }
        }

        function del_face(event) {
            count--;
            $(event.target).parents("tr").remove();
        }
    </script>

</body>

</html>