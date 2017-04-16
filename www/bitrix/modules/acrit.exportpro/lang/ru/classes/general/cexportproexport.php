<?
$MESS["ACRIT_EXPORTPRO_PROCESS_RUN"] = 'Процесс экспорт уже запущен. Если блокировка возникла из-за некорретного завершения предыдущего запуска, нажмите <a class="adm-btn adm-btn-save" href="'.$GLOBALS["APPLICATION"]->GetCurPageParam( "unlock=Y", array( "unlock" ) ).'">"Разблокировать и запустить"</a>';
$MESS["ACRIT_EXOPRTPRO_PROCESS_RUN_SUBJECT"] = "Ошибка \"Экспорт на торговые порталы\" экспорт уже запущен";
$MESS["ACRIT_EXOPRTPRO_PROCESS_RUN_ERROE_MESSAGE"] = 'Выгрузка ID: #PROFILE_ID#("#PROFILE_NAME#"). Процесс экспорт уже запущен. 
Если блокировка возникла в результате некорретного завершения предыдущего запуска, удалите блокировку вручную в настройках выгрузки на вкладке "Экспорт данных"';
$MESS["ACRIT_EXPORTPRO_PROCESS_CRON_BAD_EXPORTS"] = "С ".date( "d.m.Y", strtotime( "-1 week" ) )." по настоящий момент имеются незавершенные файлы экспорта по данной выгрузке";
?>