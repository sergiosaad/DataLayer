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

print "\n-- Create and edit father using auditkeys -----------------\n\n";
$enterprise1->nm_enterprise = "father Enterprise - $now";
$enterprise1->ds_enterprise = "father Enterprise description - $now";
$enterprise1->lk_enterprise_skin = "http://www.arqmedes.com";
$enterprise1->save();
$enterprise1->nm_edited = "saad@arqmedes.com";
$enterprise1->tp_enterprise = "G";
$enterprise1->save();
print_r($enterprise1->data());

print "\n-- Create child ----------------------------\n\n";
$enterprise2->nm_enterprise = "Child Enterprise - $now";
$enterprise2->co_enterprise = $enterprise1->co_seq_enterprise;
$enterprise2->ds_enterprise = "Child Enterprise  description - $now";
$enterprise2->lk_enterprise_skin = "http://www.arqmedesconsultoria.com.br";
$enterprise2->save();

print "\n-- Child fetch parents ----------------------------\n\n";
$enterprise2->fetchParents();
print_r($enterprise2->data());
print_r($enterprise2->parents->enterprise->data());

print "\n-- Father Domain ----------------------------\n\n";
print_r($enterprise1);
print_r($enterprise1->domainValue->tp_enterprise."\n");
$enterprise1->tp_enterprise = "G";
print_r($enterprise1->domainValue->tp_enterprise."\n");

print "\n-- Father List Domain ----------------------------\n\n";
print_r($enterprise1->listDomain("tp_enterprise"));
foreach ($enterprise1->listDomain("tp_enterprise") as $option) {
    if($option->selected){
        print_r("selected = {$option->description}\n\n");
    }
}
$enterprise1->tp_enterprise="P";
print_r("id = {$enterprise1->tp_enterprise}\n\n");
print_r("selected = {$enterprise1->domainValue->tp_enterprise}\n\n");


