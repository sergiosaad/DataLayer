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
$enterprise1 = new Enterprise();
$enterprise2 = new Enterprise();

/*
 * CREATE AND EDIT
 */

print "\n-- save ----------------------------\n\n";
$enterprise1->nm_enterprise = "New Enterprise - $now";
$enterprise1->ds_enterprise = "New Enterprise - $now";
$enterprise1->lk_enterprise_skin = "http://www.arqmedes.com";
$enterprise1->save();
$enterprise1->nm_edited = "saad@arqmedes.com";
$enterprise1->save();
print_r($enterprise1->data());

$enterprise2->nm_enterprise = "Enterprise child - $now";
$enterprise2->co_enterprise = $enterprise1->co_seq_enterprise;
$enterprise2->ds_enterprise = "Enterprise child description - $now";
$enterprise2->lk_enterprise_skin = "http://www.arqmedesconsultoria.com.br";
$enterprise2->save();
print_r($enterprise2->data());

print "\n-- findById ----------------------------\n\n";

print "\n-- find ----------------------------\n\n";


