<?php


require '../vendor/autoload.php';

use Pagination\Paginator;

$totalItems = 100;
$perPage = 3;
$currentPage = 1;

$pagination = new Paginator($totalItems, $perPage, $perPage, '');

$pagination->toJson();
?>





