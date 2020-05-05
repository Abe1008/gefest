<?php
/**
 * Copyright (c) 2018. Aleksey Eremin
 * 14.04.2020
 */
/*
 * сохранение результата редактирования в поле таблицы dzs
 * https://appelsiini.net/projects/jeditable/
 *
 * Если указан параметр:
 *    newrecord - добавить запись (newrecord)
 *    delRec    - удалить запись (delRec)
 *    addDoc    - добавить вложение - файл
 *    delDoc    - удалить вложение - файл
 *
 * Если их нет, то значит редактируем поле в одной из заметок
 * должны получить два аргумента id и value
 *
 */

require_once "common.php";
// открытая БД $My_Db

// Вставка новой записи?
// аргумент: newrecord - код оператора
if(array_key_exists('newrecord', $_REQUEST)) {
  $val = intval($_REQUEST['newrecord']);
  doNewrecord($val);
  exit();
}

// отметить время "сделал"ь
// аргумент: sdelalId номер кода записи
if(array_key_exists('sdelalId', $_REQUEST)) {
  $id = intval($_REQUEST['sdelalId']);  // код записи
  sdelalNow($id);
  exit();
}

// удалить запись
// аргумент: delRec номер кода записи
if(array_key_exists('delRec', $_REQUEST)) {
  $idr = intval($_REQUEST['delRec']);  // код записи
  doDelRec($idr);
  exit();
}
// обновить запись
// аргумент: updWdat номер кода записи
if(array_key_exists('updWdat', $_REQUEST)) {
  $idr = intval($_REQUEST['updWdat']);  // код записи
  doUpdWdat($idr);
  exit();
}

// обработка редактирования
// параметры editable.js
$f_id  = $_REQUEST['id'];
$f_val = $_REQUEST['value'];
if(empty($f_id)) {
    die ("?-Error-Нет нужных аргументов [" . __FILE__ . " " . __LINE__ . ']');
}

// первая буква - поле D (dat) T (time) P (predmet) S (subj) M (mesto) U (url)
$l1 = substr($f_id, 0, 1);      // первая буква
$Id = intval(substr($f_id,1));  // номер id записи в  таблице
// $fldval - идет в БД
// $f_val  - отображается на экране
switch ($l1) {
  // дата, из $f_value выделим дату
  case 'D':
    $fldnam = 'dat';
    $fldval = str2dattim($f_val);
    if(!validateDate($fldval, 'Y-m-d H:i')) die("data error");
    $f_val  = $fldval;
    break;
  // номер урока
  case 'N':
    $fldnam = 'urok';
    $fldval = intval($f_val);
    $f_val  = $fldval==0? '': $fldval;
    break;
  // строка предмет
  case 'P':
    $fldnam = 'predmet';
    $fldval = trim($f_val);
    $f_val  = $fldval;
    break;
  // строка тема
  case 'S':
    $fldnam = 'subj';
    $fldval = trim($f_val);
    $f_val  = $fldval;
    break;
  // строка место
  case 'M':
    $fldnam = 'mesto';
    $fldval = trim($f_val);
    $f_val  = $fldval;
    break;
  // строка URL
  case 'U':
    $fldnam = 'url';
    $fldval = trim($f_val);
    $f_val  = $fldval;
    break;
  default:
    die("?-Error-неверный формат идентификатора редактируемого поля");
}
$stmt = prepareSql("UPDATE dzs SET $fldnam=? WHERE id=$Id");
$stmt->bind_param('s', $fldval);
if(! $stmt->execute()) die("?-Error-Ошибка обновления записи");
// $stmt->close();
//
echo $f_val;

/**
 * Добавить новую запись для оператора
 * @param int $opId код оператора
 */
function  doNewrecord($opId)
{
  $f_Dat = $_REQUEST['f_dat'];
  $f_Urok = intval($_REQUEST['f_urok']);
  $f_Predmet = $_REQUEST['f_predmet'];
  $f_Subj = $_REQUEST['f_subj'];

  $sql = "INSERT INTO dzs (dat,urok,predmet,subj) VALUES (?,?,?,?)";
  $stmt = prepareSql($sql);
  $stmt->bind_param('siss',$f_Dat, $f_Urok, $f_Predmet, $f_Subj);
  if(! $stmt->execute()) die("?-Error-Ошибка добавления записи");
  // $stmt->close();
  gotoHome();
}

/**
 * Сделать отметку "сделал"
 * @param int $idRec индекс записи
 */
function  sdelalNow($idRec)
{
  execSQL("UPDATE dzs SET sdelano=NOW() WHERE id=$idRec");
  //
  gotoHome();
}

/**
 * Удалить заметку
 * @param int $idRec код заметки
 */
function  doDelRec($idRec)
{
  execSQL("DELETE FROM dzs WHERE id=$idRec");
  //
  gotoHome();
}

/**
 * Обновить время записи (для разрешения редактирования и/или удаления)
 * @param int $idRec код заметки
 */
function  doUpdWdat($idRec)
{
  execSQL("UPDATE dzs SET wdat=NOW() WHERE id=$idRec");
  //
  gotoHome();
}

/**
 * Перейти на страницу заметок указанного оператора
 */
function  gotoHome()
{
  gotoLocation("index.php");
}
