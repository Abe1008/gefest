<?php
/**
 * (C) 2020. Aleksey Eremin
 * 14.04.2020
 */
/*
 * Список домашних заданий
 */
require_once "common.php";

printHeadPage("Домашние задания");

$dat = getVal("SELECT MAX(DATE_FORMAT(dat, '%Y-%m-%d')) FROM dzs WHERE sdelano IS NULL");
$str = makeNewForm($dat); // форма ввода нового задания

echo <<<_EOF
<table width="100%" border="0">
<tr>
<td width="50%" class="showdocnote"><b>Домашние задания</b></td>
<td align="right"><a href="arhiv.php" class="godoc">архив</a></td>
<td width="30%">&nbsp;</td>
</tr>
</table>

$str
<hr>

<table width="100%" class="spis" id="myTable">
<thead><tr>
 <th class='spis' width='10%'>дата</th>
 <th class='spis' width='2%'>дн</th>
 <th class='spis' width='2%'>ур.</th>
 <th class='spis' width='8%'>предмет</th>
 <th class='spis'>тема</th>
 <th class='spis' width="7%">место</th>
 <th class="spis" width="23%">URL</th>
 <th class="spis" width="18px">сделал</th>
 <th class="spis" width="18px">св.</th>
</tr></thead>
<tbody class="hightlight">

_EOF;

// выводим заметки
$sql = "SELECT id,dat,DAYOFWEEK(dat),urok,predmet,subj,mesto,url, TIMESTAMPDIFF(MINUTE, wdat, NOW())
        FROM dzs 
        WHERE sdelano IS NULL
        ORDER BY dat,urok,wdat";
$res = queryDb($sql);
$wd =  array("", "вс","пн","вт","ср","чт","пт","сб");
while(list($id,$dat,$w,$urok,$predmet,$subj,$mesto,$url,$elapse) = fetchRow($res)) {
  // запись обновлена менее 30 минут тому назад?
  $isEdt = ($elapse < 30);  // да - можно редактировать
  if($isEdt) {
    $cle ='edt';
    $clep='edtp'; // редактирование выбора предмет
    $clem='edtm'; // редактирование выбора место
  } else {
    $cle ='';
    $clep=''; // не-редактирование выбора предмет
    $clem=''; // не-редактирование выбора место
  }
  if(empty($urok)) $urok='';
  //
  echo "<tr class='spis'>";
  //$sdat = dattim2str($dat);
  $sdat = dattimtrim($dat);
  if(!$isEdt) {
    $sdat = $sdat . '<span class="white">' . $urok . '</span>';
  }
  $dd = $wd[$w];
  //$stim = tim2str($tim);
  echo "<td class='$cle spis' id='D$id'>$sdat</td>";
  echo "<td class='spis'>$dd</td>";
  echo "<td class='$cle spis' id='N$id'>$urok</td>";
  //echo "<td class='$cle spis' id='T$id'>$stim</td>\n";
  echo "<td class='$clep spis' id='P$id'>$predmet</td>\n";
  echo "<td class='$cle spis' id='S$id'>$subj</td>\n";
  echo "<td class='$clem spis' id='M$id'>$mesto</td>";
  $goto = '';
  if(!empty($url)) {
    if(!$isEdt) {
      // если нет редактирования, заменим строку с URL ссылкой
      $surl = substr($url,0,42) . (strlen($url) > 42? ' ...': '');
      $url="<a href='$url' target='_blank' class='nounderline'>$surl</a>";
    } else {
      // если есть URL сделаем добавку для пеехода
      $goto = "<a href='$url' target='_blank' class='nounderline'>|&gt;</a>";
    }
  }
  echo "<td class='$cle spis' id='U$id'>$url</td>";
  //$sbut = formSdelal($id); // форма с кнопкой
  $sdelal = "<a href='dzsave.php?sdelalId=$id' onclick='return confirm(\"Сделал задание?\")' title='сделал задание'><img src='img/sdel.png' alt='сделал'></a>";
  echo "<td class='spis' align='center'>$goto $sdelal</td>";
  if($isEdt) {
    // при редактировании - удалить
    $delog = "<a href='dzsave.php?delRec=$id' onclick='return confirm(\"Удалить задание?\")' title='удалить'><img src='img/doc_del.png' alt='удалить'></a>";
  } else {
    // при нередактировании - оживить
    $delog = "<a href='dzsave.php?updWdat=$id' onclick='return confirm(\"Оживить задание?\")' title='оживить'><img src='img/doc_plus.png' alt='оживить'></a>";
  }
  echo "<td class='spis' align='center'>$delog</td>";
  echo "</tr>\n";
}
$res->close();
echo '</tbody></table>';

