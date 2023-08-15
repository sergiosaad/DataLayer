<?php

require 'db_config.php';
require '../vendor/autoload.php';

require 'Models/Enterprise.php';

use Example\Models\Enterprise;

$now = date_format((new DateTimeImmutable()), 'Y-m-d H:i:s');

/*
 * MODEL
 */
print "\n-- model ----------------------------\n\n";
$model = new Enterprise();

/*
 * CREATE AND EDIT
 */

print "\n-- save ----------------------------\n\n";
$model->nm_enterprise = "New Enterprise - $now";
$model->ds_enterprise = "New Enterprise - $now";
$model->lk_enterprise_skin = "http://www.arqmedes.com";
$model->save();
$model->nm_edited = "saad@arqmedes.com";
$model->save();
print_r($model);

print "\n-- findById ----------------------------\n\n";

print "\n-- find ----------------------------\n\n";


