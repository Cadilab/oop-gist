<?php

$starting = new DateTime();
date_add($starting, date_interval_create_from_date_string('1 months'));
echo $starting;

?>