<?php
/**
 * (C) 2020. Aleksey Eremin
 * 14.04.2020
 */
/*
 * Список архивных домашних заданий
 */
require_once "common.php";

printHeadPage("Архив домашних заданий");

echo <<<_EOF
<table width="100%" border="0">
<tr>
<td width="50%" class="showdocnote"><b>Архив домашних заданий</b></td>
<td align="right"><a href="index.php" class="godoc">новые</a></td>
<td width="30%">&nbsp;</td>
</tr>
</table>

<table width="100%" id="myTable1">
<thead><tr>
 <th class='spis' width='8%'>дата</th>
 <th class='spis' width='8%'>предмет</th>
 <th class='spis'>тема</th>
 <th class='spis' width="8%">место</th>
 <th class="spis" width="20%">URL</th>
 <th class="spis" width="8%">сделал</th>
</tr></thead>
<tbody class="hightlight">

_EOF;

// выводим заметки
$sql = "SELECT id,dat,predmet,subj,mesto,url,sdelano 
        FROM dzs
        WHERE Not sdelano IS NULL
        ORDER BY dat,predmet";
$res = queryDb($sql);
while(list($id,$dat,$predmet,$subj,$mesto,$url,$sdelano) = fetchRow($res)) {
  echo "<tr class='spis'>";
  $sdat = dattimtrim($dat);
  echo "<td class='spis'>$sdat</td>\n";
  echo "<td class='spis'>$predmet</td>\n";
  echo "<td class='spis'>$subj</td>\n";
  echo "<td class='spis'>$mesto</td>";
  if(!empty($url)) {
    $url = "<a href='$url' target='_blank' class='nounderline'>$url</a>";
  }
  echo "<td class='spis'>$url</td>";
  $ssd = dat2str($sdelano);
  echo "<td class='spis'>$ssd</td>";
  echo "</tr>\n";
}
echo "</tbody></table>\n";
$res->close();

// подключим редактирование полей
echo <<<_EOF
<!-- программа  -->
<script type="text/javascript" language="javascript">
$(document).ready(function(){
    // сортировка таблицы и т.д.
    $('#myTable1').DataTable( {
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
      }
      // не сортировать и не искать в 5 столбце (самый первый - 0) где столбцы с классом 'nosort'
      // https://datatables.net/reference/option/columnDefs.targets .
    } );

});
</script>

_EOF;
printEndPage();
