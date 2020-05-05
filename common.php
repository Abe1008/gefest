<?php
/**
 * (C) 2020. Aleksey Eremin
 */
/*
 * общие функции и классы
 */
require_once "MyDB.php";
// объект базы данных
$My_Db = new MyDB() ;

require_once "funcs.php";
// запуск сессии
session_start();
//// идентификатор пользователя
//if(!array_key_exists('Uid', $_SESSION)) {
//  // если пользователь не зарегистрирован - будет всё читать
//  $_SESSION['Uid'] = 0;
//  $_SESSION['Reg'] = 0;
//}
