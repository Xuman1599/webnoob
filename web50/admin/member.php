<?php
require_once("../db.php");
// 所有使用者
$users = sels("users");
//所有專案成員
$members = sels("member", ["project_id" => $_GET["id"]]);

//未加入專案成員的使用者
foreach ($users as $user) {
    if (empty(sel("member", ["user_id" => $user["id"]]))) {
        $all_user[] = $user;
    }
}

//更換組長
if (isset($_POST["leader"])) {
    //把所有成員的權限歸零
    upd("member", ["leader" => 0], ["project_id" => $_GET["id"]]);
    // 再重新更改權限
    upd("member", ["leader" => 1], ["id" => $_POST["change_leader"]]);
    header("location:member.php?id={$_GET['id']}");
}
?>
<!-- 專案成員 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body style="user-select:none;">
    <?php require_once("../nav.php"); ?>
    <form action="" method="post">
        <div class="container">

            <h2>專案成員</h2>
            <button class="btn btn-info pull-right" name="leader" type="submit">更換組長</button>
            <hr>

            <div class="row">
                <div id="users" class="span5">
                    <h3>使用者清單</h3>
                    <hr>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>使用者名稱</th>
                            </tr>
                        </thead>
                        <tbody id="place1">
                            <?php if (!empty($all_user)) {
                                foreach ($all_user as $user) { ?>
                                    <tr data-id="<?= $user["id"] ?>">
                                        <td><?= $user["name"] ?></td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>

                </div>

                <div id="member" class="span5">
                    <h3>專案成員清單</h3>
                    <hr>

                    <table class="table">

                        <thead>
                            <th>專案成員名稱</th>
                            <th>專案成員帳號</th>
                            <th>階級</th>
                            <th>組員</th>
                        </thead>

                        <tbody id="place2">
                            <?php if (!empty($members)) {
                                foreach ($members as $member) {
                                    $user = sel("users", ["id" => $member["user_id"]]);
                            ?>
                                    <tr data-id="<?= $member["id"] ?>">
                                        <td><?= $user["name"] ?></td>
                                        <td><?= $user["account"] ?></td>
                                        <?php if ($member["leader"] == 1) { ?>
                                            <th>組長</th>
                                        <?php } else { ?>
                                            <td>組員</td>
                                        <?php } ?>
                                        <td><input name="change_leader" <?= ($member["leader"] == 1) ? "checked" : "" ?> value="<?= $member["id"] ?>" type="radio"></td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>

                    </table>

                </div>
            </div>

        </div>
    </form>

    <!-- 拖移 新增使用者到專案組員 or 刪除組員 -->
    <script>
        var project_id = <?= $_GET['id'] ?>;
        var count = <?= count($members) ?>;

        $("#place1 tr").draggable({
            helper: "clone"
        });

        $("#member").droppable({
            drop: function(event, ui) {
                let id = $(ui.draggable).data("id");
                $("#place2").append(ui.draggable);
                fetch("fetch.php?project_id=" + project_id + "&user_id=" + id);
            }
        });

        if (count > 1) {

            $("#place2 tr").draggable({
                helper: "clone"
            });

            $("#users").droppable({
                drop: function(event, ui) {
                    let id = $(ui.draggable).data("id");
                    $("#place1").append(ui.draggable);
                    fetch("fetch.php?project_id=" + project_id + "&member_id=" + id)
                        .then(res => res.text())
                        .then(res => {
                            if (res != "") {
                                alert(res)
                                location.href = 'member.php?id=' + project_id
                            }
                        })
                }
            })

        }
    </script>
</body>

</html>