<?php


namespace ToT;

require_once('../bootstrap.php');

$linker = new Linker;
header('Location: ' . $linker->urlPath() . 'redirectToIndex.php');
