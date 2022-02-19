<?php
require_once("db.php");
if (isset($_POST["login"])) {
    // if ($rand == $_POST["check"]) {
    $user = sel("users", ["account" => $_POST["account"], "password" => $_POST["password"]]);
    if ($user != null) {
        $_SESSION["user"] = $user;
        if ($user["level"] == 1) {
            header("location:admin/account_main.php"); //管理員
        } else {
            header("location:default/project_main.php"); //一般人
        }
    } else {
        echo "<script>alert('帳號或密碼錯誤');location.href='';</script>";
    }
    // }else{
    //     echo"<script>alert('驗證碼錯誤');</script>";
    // }
}
?>
<!-- 首頁 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php");  ?>
</head>

<body>
    <div class="container">
        <h2>專案討論系統</h2>
        <hr>
        <div class="row">
            <div class="span6">

                <form action="" method="post">
                    <input name="account" placeholder="帳號" type="text" required><br>
                    <input name="password" placeholder="密碼" type="password" required><br>
                    <input name="check" placeholder="驗證碼" type="text"><br>
                    <button class="btn btn-inverse" name="login" type="submit">登入</button>
                </form>

            </div>

            <div class="span6">
                <h3>進度</h3>
                <pre>
                    使用者(ok)
                    專案(ok)
                    成員(ok)
                    面向(ok)
                    意見(ok)
                    執行方案(almost_ok)剩下自動製作
                    統計圖表()
                </pre>
                <pre>
                    問題:
                    json傳多筆資料合併
                </pre>
            </div>

        </div>
    </div>
</body>

</html>