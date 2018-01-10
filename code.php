<?php
require './Captcha.php';
$captcha = new Captcha;
$captcha->width = 200;
$captcha->height = 80;
$captcha->size = 30;
$captcha->show();
