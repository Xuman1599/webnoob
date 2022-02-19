<?php
if (!isset($_SESSION)) {
    session_start();
}
$db = mysqli_connect("127.0.0.1", "admin", "1234", "new50");
if (!$db) {
    die(mysqli_connect_error());
}
$db->query("SET NAMES UTF8");
function exArray($data, $s = ",", $c = "=")
{
    $array = [];
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            ($c == "LIKE") ? $sql = "`$k`$c'%$v%'" : $sql = "`$k`$c'$v'";
            $array[] = $sql;
        }
    } else {
        $array[] = $data;
    }
    $array = join($s, $array);
    return $array;
}
function sel($table, $where = "1")
{
    global $db;
    $w = exArray($where, "AND");
    $sql = "SELECT * FROM `$table`WHERE $w";
    $result = $db->query($sql);
    return $result->fetch_assoc();
}
function sels($table, $where = "1", $ex = "")
{
    global $db;
    $array = [];
    $w = exArray($where, "AND");
    $sql = "SELECT * FROM `$table`WHERE $w $ex";
    $result = $db->query($sql);
    while ($r = $result->fetch_assoc()) {
        $array[] = $r;
    }
    return $array;
}
function del($table, $where)
{
    global $db;
    $w = exArray($where, "AND");
    $sql = "DELETE FROM `$table`WHERE $w";
    $db->query($sql);
}
function ins($table, $data)
{
    global $db;
    $d = exArray($data);
    $sql = "INSERT INTO `$table`SET $d";
    $db->query($sql);
    var_dump($sql);
    return $db->insert_id;
}
function upd($table, $data, $where)
{
    global $db;
    $d = exArray($data);
    $w = exArray($where, "AND");
    $sql = "UPDATE `$table`SET $d WHERE $w";
    $db->query($sql);
}
function dd($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}
function querys($sql){
	global $db;
	$array = [];
	$result = $db->query($sql);
	while($r = $result->fetch_assoc()){
		$array[] = $r;
	}
	return $array;
}
function query($sql){
	global $db;
	$result = $db->query($sql);
	return $result->fetch_assoc();
}