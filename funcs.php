<?php
/**
 * (C) 2018. Aleksey Eremin
 * 04.09.18 23:07
 */

/*
 * Библиотека общих функций
 */

/**
 * Преобразование даты из формата SQL в строку русского формата DD.MM.YYYY
 * @param string $dat строка в формате SQL YYYY-MM-DD
 * @return string дата DD.MM.YYYY
 */
function dat2str($dat)
{
  $str = null;
  if(preg_match("/(\d{4})-(\d{1,2})-(\d{1,2})/",$dat, $mah)) {
    $y = $mah[1];  $m = $mah[2];  $d = $mah[3];
    $str = sprintf("%02d.%02d.%04d", $d,$m,$y);
  }
  return $str;
}

/**
 * Преобразование даты времени из формата SQL в строку русского формата DD.MM.YYYY HH:MM
 * @param string $dat строка в формате SQL YYYY-MM-DD HH:MM
 * @return string дата DD.MM.YYYY HH:MM
 */
function dattim2str($dat)
{
  $str = null;
  if(preg_match("/(\d{4})-(\d{1,2})-(\d{1,2})\s+(\d{1,2})[;:](\d{1,2})/",$dat, $mah)) {
    $y = $mah[1]; $m = $mah[2]; $d = $mah[3];
    $h = $mah[4]; $i = $mah[5];
    $str = sprintf("%02d.%02d.%04d %02d:%02d", $d,$m,$y, $h,$i);
  }
  return $str;
}

/**
 * Преобразование даты-времени из формата SQL в строку русского формата DD.MM.YYYY
 * @param string $dat строка в формате SQL YYYY-MM-DD HH:MM[:SS]
 * @return string дата DD.MM.YYYY
 */
function tim2str($dat)
{
  $str = "00:00";
  if(preg_match("/(\d{1,2})[;:](\d{1,2})/",$dat, $mah)) {
    $h = $mah[1];  $m = $mah[2];;
    $str = sprintf("%02d:%02d", $h,$m);
  }
  return $str;
}

/**
 * убрать время из строки, если оно 00:00
 * @param string $dat строка в формате SQL YYYY-MM-DD HH:MM[:SS]
 * @return string дата YYYY-MM-DD
 */
function dattimtrim($dat)
{
  $str = $dat;
  if (preg_match("/(\d{2,4})[\.,-](\d{1,2})[\.,-](\d{1,2})\s+(\d{1,2})[;:](\d{1,2})/", $dat, $match)) {
    $y = $match[1];
    $m = $match[2];
    $d = $match[3];
    $h = $match[4];
    $i = $match[5];
    if($h == 0 && $i == 0) {
      $str = $str = sprintf("%04d-%02d-%02d", $y,$m,$d);
    } else {
      $str = $str = sprintf("%04d-%02d-%02d %02d:%02d", $y,$m,$d,$h,$i);
    }
  }
  return $str;
}

/**
 * Преобразование строки русского формата DD.MM.[YY]YY в дату формата SQL YYYY-MM-DD
 * @param string $str строка в формате DD.MM.[YY]YY (вместо точки может быть запятая)
 * @return string дата  YYYY-MM-DD
 */
function str2dat($str)
{
  $dat = null;
  $d = 0; $y = '00';
  if (preg_match("/(\d{1,2})[\.,](\d{1,2})[\.,](\d{2,4})/", $str, $match)) {
    $d = $match[1];
    $m = $match[2];
    $y = $match[3];
  } else if (preg_match("/(\d{2,4})[\.,-](\d{1,2})[\.,-](\d{1,2})/", $str, $match)) {
    $d = $match[3];
    $m = $match[2];
    $y = $match[1];
  }
  if ($d > 0) {
    if ($y < 100) {
      $y = '20' . $y;
    }
    $dat = sprintf("%04d-%02d-%02d", $y, $m, $d);
  }
  return $dat;
}

/**
 * Преобразование строки русского формата DD.MM.[YY]YY HH:MM в дату формата SQL YYYY-MM-DD HH:MM
 * @param string $str строка в формате DD.MM.[YY]YY HH:MM или YYYY-MM-DD HH:MM (вместо точки может быть запятая)
 * @return string дата  YYYY-MM-DD HH:MM
 */
function str2dattim($str)
{
  $dat = null;
  $d = 0; $y = '00'; $m = 0;
  $h = 0; $i = 0;
  if (preg_match('/(\d{1,2})\D+(\d{1,2})\D+(\d{2,4})\s+(\d{1,2})\D+(\d{1,2})/A', $str, $match)) {
    $d = $match[1];
    $m = $match[2];
    $y = $match[3];
    $h = $match[4]; // час
    $i = $match[5]; // минута
  } else if (preg_match('/(\d{2,4})\D+(\d{1,2})\D+(\d{1,2})\s+(\d{1,2})\D+(\d{1,2})/A', $str, $match)) {
    $d = $match[3];
    $m = $match[2];
    $y = $match[1];
    $h = $match[4]; // час
    $i = $match[5]; // минута
  } else if (preg_match('/(\d{1,2})\D+(\d{1,2})\D+(\d{2,4})/', $str, $match)) {
    $d = $match[1];
    $m = $match[2];
    $y = $match[3];
  } else if (preg_match('/(\d{2,4})\D+(\d{1,2})\D+(\d{1,2})/', $str, $match)) {
    $d = $match[3];
    $m = $match[2];
    $y = $match[1];
  }
  if ($d > 0) {
    if ($y < 100) {
      $y = '20' . $y;
    }
    $dat = sprintf("%04d-%02d-%02d %02d:%02d", $y, $m, $d, $h, $i);
  }
  return $dat;
}

