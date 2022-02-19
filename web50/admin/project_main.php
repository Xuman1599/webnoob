<?php
require_once("../db.php");
$projects = sels("project");

// 刪除專案
if (isset($_POST["delete"])) {
    del("project", ["id" => $_POST["delete"]]);
    header("location:project_main.php");
}

?>

<!-- 專案顯示頁面 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php require_once("../nav.php"); ?>

    <form action="" method="post">
        <div class="container">

            <h2>專案管理</h2>
            <a href="project_insert.php" class="pull-right btn-inverse btn">新增專案</a>
            <hr>

            <table class="table hover">

                <thead>
                    <th>專案名稱</th>
                    <th>專案說明</th>
                </thead>

                <tbody>
                    <?php
                    if (!empty($projects)) {
                        foreach ($projects as $project) { ?>
                            <tr>
                                <td><?= $project["name"] ?></td>
                                <td><?= $project["des"] ?></td>
                                <td><a class="btn btn-inverse" href="member.php?id=<?= $project["id"] ?>">成員</a></td>
                                <td><a class="btn btn-info" href="face.php?id=<?= $project["id"] ?>">內容</a></td>
                                <td><a class="btn btn-success" href="project_update.php?id=<?= $project["id"] ?>">修改</a></td>
                                <td><button name="delete" value="<?= $project["id"] ?>" class="btn btn-danger" onclick="return confirm('確定要刪除此專案嗎?')" type="submit">刪除</button></td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>

            </table>

        </div>
    </form>

</body>

</html>