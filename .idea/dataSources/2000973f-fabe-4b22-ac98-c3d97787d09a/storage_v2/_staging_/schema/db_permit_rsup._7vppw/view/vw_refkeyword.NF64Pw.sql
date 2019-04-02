create OR REPLACE DEFINER=`root`@`localhost` view vw_refkeyword as
select `db_permit_rsup`.`tbl_refkeyword`.`idR_Keyword`  AS `idR_Keyword`,
       `db_permit_rsup`.`tbl_refkeyword`.`idKeyword`    AS `idKeyword`,
       `db_permit_rsup`.`tbl_mstkeyword`.`namaKeyword`  AS `namaKeyword`,
       `db_permit_rsup`.`tbl_refkeyword`.`idDokumen`    AS `idDokumen`,
       `db_permit_rsup`.`tbl_mstdokumen`.`judulDokumen` AS `judulDokumen`,
       `db_permit_rsup`.`tbl_refkeyword`.`createdBy`    AS `createdBy`,
       `db_permit_rsup`.`tbl_refkeyword`.`createdDate`  AS `createdDate`
from ((`db_permit_rsup`.`tbl_refkeyword` left join `db_permit_rsup`.`tbl_mstkeyword` on ((
    `db_permit_rsup`.`tbl_mstkeyword`.`idKeyword` = `db_permit_rsup`.`tbl_refkeyword`.`idKeyword`)))
       left join `db_permit_rsup`.`tbl_mstdokumen`
                 on ((`db_permit_rsup`.`tbl_mstdokumen`.`idDokumen` = `db_permit_rsup`.`tbl_refkeyword`.`idDokumen`)));

