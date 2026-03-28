<?php
require 'bootstrap/app.php';
$form = app('db')->table('forms')->first();
echo 'Form UUID: ' . $form->id . PHP_EOL;
echo 'Form Name: ' . $form->name . PHP_EOL;
?>