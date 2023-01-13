SET @@session.sql_mode = '';

DELETE FROM `oxshops`  WHERE oxname   = 'testsubshop';
DELETE FROM `oxconfig` WHERE oxshopid = 2;

DROP VIEW IF EXISTS `oxv_oxarticles_2`;
DROP VIEW IF EXISTS `oxv_oxattribute_2`;
DROP VIEW IF EXISTS `oxv_oxcategories_2`;
DROP VIEW IF EXISTS `oxv_oxdeliveryset_2`;
DROP VIEW IF EXISTS `oxv_oxdelivery_2`;
DROP VIEW IF EXISTS `oxv_oxdiscount_2`;
DROP VIEW IF EXISTS `oxv_oxlinks_2`;
DROP VIEW IF EXISTS `oxv_oxobject2category_2`;
DROP VIEW IF EXISTS `oxv_oxselectlist_2`;
DROP VIEW IF EXISTS `oxv_oxvendor_2`;
DROP VIEW IF EXISTS `oxv_oxmanufacturers_2`;
DROP VIEW IF EXISTS `oxv_oxvoucherseries_2`;
DROP VIEW IF EXISTS `oxv_oxwrapping_2`;
