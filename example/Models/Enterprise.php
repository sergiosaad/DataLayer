<?php

namespace Example\Models;

use SergioSaad\DataLayer\DataLayer;

/**
 * Class Address
 * @package Example\Models
 */
class Enterprise extends DataLayer
{
    /**
     * Address constructor.
     */
    public function __construct()
    {
        $requiredFields =[
            "nm_enterprise",
        ];

        //field tp_enterprise, varchar(2), with the following domain: P:Privada | G:Governo | I:Indefinido 	, where the default is I.

        parent::__construct("tb_enterprise", $requiredFields, 'co_seq_enterprise',true);
    }
}

//Enterprise table example
  
// CREATE TABLE IF NOT EXISTS `tb_enterprise` (
//     `co_seq_enterprise` INT NOT NULL AUTO_INCREMENT,
//     `co_enterprise` INT NULL,
//     `nm_enterprise` VARCHAR(80) NOT NULL,
//     `ds_enterprise` VARCHAR(400) NULL,
//     `lk_enterprise_logo` VARCHAR(400) NULL,
//     `lk_enterprise_thumb` VARCHAR(400) NULL,
//     `lk_enterprise_skin` VARCHAR(400) NULL,
//     `dt_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
//     `nm_created` VARCHAR(80) NOT NULL DEFAULT 'automatic',
//     `dt_edited` DATETIME NULL DEFAULT NULL,
//     `nm_edited` VARCHAR(80) NULL DEFAULT NULL,
//     `st_record` INT(11) NOT NULL DEFAULT 1,
//     PRIMARY KEY (`co_seq_enterprise`),
//     INDEX `fk_rsk_tb_enterprise_rsk_tb_enterprise1_idx` (`co_enterprise` ASC),
//     CONSTRAINT `fk_rsk_tb_enterprise_rsk_tb_enterprise1`
//       FOREIGN KEY (`co_enterprise`)
//       REFERENCES `tb_enterprise` (`co_seq_enterprise`)
//       ON DELETE NO ACTION
//       ON UPDATE NO ACTION)
//   ENGINE = InnoDB
