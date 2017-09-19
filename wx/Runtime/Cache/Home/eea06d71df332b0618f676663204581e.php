<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>客户信息</title>
</head>
<body>
昵称:<?php echo ($result['nickname']); ?>
<br>
性别:<?php echo ($result['sex']); ?>
<br>
所在城市:<?php echo ($result['city']); ?>
</body>
</html>