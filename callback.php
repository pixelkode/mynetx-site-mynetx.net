<?php

mail('mynetx1@googlemail.com', 'Callback called', print_r($_GET, 1).print_r($_POST, 1).print_r($_SERVER, 1).' '.gethostbyaddr($_SERVER['REMOTE_ADDR']), "From: me@random.mynetx.net\r\n");

echo 'It works!';
