<?php
/**
 * Created by PhpStorm.
 * 这个API记录加QQ群的记录，以期将用户名与QQ号对应起来，来解决共享帐号或一人多号作弊
 * User: tongyifan
 * Date: 18-12-29
 * Time: 上午12:14
 */

$token = htmlspecialchars($_POST['token']);
$sign = htmlspecialchars($_POST['sign']);
$qq = htmlspecialchars($_POST['qq']);
$username = htmlspecialchars($_POST['username']);
if (!auth_token($token, $sign, $username . $qq)) {
    header('HTTP/1.1 401 Unauthorized');
    die();
}
$dupe_users = array();
$res = sql_query("SELECT username FROM users WHERE qq = " . sqlesc($qq) . " OR email LIKE '%$qq%' OR username LIKE '%$qq%'") or sqlerr(__FILE__, __LINE__);
while ($arr = mysql_fetch_array($res)) {
    $dupe_users[] = $arr['username'];
}

if (!empty($dupe_users)) {
    foreach ($dupe_users as $dupe_user) {
        if ($dupe_user != $username) {
            $subject = "QQ群内可疑用户";
            $msg = "申请者站内用户名：$username\n\t申请者QQ号：$qq\n\t存疑用户：{$dupe_user}";
            sql_query("INSERT INTO staffmessages (sender, added, msg, subject) VALUES(10, " . sqlesc(date("Y-m-d H:i:s")) . ", " . sqlesc($message) . ", " . sqlesc($subject) . ")") or sqlerr(__FILE__, __LINE__);
            $Cache->delete_value('staff_message_count');
            $Cache->delete_value('staff_new_message_count');
        }
    }
}

sql_query("UPDATE users SET qq = " . sqlesc($qq) . " WHERE username = " . sqlesc($username)) or sqlerr(__FILE__, __LINE__);