/**
/**
 * Проверяет корректность строки даты с заданным форматом
 * http://php.net/manual/ru/function.checkdate.php
 * http://php.net/manual/ru/datetime.createfromformat.php
 * @param string $dat     строка даты
 * @param string $format  формат строки даты
 * @return bool true - дата корректна, false - неправильная дата
 */
function validateDate($dat, $format = 'Y-m-d')
{
  $d = DateTime::createFromFormat($format, $dat);
  return ($d) && ($d->format($format) == $dat);
}

/**
 * Формирует начало страницы html
 * @param string $title    заголовок страницы
 * @param string $t        добавка в заголовок
 */
function printHeadPage($title, $t='')
{
  echo <<<_EOF
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>$title</title>
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="css/popup.css">
  <script type="text/javascript" language="javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" language="javascript" src="js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" language="javascript" src="js/jquery.jeditable.js"></script>
</head>
<body>

_EOF;
}

/**
 * вывести конец страницы
 */
function printEndPage()
{
  echo "\n</body>\n</html>\n";
}

/**
 * Переход в указанное место URL
 * @param string $url  URL перехода
 */
function gotoLocation($url)
{
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: " . $url);
}

/**
 * Возвращает первое поле в первой строке, заданного SQL-запроса
 * @param string $sql SQL запрос
 * @return null значение поля
 */
function getVal($sql)
{
  $val = null;
  $res = queryDb($sql);
  if ($row = fetchRow($res)) $val = $row[0];
  $res->close();
  return $val;
}

/**
 * Возвращает массив значений первой строки заданного SQL-запроса
 * @param string $sql запрос
 * @return null array цифровой массив значений
 */
function getVals($sql)
{
  $res = queryDb($sql);
  $row = fetchRow($res);
  $res->close();
  return $row;
}

/**
 * Простая обертка для функции выполнения запроса
 * @param string $sql   строка запроса
 * @return bool|mysqli_result результат запроса
 */
function queryDb($sql)
{
  global $My_Db;
  return $My_Db->query($sql);
}

/**
 * Простая обертка для функции загрузки числового массива полей строки запроса
 * @param mysqli_result $res    результат query
 * @return mixed    числовой массив результата
 */
function fetchRow($res)
{
  return $res->fetch_row();
}

/**
 * Простая обертка для функции загрузки ассоциативного массива полей строки запроса
 * @param mysqli_result $res     результат query
 * @return mixed    ассоциативный массив результата
 */
function fetchAssoc($res)
{
  return $res->fetch_assoc();
}

/**
 * Простая обертка для функции загрузки числового и ассоциативного массива полей строки запроса
 * @param mysqli_result $res  результат query
 * @return mixed  числовой и ассоцитивный массив строки
 */
function fetchArray($res)
{
  return $res->fetch_array();
}

/**
 * Выполнить SQL-запрос
 * @param string $sql  SQL-запрос
 * @return boolean|mixed результат выполнения оператора типа INSERT, DELETE, UPDATE
 */
function execSQL($sql)
{
  global $My_Db;
  $r = $My_Db->query($sql);
  return $r;
}

/**
 * Подготавливает оператор для выполнения подстановок в SQL запросе
 * @param string $sql  строка SQL запроса
 * @return mysqli_stmt подготовленный оператор
 */
function prepareSql($sql)
{
  global $My_Db;
  return $My_Db->prepare($sql);
}

/**
 * Преобразовывает символы кавычек и других символов входной строки в безопасные символы
 * @param string $str входная строка
 * @return string строка без кавычек
 */
function s2s($str)
{
  return htmlspecialchars($str, ENT_QUOTES);
}

/**
 * Проверить время активности пользователя (тайм-аут активности) сек
 * Если tmout равен нулю, то сбрасываем время метки в текущее без проверки.
 * @param int $tmout время допустимой неактивности, сек
 */
function test_timeout_user_actitiviti($tmout)
{
  // время ожидания активности пользователя
  $tsnow = date('U'); // текущее время
  $tsses = intval($_SESSION['datatime_work_metka']);
  $_SESSION['datatime_work_metka'] = $tsnow;
  if($tmout > 0 && $tsses > 0 && ($tsnow - $tsses) > $tmout) {
    unset($_SESSION['Uid']);
    unset($_SESSION['Reg']);
    $_SESSION["error_message"] = "<span style='color: blue'>Истекло время ожидания...</span>";
  }
}

