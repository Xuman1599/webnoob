<?php
require_once("../db.php");
//所有使用者
$users = sels("users");

//管理員
$admin = $users[0];

//修改
if (isset($_POST["edit"])) {
    upd("users", ["name" => $_POST["name"], "password" => $_POST["password"]], ["id" => $_POST["edit"]]);
    header("location:account_main.php");
}

//刪除
if (isset($_POST["delete"])) {
    del("users", ["id" => $_POST["delete"]]);
    header("location:account_main.php");
}

//新增
if (isset($_POST["insert"])) {
    ins("users", ["name" => $_POST["ins_name"], "account" => $_POST["ins_account"], "password" => $_POST["ins_password"]]);
    header("location:account_main.php");
}


// 遞增排序
if (isset($_POST["asc"])) {
    $users = $db->query("SELECT * FROM `users` WHERE `level` != 1 ORDER BY `account` ASC");
}

//遞減排序
if (isset($_POST["desc"])) {
    $users = $db->query("SELECT * FROM `users` WHERE `level` != 1 ORDER BY `account` DESC");
}

?>
<!-- 管理帳號 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php require_once("../nav.php"); ?>
    <div class="container">
        <form action="" method="post">
            <h2>帳號管理</h2>
            <a class="pull-right btn btn-inverse" data-toggle="modal" href="#ins">新增帳號</a>
            <button class="pull-right btn" name="asc" type="submit">遞增排序</button>
            <button class="pull-right btn btn-primary" name="desc" type="submit">遞減排序</button>
        </form>
        <hr>
        <form action="" method="post">
            <table class="table hover">
                <thead>
                    <th>使用者名稱</th>
                    <th>使用者帳號</th>
                    <th>使用者密碼</th>
                </thead>
                <tbody>
                    <!-- 管理員 -->
                    <?php if (isset($admin)) { ?>
                        <th><?= $admin["name"] ?></th>
                        <th><?= $admin["account"] ?></th>
                        <th><?= $admin["password"] ?></th>
                        <?php }
                    foreach ($users as $user) {
                        if ($user["level"] != 1) { ?>
                            <!-- 一般使用者 -->
                            <tr>
                                <td><input name="name" value="<?= $user["name"] ?>" type="text" required></td>
                                <td><input value="<?= $user["account"] ?>" type="text" disabled></td>
                                <td><input name="password" value="<?= $user["password"] ?>" type="password" required></td>
                                <td><button class="btn btn-info" value="<?= $user["id"] ?>" name="edit" type="submit">修改</button></td>
                                <td><button class="btn btn-danger" value="<?= $user["id"] ?>" name="delete" type="submit">刪除</button></td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>
        </form>
    </div>



    <form action="" method="post">
        <div class="modal fade lade" id="ins">
            <div class="modal-header">
                <h3>新增帳號</h3>
            </div>
            <div class="modal-body">
                使用者名稱:<input name="ins_name" type="text" required><br>
                使用者帳號:<input name="ins_account" type="text" required><br>
                使用者密碼:<input name="ins_password" type="password" required><br>
            </div>
            <div class="modal-footer">
                <button class="btn btn-inverse pull-right" name="insert" type="submit">註冊</button>
            </div>
        </div>
    </form>
</body>

</html>