<?php
/**
 * Created by PhpStorm.
 * User: shara
 * Date: 28.03.2016
 * Time: 23:45
 */
$connection = Bitrix\Main\Application::getConnection();
$connection->queryExecute('SET NAMES "utf8"');
$connection->queryExecute('SET collation_connection = "utf8_unicode_ci"');