/**
 * формирует форму с тэгом select и элементами option для переключения названного параметра.
 * форма выбора региона "автозапуском" https://javatalks.ru/topics/22399
 * @param string $nameParam  название параметра для выбора региона
 * @return string строка с формой
 */
function make_FormSelectRegion($nameParam)
{
  $myself = $_SERVER['PHP_SELF'];
  $reg = getIntPar($nameParam);
//  $str = <<<_EOF
//  <form action='$myself' method='post' name='FormSelReg'>
//  <select size=1 name='$nameParam' onchange="document.forms['FormSelReg'].submit()">
//_EOF;
  $str  = "<form action='$myself' method='post'>";
  $str .= "<select size=1 name='$nameParam' onchange='this.form.submit()'>";
  $rst = queryDb("SELECT id,nam FROM Regions WHERE id>=0 ORDER BY id");
  while(list($fid,$fnam)=fetchRow($rst)) {
    $s = ($reg == $fid)? 'selected': ''; // выбор региона
    $str .= "<option value='$fid' $s>$fnam</option>";
  }
  $rst->close();
  $str .= "</select></form>";
  return $str;
}

/**
 * Вернуть числовое значение параметра из формы или сессионной переменной
 * и задать этот параметр в сессию,
 * а если параметр формы не задан, то прочитать этот параметр из сессии.
 * @param string $namePar  имя параметра
 * @return int числовое значение параметра
 */
function getIntPar($namePar)
{
  $par = 0;
  if(array_key_exists($namePar, $_REQUEST)) {
    // вызвали форму
    $par = $_REQUEST[$namePar];
    $_SESSION[$namePar] = $par;
  } else {
    // форму не вызывали, проверим сессионную переменную
    if(array_key_exists($namePar, $_SESSION))
      $par = $_SESSION[$namePar];
  }
  return intval($par);
}

/**
 * Определить возможность редактирования оператора,
 * если uid больше 1 и регион равен -1, то это супер-пользователь,
 * который может редактировать любой регион.
 * Если регион больше 0, то можно редактировать только свой.
 * @param int $op_id  код оператора
 * @return bool можно редактировать
 */
function isCanEditOp($op_id)
{
  global $Uid, $Reg;
  if($Uid < 1)
    return false;
  if($Reg == -1)
    return true;
  // установим признак возможности редактирования строки оператора по региону
  $re = sprintf("%02d", $Reg);  // двузначный номер региона
  $canEditOp = intval(getVal("SELECT COUNT(*) FROM Opers WHERE op_id=$op_id AND regs LIKE '%$re%'"));
  return $canEditOp != 0;
}

/**
 * Определить возможность редактирования конкретной записи, по номеру региона
 * если Reg = -1,то это супер-пользователь, который может редактировать любой регион.
 * Если Reg > 1 то можно редактировать только свой регион.
 * @param int $regstr номер региона строки файла
 * @return bool можно редактировать
 */
function isCanEditRec($regstr)
{
  global $Uid, $Reg;
  if($Uid < 1)
    return false;
  if($regstr == $Reg || $Reg == -1)
    return true;
  return false;
}

/**
 * Определить возможность редактирования оправданий оператора,
 * если регион -1
 * то это супер-пользователь, который может редактировать.
 * @return bool можно редактировать
 */
function isSuperReg()
{
  global $Uid, $Reg;
  return $Uid > 0 && $Reg == -1;
}

/**
 * Сделать форму логина в зависимости от состояния лога
 * @param string $UrlGoto куда перейти после регистрации
 * @return string текст формы
 */
function makeFormLogin($UrlGoto = null)
{
  global $Uid;
  $s = 'вход';
  $t = '';
  if($Uid > 0) {
    $s = 'выход';
    $t = getVal("SELECT login FROM users WHERE uid=$Uid");
    $t = "title='$t'";
  }
  if(empty($UrlGoto)) $UrlGoto = $_SERVER['PHP_SELF'];
  $r = "<form method='post' action='../login.php' $t>" .
       "<input type='hidden' name='goto' value='$UrlGoto'>" .
       "<input type='submit' class='inputoutput' value='$s'>" .
       "</form>";
  return $r;
}

/**
 * Заменяет во входной строке название организационной формы предприятия
 * на сокращенный вариант
 * @param $str  String входная строка
 * @return String строка с замененным сокращением
 */
function zamenaOOO($str)
{
  $zamena = array(
      array('Акционерное общество',                     'АО'),
      array('Публичное акционерное общество',           'ПАО'),
      array('Общество с ограниченной ответственностью', 'ООО'),
      array('ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ', 'OOO'), // str_ireplace не работает
      array('Закрытое акционерное общество',            'ЗАО'),
      array('ЗАКРЫТОЕ АКЦИОНЕРНОЕ ОБЩЕСТВО',            'ЗАО'),
      array('Индивидуальный предприниматель',           'ИП')
  );
  for($i = 0; $i < count($zamena); $i++) {
    $str = str_replace($zamena[$i][0],$zamena[$i][1],$str);
  }
  return $str;
}
