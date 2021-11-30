<section id="main">
    <?= $this->projectHeader->render($project, 'TaskGanttController', 'show', false, 'Gantt') ?>
    <div class="menu-inline">
        <ul>
            <li <?= $sorting === 'board' ? 'class="active"' : '' ?>>
                <?= $this->url->icon('sort-numeric-asc', t('Sort by position'), 'TaskGanttController', 'show', array('project_id' => $project['id'], 'sorting' => 'board', 'plugin' => 'Gantt')) ?>
            </li>
            <li <?= $sorting === 'date' ? 'class="active"' : '' ?>>
                <?= $this->url->icon('sort-amount-asc', t('Sort by date'), 'TaskGanttController', 'show', array('project_id' => $project['id'], 'sorting' => 'date', 'plugin' => 'Gantt')) ?>
            </li>
            <li>
                <?= $this->modal->large('plus', t('Add task'), 'TaskCreationController', 'show', array('project_id' => $project['id'])) ?>
            </li>
        </ul>
    </div>

    <?php if (! empty($tasks)): ?>
        <div
            id="gantt-chart"
            data-records='<?= json_encode($tasks, JSON_HEX_APOS) ?>'
            data-save-url="<?= $this->url->href('TaskGanttController', 'save', array('project_id' => $project['id'], 'plugin' => 'Gantt')) ?>"
            data-label-start-date="<?= t('Start date:') ?>"
            data-label-end-date="<?= t('Due date:') ?>"
            data-label-assignee="<?= t('Assignee:') ?>"
            data-label-not-defined="<?= t('There is no start date or due date for this task.') ?>"
        ></div>
		<button type='button' onclick='open_win_editar()' id='myBtn' class='btn btn-danger btn-sm'>Export PDF</button>
  <div id="demo"></div>
  <script language="javascript">
      function check_month(mese) {
          var cast = '';
          switch (mese) {
              case 1:
                  cast = 'Gennaio';
                  break
              case 2:
                  cast = 'Febbraio';
                  break
              case 3:
                  cast = 'Marzo';
                  break
              case 4:
                  cast = 'Aprile';
                  break
              case 5:
                  cast = 'Maggio';
                  break
              case 6:
                  cast = 'Giugno';
                  break
              case 7:
                  cast = 'Luglio';
                  break
              case 8:
                  cast = 'Agosto';
                  break
              case 9:
                  cast = 'Settembre';
                  break
              case 10:
                  cast = 'Ottobre';
                  break
              case 11:
                  cast = 'Novembre';
                  break
              case 12:
                  cast = 'Dicembre';
                  break;
          }
          return cast;
      }

      function checkRowMonthDay(text, firstDay, lastDay, firstMonth, lastMonth) {

          // console.log('checkRow', text, firstDay, lastDay, firstMonth, lastMonth)
          if (text.textContent.indexOf('Gen') !== -1)
              if (text.textContent === firstMonth)
                  return (31 - firstDay + 1);
              else if (text.textContent === lastMonth)
                  if (lastDay === 31)
                      return 31;
                  else
                      return lastDay;
              else
                  return 31;
          if (text.textContent.indexOf('Feb') !== -1){//controllo se l'anno è bisestile
			  var tmp = text.textContent;
var bisestile = tmp.substr(tmp.length - 4);
              if (bisestile % 4 === 0) {
                  if (bisestile % 100 === 0) {
                      if (bisestile % 400 === 0) {
                          if (text.textContent === firstMonth)
                              return (29 - firstDay + 1);
                          else if (text.textContent === lastMonth)
                              if (lastDay === 29)
                                  return 29;
                              else
                                  return lastDay;
                          else
                              return 29;
                      } else {
                          if (text.textContent === firstMonth)
                              return (28 - firstDay + 1);
                          else if (text.textContent === lastMonth)
                              if (lastDay === 28)
                                  return 28;
                              else
                                  return lastDay;
                          else
                              return 28;
                      }
                  } else {
                      if (text.textContent === firstMonth)
                          return (29 - firstDay + 1);
                      else if (text.textContent === lastMonth)
                          if (lastDay === 28)
                              return 28;
                          else
                              return lastDay;
                      else
                          return 29;
                  }
              } else {
                  if (text.textContent === firstMonth)
                      return 28 - firstDay + 1;
                  else if (text.textContent === lastMonth)
                      if (lastDay === 28)
                          return 28;
                      else
                          return lastDay;
                  else
                      return 28;
              }
		  }
          if (text.textContent.indexOf('Mar') !== -1)
              if (text.textContent === firstMonth)
                  return (31 - firstDay + 1);
              else if (text.textContent === lastMonth)
                  if (lastDay === 31)
                      return 31;
                  else
                      return lastDay;
              else
                  return 31;
          if (text.textContent.indexOf('Apr') !== -1)
              if (text.textContent === firstMonth)
                  return (30 - firstDay + 1);
              else if (text.textContent === lastMonth)
                  if (lastDay === 30)
                      return 30;
                  else
                      return lastDay;
              else
                  return 30;
          if (text.textContent.indexOf('Mag') !== -1)
              if (text.textContent === firstMonth)
                  return (31 - firstDay + 1);
              else if (text.textContent === lastMonth)
                  if (lastDay === 31)
                      return 31;
                  else
                      return lastDay;
              else
                  return 31;
          if (text.textContent.indexOf('Giu') !== -1)
              if (text.textContent === firstMonth)
                  return (30 - firstDay + 1);
              else if (text.textContent === lastMonth)
                  if (lastDay === 30)
                      return 30;
                  else
                      return lastDay;
              else
                  return 30;
          if (text.textContent.indexOf('Lug') !== -1)
              if (text.textContent === firstMonth)
                  return (31 - firstDay + 1);
              else if (text.textContent === lastMonth)
                  if (lastDay === 31)
                      return 31;
                  else
                      return lastDay;
              else
                  return 31;
          if (text.textContent.indexOf('Ago') !== -1)
              if (text.textContent === firstMonth)
                  return (31 - firstDay + 1);
              else if (text.textContent === lastMonth)
                  if (lastDay === 31)
                      return 31;
                  else
                      return lastDay;
              else
                  return 31;
          if (text.textContent.indexOf('Set') !== -1)
              if (text.textContent === firstMonth)
                  return (30 - firstDay + 1);
              else if (text.textContent === lastMonth)
                  if (lastDay === 30)
                      return 30;
                  else
                      return lastDay;
              else
                  return 30;
          if (text.textContent.indexOf('Ott') !== -1)
              if (text.textContent === firstMonth)
                  return (31 - firstDay + 1);
              else if (text.textContent === lastMonth)
                  if (lastDay === 31)
                      return 31;
                  else
                      return lastDay;
              else
                  return 31;
          if (text.textContent.indexOf('Nov') !== -1)
              if (text.textContent === firstMonth)
                  return (30 - firstDay + 1);
              else if (text.textContent === lastMonth)
                  return lastDay;
              else
                  return 30;
          if (text.textContent.indexOf('Dic') !== -1)
              if (text.textContent === firstMonth)
                  return (31 - firstDay + 1);
              else if (text.textContent === lastMonth)
                  if (lastDay === 31)
                      return 31;
                  else
                      return lastDay;
              else
                  return 31;
      }

      function render_month(text) {
          if (text.indexOf('Gen') !== -1)
              return 1
          if (text.indexOf('Feb') !== -1)
              return 2
          if (text.indexOf('Mar') !== -1)
              return 3
          if (text.indexOf('Apr') !== -1)
              return 4
          if (text.indexOf('Mag') !== -1)
              return 5
          if (text.indexOf('Giu') !== -1)
              return 6
          if (text.indexOf('Lug') !== -1)
              return 7
          if (text.indexOf('Ago') !== -1)
              return 8
          if (text.indexOf('Set') !== -1)
              return 9
          if (text.indexOf('Ott') !== -1)
              return 10
          if (text.indexOf('Nov') !== -1)
              return 11
          if (text.indexOf('Dic') !== -1)
              return 12;
      }

      function open_win_editar() {
          console.log(<?= json_encode($tasks, JSON_HEX_APOS) ?>);
          var table = document.createElement('table');
          var tbody = document.createElement('tbody');
          var chart = document.querySelector('#gantt-chart');
          var vtheader = chart.querySelector('.ganttview-vtheader');
          var slidetheader = chart.querySelector('.ganttview-slide-container');
          var firstDay;
          var lastDay;
          var body = document.getElementById("demo");
          slidetheader.querySelectorAll('.ganttview-hzheader-days').forEach(ghds => {//recupero i giorni per sistemare lo span th delle colonne
              firstDay = ghds.firstChild.textContent;
              lastDay = ghds.lastChild.textContent;
          })

          slidetheader.querySelectorAll('.ganttview-hzheader-months').forEach(ghms => {

                  const firstMonth = ghms.firstChild.textContent;
                  const lastMonth = ghms.lastChild.textContent;

                  ghms.querySelectorAll('.ganttview-hzheader-month').forEach(ghm => {
                      vtheader.querySelectorAll('.ganttview-vtheader-series').forEach(gvs => {
                          var bool = false;
                          var cellMonthtext = document.createTextNode(ghm.textContent);
                          var row = document.createElement('tr');
                          var th = document.createElement('th');
                          th.appendChild(cellMonthtext)
                          th.style.backgroundColor = 'rgb(186 186 186)'
                          th.style.breakAfter = 'always'
                          row.appendChild(th)
                          slidetheader.querySelectorAll('.ganttview-hzheader-days').forEach(ghds => {
                                  firstDay = ghds.firstChild.textContent;
                                  lastDay = ghds.lastChild.textContent;
                                  var k = 0;
                                  ghds.querySelectorAll('.ganttview-hzheader-day').forEach(ghd => {
                                      var check = checkRowMonthDay(cellMonthtext, firstDay, lastDay, firstMonth, lastMonth);
                                      var thghd = document.createElement("th");
                                      var text = document.createTextNode(ghd.textContent);
                                      var text2 = document.createTextNode(String(k + 1));
                                      if (cellMonthtext.textContent === firstMonth) {
                                          if (k < check) {
                                              thghd.appendChild(text);
                                              row.appendChild(thghd)
                                          }
                                          k++;
                                      } else if (cellMonthtext.textContent === lastMonth) {
                                          if (check === lastDay && k < check) {
                                              thghd.appendChild(text2);
                                              row.appendChild(thghd)
                                              k++;
                                          } else {
                                              if (Number(text.textContent) > 0 && text.textContent <= Number(lastDay) && k < (Number(lastDay))) {
                                                  thghd.appendChild(text);
                                                  row.appendChild(thghd)
                                                  k++;
                                              }
                                          }
                                      } else {
                                          if (Number(text.textContent) > 0 && text.textContent <= Number(check) && k < (Number(check))) {
                                              thghd.appendChild(document.createTextNode(String(k + 1)));
                                              row.appendChild(thghd)
                                              k++;
                                          }
                                      }
                                      if (Number(text.textContent) > 0 && Number(text.textContent) < 10) {
                                          thghd.style.padding = '10px'
                                      } else {
                                          thghd.style.padding = '5px'
                                      }
                                      thghd.style.backgroundColor = 'rgb(186 186 186)'
                                      thghd.style.breakAfter = 'always'
                                  })
                                  row.style.fontSize = '18px'
                                  i++;
                              }
                          )
                          tbody.appendChild(row)
                          var data_record = <?= json_encode($tasks, JSON_HEX_APOS) ?>;
                          var i = 0;
                          var month = '';
                          var thisMonth = '';
                          var gattgrid = chart.querySelector('.ganttview-grid');
                          gvs.querySelectorAll('.ganttview-vtheader-series-name').forEach(gvsn => {
                              var row = document.createElement('tr');
                              thisMonth = render_month(cellMonthtext.textContent)
                              month = check_month(data_record[i]['start'][1]);
                              gvsn.querySelectorAll('.markdown').forEach(gvsnspan => {//parte sinistra della tabella
                                  const cell = document.createElement('td');
                                  const cellText = document.createTextNode(gvsnspan.textContent.split(')').pop());
                                  cell.appendChild(cellText);
                                  row.appendChild(cell);
                                  cell.style.backgroundColor = 'rgb(229 229 229)'
                              });
                              const ganttgridrow = gattgrid.querySelector('.ganttview-grid-row');
                              let j = 0;
                              ganttgridrow.querySelectorAll('.ganttview-grid-row-cell').forEach(ganttgridrowcell => {
                                  const check = checkRowMonthDay(cellMonthtext, firstDay, lastDay, firstMonth, lastMonth);
                                  const ganttcell = document.createElement('td');
                                  const cellText = document.createTextNode('X');
                                  const cellText2 = document.createTextNode('5');
                                  const init = new Date(data_record[i]['start'][0], data_record[i]['start'][1], data_record[i]['start'][2]);
                                  const end = new Date(data_record[i]['end'][0], data_record[i]['end'][1], data_record[i]['end'][2]);
                                  const middle = new Date(data_record[i]['start'][0], thisMonth, j);
                                  const today = new Date();
                                  if (cellMonthtext.textContent === firstMonth) {
                                      if (j < check) {
                                          row.appendChild(ganttcell)
                                          if (init.getTime() > middle.getTime() && init.getTime() !== end.getTime() && check - j >= 0) {
                                              if (init.getDate() - Number(firstDay) <= j ) {
                                                  console.log(j)
                                                  ganttcell.appendChild(cellText)
                                              }
                                          }
                                      }
                                  } else if (cellMonthtext.textContent === lastMonth) {
                                      if (j < (Number(lastDay))) {
                                          row.appendChild(ganttcell)
                                      }
                                  } else {
                                      if (j < (Number(check))) {

                                          row.appendChild(ganttcell)
                                          if (init.getTime() < middle.getTime() && middle.getTime() < end.getTime())//intermezzo
                                              ganttcell.appendChild(cellText)

                                          if (init.getMonth() + 1 === middle.getMonth() + 1 && init.getDate() - 1 <= middle.getDate() && init.getTime() !== end.getTime())
                                              ganttcell.appendChild(cellText)
                                      }

                                  }
                                  ganttcell.style.backgroundColor = 'rgb(229 229 229)'
                                  if (init.getTime() === end.getTime() && init.getTime() > today.getTime()) {
                                      if (j === end.getDate() && thisMonth === today.getMonth() + 1) {
                                          ganttcell.appendChild(cellText)
                                      }
                                  } else if (middle.getTime() < end.getTime() && middle.getTime() > init.getTime()) {
                                      ganttcell.appendChild(cellText)
                                  } else if (init.getTime() < end.getTime() && init.getTime() > today.getTime()) {
                                      if (init.getTime() < middle.getTime())
                                          ganttcell.appendChild(cellText)
                                  }
                                  j++;
                              })
                              i++
                              tbody.appendChild(row);
                          })
                      })
                  })
              }
          );
          // row.style.pageBreakAfter= 'always'
          // row.style.pageBreakInside= 'avoid'
          // row.style.pageBreakBefore= 'avoid'
          table.appendChild(tbody);
          table.style.color = 'black'
          console.log(table)
          body.appendChild(table)
          var divToPrint = document.getElementById('demo');
          console.log(divToPrint)
          var title = document.querySelector('.title').textContent;
          console.log(title)
          var popupWin = window.open(title, '_blank', 'width=1000, height=1000');
          popupWin.document.write('<html><title>' + title + '</title><body onload="document.title; window.print()">' + divToPrint.innerHTML + '</html>');
          popupWin.document.close();
          body.removeChild(table);
      }

  </script>

        <p class="alert alert-info"><?= t('Moving or resizing a task will change the start and due date of the task.') ?></p>
    <?php else: ?>
        <p class="alert"><?= t('There is no task in your project.') ?></p>
    <?php endif ?>
</section>