// заполнить наборы для select
$jsonPredmet = makeTipSelectJson('predmets');
$jsonMesto   = makeTipSelectJson('mestos');
// подключим редактирование полей
echo <<<_EOF
<!-- программа  -->
<script type="text/javascript" language="javascript">
$(document).ready(function(){
    // сортировка таблицы и т.д.
    $('#myTable').DataTable( {
      //  scrollY:        "85vh",
      //  scrollCollapse: true,
      info:        true,
      paging:      false,
      // размещение элемнтов https://datatables.net/reference/option/dom
      dom: 'ift',   // https://stackoverflow.com/questions/8355638/datatables-place-search-and-entries-filter-under-the-table
      // https://datatables.net/reference/option/language
      language: {
        search:       "поиск:",
        zeroRecords:  "нет совпадающих записей",
        info:         "записей _TOTAL_",
        infoEmpty:    "совпадений 0",
        infoFiltered: "(всего _MAX_)"
      },
      // не сортировать и не искать в 5 столбце (самый первый - 0) где столбцы с классом 'nosort'
      // https://datatables.net/reference/option/columnDefs.targets .
      columnDefs: [
        { targets: [4,5,6,7,8], orderable:  false },
        { targets: [7,8], searchable: false }
      ]
    } );

  // подключим редактирование "в таблице на месте"
  $('td.edt').editable('dzsave.php', {
    placeholder: '...'
  });
  // подключим редактирование "в таблице на месте"
  $('td.edtp').editable('dzsave.php', 
  {
     placeholder: '',
     //data   : " {'E':'Letter E','F':'Letter F','G':'Letter G', 'selected':'F'}",
     data: '$jsonPredmet',
     type: 'select'
     /* submit : 'OK' */
  });
    // подключим редактирование "в таблице на месте"
  $('td.edtm').editable('dzsave.php', 
  {
     placeholder: '',
     data: '$jsonMesto',
     type: 'select'
  });
});
</script>

_EOF;
printEndPage();

// формирование кнопки "сделал" для записей
function formSdelal($id)
{
  $txt = <<<_EOF
  <form action="dzsave.php" method="post">
  <input type="hidden" name="sdelalId" value="$id">
  <input type="submit" value="сделал" class="info">
  </form>
_EOF;
  return $txt;
}

// подготовим список предметов
function  makeTipSelectJson($tabname)
{
  $res = queryDb("SELECT nam FROM $tabname ORDER BY nam");
  $arraytips = array();
  while(list($tip)=fetchRow($res)) $arraytips[$tip] = $tip;
  $res->close();
  $str = json_encode($arraytips); //($arraytips, JSON_UNESCAPED_UNICODE);
  return $str;
}

/**
 * формирует форму "новае задание"
 * @param $dat дата записи
 * @return string форма
 */
function makeNewForm($dat)
{
  $spredmet = "<select size=1 name='f_predmet' title='предмет'>";
  $rst = queryDb("SELECT nam FROM predmets ORDER BY nam");
  while(list($fnam)=fetchRow($rst)) {
    $spredmet .= "<option value='$fnam'>$fnam</option>";
  }
  $rst->close();
  $spredmet .= "</select>";
  $surok = <<<_EOF
  <select size=1 name='f_urok' title='урок'>
  <option value=''> </option>
  <option value='1'>1</option>
  <option value='2'>2</option>
  <option value='3'>3</option>
  <option value='4'>4</option>
  <option value='5'>5</option>
  <option value='6'>6</option>
  <</select>
_EOF;

  if(empty($dat)) {
    $dat = date("Y-m-d", time()+3600*24);
  }
  // кнопка "новая заметка"
  $frm = <<<_EOF
<div class="inputnew">
  <form action="dzsave.php" method="post">
  <input type="hidden" name="newrecord" value="1">
  <input type="date" name="f_dat" value="$dat">
  $surok
  $spredmet
  <input type="text" name="f_subj" size="80" placeholder="тема задания">
  <input type="submit" value="новое задание" class="info">
  </form>
</div>
_EOF;
  return $frm;
}
