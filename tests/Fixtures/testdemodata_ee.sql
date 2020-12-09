SET @@session.sql_mode = '';

# Product with inactive bundle
UPDATE `oxarticles` SET `OXMAPID` = 1118, `OXVPE` = 1 WHERE OXID='_test_active_main_bundle';
UPDATE `oxarticles` SET `OXMAPID` = 1119, `OXVPE` = 1 WHERE OXID='_test_inactive_bundle';
INSERT INTO `oxarticles2shop` (`OXSHOPID`, `OXMAPOBJECTID`, `OXTIMESTAMP`) VALUES
(1, 1118, '2020-01-01 00:00:00'),
(1, 1119, '2020-01-01 00:00:00');

REPLACE INTO oxvendor2shop (OXSHOPID, OXMAPOBJECTID) VALUES
(1, 902), (1, 903), (1, 904);

INSERT INTO oxconfig (OXID, OXSHOPID, OXVARNAME, OXVARTYPE, OXVARVALUE) SELECT
MD5(RAND()), 2, OXVARNAME, OXVARTYPE, OXVARVALUE from oxconfig;

REPLACE INTO oxselectlist2shop (OXSHOPID, OXMAPOBJECTID) VALUES
(1, 1);

INSERT INTO `oxlinks2shop` (`OXSHOPID`, `OXMAPOBJECTID`, `OXTIMESTAMP`) VALUES
(1,	902, '2020-04-16 16:41:05'),
(1,	903, '2020-04-16 16:41:05'),
(1,	904, '2020-04-16 16:41:05'),
(1,	905, '2020-04-16 16:41:05');

# Banner for second shop
INSERT INTO `oxactions` (`OXID`, `OXSHOPID`, `OXTYPE`, `OXSORT`, `OXACTIVE`, `OXTITLE`) VALUES
('_test_second_shop_banner_1', 2, 3, 2, 1, 'subshop banner 1'),
('_test_second_shop_banner_2', 2, 3, 1, 1, 'subshop banner 2');

#promotions
INSERT INTO `oxactions` (`OXID`, `OXSHOPID`, `OXTYPE`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXLONGDESC`, `OXLONGDESC_1`, `OXLONGDESC_2`, `OXLONGDESC_3`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXPIC`, `OXPIC_1`, `OXPIC_2`, `OXPIC_3`, `OXLINK`, `OXLINK_1`, `OXLINK_2`, `OXLINK_3`, `OXSORT`, `OXTIMESTAMP`) VALUES
('test_active_sub_shop_promotion_1',        2,	2,	'Current sub shop Promotion 1 DE',	'Current sub shop Promotion 1 EN',	'',	'',	'Long description 1 DE',	'Long description 1 EN',	'',	'',	1,	'2010-10-10 00:00:00',	'2111-10-10 00:00:00',	'',	'',	'',	'',	'',	'',	'',	'',	3,	'2020-04-23 12:07:10'),
('test_active_sub_shop_promotion_2',	    2,	2,	'Current sub shop Promotion 2 DE',	'Current sub shop Promotion 2 EN',	'',	'',	'Long description 2 DE',	'Long description 2 EN',	'',	'',	1,	'2010-01-01 00:00:00',	'2111-10-10 00:00:00',	'',	'',	'',	'',	'',	'',	'',	'',	2,	'2020-04-23 12:07:10'),
('test_inactive_sub_shop_promotion_1',	    2,	2,	'Upcoming sub shop promotion DE',	'Upcoming sub shop promotion EN',	'',	'',	'Long description 3 DE',	'Long description 3 EN',	'',	'',	0,	'2010-01-01 00:00:00',	'2010-02-01 00:00:00',	'',	'',	'',	'',	'',	'',	'',	'',	1,	'2020-04-23 12:07:10');

INSERT INTO `oxarticles2shop` (`OXSHOPID`, `OXMAPOBJECTID`) VALUES
(2, 933),
(2, 1088),
(2, 1094);

# Category for fast sorting
REPLACE INTO `oxcategories` (`OXID`, `OXMAPID`, `OXPARENTID`,   `OXLEFT`, `OXRIGHT`, `OXROOTID`,     `OXSORT`, `OXACTIVE`, `OXSHOPID`,   `OXTITLE`,                    `OXDESC`,                    `OXLONGDESC`,                `OXDEFSORT`, `OXDEFSORTMODE`, `OXPRICEFROM`, `OXPRICETO`, `OXACTIVE_1`, `OXTITLE_1`,                  `OXDESC_1`,                        `OXLONGDESC_1`,                    `OXVAT`, `OXSHOWSUFFIX`) VALUES
('e7d257920a5369cd8d7db52485491d54', 926, 'oxrootid',      1,        4,        'e7d257920a5369cd8d7db52485491d54', 1,        1,         2, 'Test category', 'Test category desc [DE]', 'Test category long desc', 'oxartnum',   0,               0,             0,           1,           'Test category [EN]', 'Test category desc [EN]', 'Test category long desc [EN]',  5,       1);

REPLACE INTO `oxobject2category` (`OXID`, `OXSHOPID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`) VALUES
('28819912f2c4febde1c3987de797635a',	2,	'd861ad687c60820255dbf8f88516f24d',	'e7d257920a5369cd8d7db52485491d54',	0,	0),
('85fc3e4814da77dcc3abab31163f52da',	2,	'd86f775338da3228bec9e968f02e7551',	'e7d257920a5369cd8d7db52485491d54',	0,	0),
('94d865bf075725341cafa4bd45941032',	2,	'd86236918e1533cccb679208628eda32',	'e7d257920a5369cd8d7db52485491d54',	0,	999999999);
SET @@session.sql_mode = '';

REPLACE INTO `oxpricealarm` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXEMAIL`, `OXARTID`, `OXPRICE`, `OXCURRENCY`, `OXLANG`, `OXINSERT`, `OXSENDED`, `OXTIMESTAMP`) VALUES
('_test_wished_price_without_user_',	1,	'',	'test-email@test.com',	'dc5ffdf380e15674b56dd562a7cb6aec',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'0000-00-00 00:00:00',	'2020-05-26 10:30:18'),
('_test_wished_price_1_',	1,	'e7af1c3b786fd02906ccd75698f4e6b9',	'user@oxid-esales.com',	'dc5ffdf380e15674b56dd562a7cb6aec',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'0000-00-00 00:00:00',	'2020-05-26 10:31:33'),
('_test_wished_price_2_',	1,	'245ad3b5380202966df6ff128e9eecaq',	'redaktion@redaktion.net',	'dc5ffdf380e15674b56dd562a7cb6aec',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'0000-00-00 00:00:00',	'2020-05-26 11:48:20'),
('_test_wished_price_3_',	1,	'e7af1c3b786fd02906ccd75698f4e6b9',	'user@oxid-esales.com',	'_test_product_wished_price_3_',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'0000-00-00 00:00:00',	'2020-05-26 10:31:33'),
('_test_wished_price_4_',	1,	'e7af1c3b786fd02906ccd75698f4e6b9',	'user@oxid-esales.com',	'_test_product_wished_price_4_',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'0000-00-00 00:00:00',	'2020-05-26 10:31:33'),
('_test_wished_price_5_',	1,	'e7af1c3b786fd02906ccd75698f4e6b9',	'user@oxid-esales.com',	'does_not_exist',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'0000-00-00 00:00:00',	'2020-05-26 10:31:33'),
('_test_wished_price_6_',	1,	'e7af1c3b786fd02906ccd75698f4e6b9',	'user@oxid-esales.com',	'dc5ffdf380e15674b56dd562a7cb6aec',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'2020-05-31 10:31:33',	'2020-05-26 10:31:33'),
('_test_wished_price_7_',	1,	'non-existing-user-id',	'user@oxid-esales.com',	'dc5ffdf380e15674b56dd562a7cb6aec',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'2020-05-31 10:31:33',	'2020-05-26 10:31:33'),
('_test_wished_price_delete_',	1,	'e7af1c3b786fd02906ccd75698f4e6b9',	'user@oxid-esales.com',	'dc5ffdf380e15674b56dd562a7cb6aec',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'2020-05-31 10:31:33',	'2020-05-26 10:31:33'),
('_test_wished_price_delete_1_',	1,	'e7af1c3b786fd02906ccd75698f4e6b9',	'user@oxid-esales.com',	'dc5ffdf380e15674b56dd562a7cb6aec',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'2020-05-31 10:31:33',	'2020-05-26 10:31:33'),
('_test_wished_price_delete_2_',	1,	'e7af1c3b786fd02906ccd75698f4e6b9',	'user@oxid-esales.com',	'dc5ffdf380e15674b56dd562a7cb6aec',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'2020-05-31 10:31:33',	'2020-05-26 10:31:33'),
('_test_wished_price_delete_3_',	1,	'e7af1c3b786fd02906ccd75698f4e6b9',	'user@oxid-esales.com',	'dc5ffdf380e15674b56dd562a7cb6aec',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'2020-05-31 10:31:33',	'2020-05-26 10:31:33');


REPLACE INTO `oxarticles` (`OXID`, `OXSHOPID`, `OXPARENTID`, `OXACTIVE`, `OXHIDDEN`, `OXACTIVEFROM`, `OXACTIVETO`, `OXARTNUM`, `OXEAN`, `OXDISTEAN`, `OXMPN`, `OXTITLE`, `OXSHORTDESC`, `OXPRICE`, `OXBLFIXEDPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXBPRICE`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXEXTURL`, `OXURLDESC`, `OXURLIMG`, `OXVAT`, `OXTHUMB`, `OXICON`, `OXPIC1`, `OXPIC2`, `OXPIC3`, `OXPIC4`, `OXPIC5`, `OXPIC6`, `OXPIC7`, `OXPIC8`, `OXPIC9`, `OXPIC10`, `OXPIC11`, `OXPIC12`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`, `OXDELIVERY`, `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXFILE`, `OXSEARCHKEYS`, `OXTEMPLATE`, `OXQUESTIONEMAIL`, `OXISSEARCH`, `OXISCONFIGURABLE`, `OXVARNAME`, `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`, `OXVARMINPRICE`, `OXVARMAXPRICE`, `OXVARNAME_1`, `OXVARSELECT_1`, `OXVARNAME_2`, `OXVARSELECT_2`, `OXVARNAME_3`, `OXVARSELECT_3`, `OXTITLE_1`, `OXSHORTDESC_1`, `OXURLDESC_1`, `OXSEARCHKEYS_1`, `OXTITLE_2`, `OXSHORTDESC_2`, `OXURLDESC_2`, `OXSEARCHKEYS_2`, `OXTITLE_3`, `OXSHORTDESC_3`, `OXURLDESC_3`, `OXSEARCHKEYS_3`, `OXBUNDLEID`, `OXFOLDER`, `OXSUBCLASS`, `OXSTOCKTEXT_1`, `OXSTOCKTEXT_2`, `OXSTOCKTEXT_3`, `OXNOSTOCKTEXT_1`, `OXNOSTOCKTEXT_2`, `OXNOSTOCKTEXT_3`, `OXSORT`, `OXSOLDAMOUNT`, `OXNONMATERIAL`, `OXFREESHIPPING`, `OXREMINDACTIVE`, `OXREMINDAMOUNT`, `OXAMITEMID`, `OXAMTASKID`, `OXVENDORID`, `OXMANUFACTURERID`, `OXSKIPDISCOUNTS`, `OXRATING`, `OXRATINGCNT`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`, `OXUPDATEPRICE`, `OXUPDATEPRICEA`, `OXUPDATEPRICEB`, `OXUPDATEPRICEC`, `OXUPDATEPRICETIME`, `OXISDOWNLOADABLE`) VALUES
('_test_product_wished_price_3_',	1,	'',	1,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'333',	'',	'',	'',	'Product 3',	'',	10,	1,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:25:26',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	10,	10,	'',	'',	'',	'',	'',	'',	'Product 1',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0),
('_test_product_wished_price_4_',	1,	'',	0,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'444',	'',	'',	'',	'Product 4',	'',	10,	0,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:25:26',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	10,	10,	'',	'',	'',	'',	'',	'',	'Product 1',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0),
('_test_product_for_rating_5_',	1,	'',	1,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'555',	'',	'',	'',	'Product 5',	'',	10,	0,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:25:26',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	10,	10,	'',	'',	'',	'',	'',	'',	'Product 1',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0),
('_test_product_for_rating_6_',	1,	'',	1,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'666',	'',	'',	'',	'Product 6',	'',	10,	0,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:25:26',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	10,	10,	'',	'',	'',	'',	'',	'',	'Product 1',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0),
('_test_product_for_rating_avg',	1,	'',	1,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'987',	'',	'',	'',	'Product 987',	'',	10,	0,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:25:26',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	10,	10,	'',	'',	'',	'',	'',	'',	'Product 1',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0),
('_test_product_for_wish_list',	1,	'',	1,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'645',	'',	'',	'',	'Product 645',	'',	10,	0,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:25:26',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	10,	10,	'',	'',	'',	'',	'',	'',	'Product 1',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0),
('_test_product_for_basket',	1,	'',	1,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'621',	'',	'',	'',	'Product 621',	'',	10,	0,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:25:26',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	10,	10,	'',	'',	'',	'',	'',	'',	'Product 1',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0);

REPLACE INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`, `OXCUSTNR`, `OXUSTID`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`, `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXUPDATEKEY`, `OXUPDATEEXP`, `OXPOINTS`) VALUES
('245ad3b5380202966df6ff128e9eecaq', 1, 'user', 1, 'otheruser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0),
('e7af1c3b786fd02906ccd75698f4e6b9', 1, 'user', 1, 'user@oxid-esales.com', '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 2, '', '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-21', '', '', 0, 0),
('_45ad3b5380202966df6ff128e9eecaq', 1, 'user', 1, 'differentuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0),
('_678d3b5380202966df6ff128e9eecaq', 1, 'user', 1, 'exampleuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0),
('9119cc8cd9593c214be93ee558235f3c', 1, 'user', 1, 'existinguser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', '', 'Eleanor', 'Rodriguez', 'Jerry Dove Drive', '1118', '', 'Tuscon', '8f241f11096877ac0.98748826', 'AZ', '85713', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0),
('9119cc8cd9593c214be93ee558235f3x', 1, 'user', 1, 'foremailchange@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', '', 'Eleanor', 'Rodriguez', 'Jerry Dove Drive', '1118', '', 'Tuscon', '8f241f11096877ac0.98748826', 'AZ', '85713', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0),
('9119cc8cd9593c214be93ee558235g5x', 1, 'user', 1, 'foremailchangeCE@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', '', 'Eleanor', 'Rodriguez', 'Jerry Dove Drive', '1118', '', 'Tuscon', '8f241f11096877ac0.98748826', 'AZ', '85713', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0),
('309db395b6c85c3881fcb9b437a73dd7', 1, 'user', 1, 'tempuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef'    , 8, '', '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0),
('309db395b6c85c3881fcb9b437a73ff5', 1, 'user', 1, 'deletebytest@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef'    , 8, '', '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0),
('309db395b6c85c3881fcb9b437a73cc8', 1, 'user', 1, 'tobedeleted@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef'    , 8, '', '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0),
('245ad3b5380202966df6ff12dodo9caq', 1, 'user', 1, 'dodo@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef'    , 8, '', '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0);

REPLACE INTO `oxratings` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXTYPE`, `OXOBJECTID`, `OXRATING`) VALUES
('test_user_rating', 1, '245ad3b5380202966df6ff128e9eecaq', 'oxarticle', '_test_product_for_rating_avg', 3),
('test_rating_1_', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'oxarticle', '_test_product_for_rating_avg', 1);

UPDATE `oxarticles` SET `OXRATING` = '2', `OXRATINGCNT` = '2' WHERE oxid = '_test_product_for_rating_avg';

UPDATE `oxnewssubscribed` SET `OXDBOPTIN` = '1', `OXSUBSCRIBED` = '2020-04-01 11:11:11', `OXUNSUBSCRIBED` = '0000-00-00 00:00:00' WHERE `OXUSERID` = 'e7af1c3b786fd02906ccd75698f4e6b9';

UPDATE `oxnewssubscribed` SET `OXDBOPTIN` = 1 where `OXUSERID` = 'e7af1c3b786fd02906ccd75698f4e6b9';

REPLACE INTO `oxobject2group` (`OXID`, `OXSHOPID`, `OXOBJECTID`, `OXGROUPSID`, `OXTIMESTAMP`) VALUES
('test_unsubscribe', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'oxidnewsletter', '2012-06-04 07:04:54');

REPLACE INTO `oxaddress` (`OXID`, `OXUSERID`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXCITY`, `OXCOUNTRY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXSAL`, `OXTIMESTAMP`) VALUES
('test_delivery_address',	'e7af1c3b786fd02906ccd75698f4e6b9',	'Marc',	'Muster',	'Hauptstr',	'13',	'Freiburg',	'Germany',	'a7c40f631fc920687.20179984', '',	'79098',	'MR',	'2020-07-14 14:12:48'),
('test_delivery_address_2',	'e7af1c3b786fd02906ccd75698f4e6b9',	'Marc',	'Muster',	'Hauptstr2',	'132',	'Freiburg',	'Austria',	'a7c40f6320aeb2ec2.72885259', '',	'79098',	'MR',	'2020-07-14 14:44:06'),
('test_delivery_address_us', '9119cc8cd9593c214be93ee558235f3c', 'Eleanor', 'Rodriguez', 'Cook Hill Road', '4631', 'Apalachin', 'United States', '8f241f11096877ac0.98748826', 'AZ', '13732', 'MR',	'2020-07-14 14:44:06');

REPLACE INTO `oxuserbaskets` (`OXID`, `OXUSERID`, `OXTITLE`, `OXPUBLIC`) VALUES
('_test_wish_list_public', 'e7af1c3b786fd02906ccd75698f4e6b9', 'wishlist', true),
('test_make_wishlist_private',	'e7af1c3b786fd02906ccd75698f4e6b9',	'wishlist',	true),
('_test_basket_public', 'e7af1c3b786fd02906ccd75698f4e6b9', 'buy_these', true),
('_test_wish_list_private', '245ad3b5380202966df6ff128e9eecaq', 'wishlist', false),
('_test_basket_private', '245ad3b5380202966df6ff128e9eecaq', 'buy_these_later', false),
('_test_basket_private_ex', '309db395b6c85c3881fcb9b437a73dd6', 'buy_these_later', false),
('_test_noticelist_public', '_678d3b5380202966df6ff128e9eecaq', 'noticelist', true),
('_test_savedbasket_public', 'e7af1c3b786fd02906ccd75698f4e6b9', 'savedbasket', true),
('_test_savedbasket_private', 'e7af1c3b786fd02906ccd75698f4e6b9', 'savedbasket_private', false),
('_test_voucher_public', 'e7af1c3b786fd02906ccd75698f4e6b9', 'test_voucher', true);


REPLACE INTO `oxuserbasketitems` (`OXID`, `OXBASKETID`, `OXARTID`, `OXAMOUNT`, `OXSELLIST`, `OXPERSPARAM`) VALUES
('_test_wish_list_item_1', '_test_wish_list_public', '_test_product_for_wish_list', 1, 'N;', ''),
('_test_wish_list_item_2', '_test_wish_list_private', '_test_product_for_wish_list', 1, 'N;', ''),
('_test_basket_item_1', '_test_basket_public', '_test_product_for_basket', 1, 'N;', ''),
('_test_basket_item_2', '_test_basket_private', '_test_product_for_basket', 1, 'N;', ''),
('_test_voucherbasket_item_1', '_test_voucher_public', '_test_product_for_basket', 1, 'N;', '');

UPDATE `oxcountry` SET `oxorder` = 1 where `OXID` = 'a7c40f631fc920687.20179984';
UPDATE `oxcountry` SET `oxorder` = 2 where `OXID` = '8f241f11096877ac0.98748826';
UPDATE `oxcountry` SET `oxorder` = 3 where `OXID` = 'a7c40f6321c6f6109.43859248';
UPDATE `oxcountry` SET `oxorder` = 4 where `OXID` = 'a7c40f6320aeb2ec2.72885259';
UPDATE `oxcountry` SET `oxorder` = 5 where `OXID` = 'a7c40f632a0804ab5.18804076';

REPLACE INTO `oxorder` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`, `OXORDERNR`, `OXBILLCOMPANY`, `OXBILLEMAIL`, `OXBILLFNAME`,
 `OXBILLLNAME`, `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLADDINFO`, `OXBILLCITY`,
  `OXBILLCOUNTRYID`, `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLFON`, `OXBILLFAX`, `OXBILLSAL`, `OXDELCOMPANY`, `OXDELFNAME`,
  `OXDELLNAME`, `OXDELSTREET`, `OXDELSTREETNR`, `OXDELADDINFO`, `OXDELCITY`, `OXDELCOUNTRYID`, `OXDELSTATEID`, `OXDELZIP`,
   `OXDELFON`, `OXDELFAX`, `OXDELSAL`, `OXPAYMENTID`, `OXPAYMENTTYPE`, `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`,
   `OXARTVAT1`, `OXARTVATPRICE1`, `OXARTVAT2`, `OXARTVATPRICE2`, `OXDELCOST`, `OXDELVAT`, `OXPAYCOST`, `OXPAYVAT`, `OXWRAPCOST`,
   `OXWRAPVAT`, `OXGIFTCARDCOST`, `OXGIFTCARDVAT`, `OXCARDID`, `OXCARDTEXT`, `OXDISCOUNT`, `OXEXPORT`, `OXBILLNR`, `OXBILLDATE`,
    `OXTRACKCODE`, `OXSENDDATE`, `OXREMARK`, `OXVOUCHERDISCOUNT`, `OXCURRENCY`, `OXCURRATE`, `OXFOLDER`, `OXTRANSID`, `OXPAYID`,
    `OXXID`, `OXPAID`, `OXSTORNO`, `OXIP`, `OXTRANSSTATUS`, `OXLANG`, `OXINVOICENR`, `OXDELTYPE`, `OXTIMESTAMP`,
    `OXISNETTOMODE`) VALUES
('7d090db46a124f48cb7e6836ceef3f66',1,'e7af1c3b786fd02906ccd75698f4e6b9','2011-03-30 10:55:13',1,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','7d011a153655ef215558cddd43dc65a8','oxidinvoice',1639.15,2108.39,1950.59,19,311.44,0,0,0,19,0,0,0,0,0,0,'','',157.8,0,'7661','2020-03-31','track_me','2020-08-24 11:11:11','Hier können Sie uns noch etwas mitteilen.',0,'EUR',1,'ORDERFOLDER_NEW','','','','2020-04-01 12:12:12',0,'','OK',0,661,'oxidstandard','2020-08-21 09:39:46',0),
('8c69bc776dd339a83d863c4f64693bb6',1,'e7af1c3b786fd02906ccd75698f4e6b9','2019-08-21 11:41:41',2,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-21 09:41:41',0),
('0c99bad495d00254a936ccee2391f763',1,'e7af1c3b786fd02906ccd75698f4e6b9','2020-04-22 14:07:12',3,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','del company','Marcia','Pattern','Nebenstraße','123','del addinfo','Freiburg','a7c40f631fc920687.20179984','HH','79106','04012345678','04012345679','MRS','c3260a603ed4e2d3b01981cbc05e8dfd','oxidinvoice',226.05,269,269,19,42.95,0,0,0,19,0,0,0,0,0,19,'','',0,0,'7663','2020-08-24','trick','2020-08-24 11:11:13','Hej, greetings to graphQL! ',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,663,'oxidstandard','2020-08-21 12:07:12',0),
('8c726d3f42ff1a6ea2828d5f309de881',1,'e7af1c3b786fd02906ccd75698f4e6b9','2020-05-23 14:08:55',4,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','del company','Marcia','Pattern','Nebenstraße','123','del addinfo','Freiburg','a7c40f631fc920687.20179984','HH','79106','04012345678','04012345679','MRS','direct_debit_order_payment','oxiddebitnote',35.62,42.39,46.29,19,6.77,0,0,3.9,19,0,0,0,0,0,19,'','',0,0,'7664','2020-08-24','track','2020-08-24 11:11:14','please deliver as fast as you can',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,664,'oxidstandard','2020-08-21 12:08:55',0),
('_019bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-01 11:41:41',100,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-01 09:41:41',0),
('_029bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-02 11:41:41',101,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-02 09:41:41',0),
('_039bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-03 11:41:41',102,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-03 09:41:41',0),
('_049bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-04 11:41:41',103,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-04 09:41:41',0),
('_059bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-05 11:41:41',104,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-05 09:41:41',0),
('_069bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-06 11:41:41',105,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-06 09:41:41',0),
('_079bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-07 11:41:41',106,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-07 09:41:41',0),
('_089bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-08 11:41:41',107,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-08 09:41:41',0),
('_099bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-09 11:41:41',108,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-09 09:41:41',0),
('_109bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-10 11:41:41',109,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-10 09:41:41',0),
('_119bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-11 11:41:41',110,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-11 09:41:41',0),
('_129bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-12 11:41:41',111,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-12 09:41:41',0),
('_139bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-13 11:41:41',112,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-13 09:41:41',0),
('_149bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-14 11:41:41',113,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-14 09:41:41',0),
('7f0b6ef39c4e76c04a0f75232489bb65',1,'245ad3b5380202966df6ff128e9eecaq','2020-08-28 11:04:14',200,'','user@oxid-esales.com','Marc','Muster','Hauptstr.','13','','Freiburg','a7c40f631fc920687.20179984','','79098','','','MR','','','','','','','','','','','','','','8ebefe11f18f4e6457d01ca9785d2c98','oxidcashondel',178.3,209.38,220.78,10,2.72,19,27.38,3.9,19,7.5,19,0,0,0,19,'','',123.4,0,'','2020-09-01','tracking_code','2020-09-02 12:12:12','',0.0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,0,'oxidstandard','2020-08-28 09:04:14',0),
('8g0b6ef39c4e76c04a0f75232489bb65',1,'_678d3b5380202966df6ff128e9eecaq','2020-08-28 11:04:14',201,'','user@oxid-esales.com','Marc','Muster','Hauptstr.','13','','Freiburg','a7c40f631fc920687.20179984','','79098','','','MR','','','','','','','','','','','','','','8ebefe11f18f4e6457d01ca9785d2c98','oxidcashondel',178.3,209.38,220.78,10,2.72,19,27.38,3.9,19,7.5,19,0,0,0,19,'','',0,0,'','0000-00-00 00:00:00','','0000-00-00 00:00:00','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,0,'unknownshipping','2020-08-28 09:04:14',0),
('_order_with_non_existing_product',1,'245ad3b5380202966df6ff128e9eecaq','2019-08-14 11:41:41',113,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-14 09:41:41',0),
('_order_with_deleted_product',1,'245ad3b5380202966df6ff128e9eecaq','2019-08-14 11:41:41',113,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard','2020-08-14 09:41:41',0);

REPLACE INTO `oxorderarticles` (`OXID`, `OXORDERID`, `OXAMOUNT`, `OXARTID`, `OXARTNUM`, `OXTITLE`, `OXSHORTDESC`, `OXSELVARIANT`, `OXNETPRICE`, `OXBRUTPRICE`, `OXVATPRICE`, `OXVAT`, `OXPERSPARAM`, `OXPRICE`, `OXBPRICE`, `OXNPRICE`, `OXWRAPID`, `OXEXTURL`, `OXURLDESC`, `OXURLIMG`, `OXTHUMB`, `OXPIC1`, `OXPIC2`, `OXPIC3`, `OXPIC4`, `OXPIC5`, `OXWEIGHT`, `OXSTOCK`, `OXDELIVERY`, `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXFILE`, `OXSEARCHKEYS`, `OXTEMPLATE`, `OXQUESTIONEMAIL`, `OXISSEARCH`, `OXFOLDER`, `OXSUBCLASS`, `OXSTORNO`, `OXORDERSHOPID`, `OXISBUNDLE`) VALUES
('1d48d8339e49f906700b520ceb4c79aa','7f0b6ef39c4e76c04a0f75232489bb65',1,'05848170643ab0deb9914566391c0c63','1402','Harness MADTRIXX','New freestyle harness with a lean cut outline','',133.61,159,25.39,19,'',159,159,133.61,'','','','','','ion_madtrixx_kite_waist_2011_1.jpg','','','','',0,15,'0000-00-00','2010-12-06','2020-08-28 09:04:14',0,0,0,'','harness, hip harness, madtrixx','','',1,'','oxarticle',0,1,0),
('6c220c5d926c5092035234285f3c8fc3','7f0b6ef39c4e76c04a0f75232489bb65',1,'058c7b525aad619d8b343c0ffada0247','400-03','Sticky Tape DACRON KITEFIX','ideal for small repairs of the kite','black',7.01,7.99,0.98,14,'',7.99,7.99,7.01,'','','','','','kitefix_self-adhesive_dacron_1.jpg','','','','',0,5,'0000-00-00','2010-12-06','2020-08-28 09:04:14',0,0,0,'','sticky tape, kite, repair, kitefix','','',0,'','oxarticle',0,1,0),
('b4b9f8dd0be567d6fcfc59d9b7bb266b','7f0b6ef39c4e76c04a0f75232489bb65',1,'dc5ffdf380e15674b56dd562a7cb6aec','3503','Kuyichi leather belt JEVER','Leather belt, unisex','',27.18,29.9,2.72,10,'',29.9,29.9,27.18,'','','','','','p1170221_1.jpg','p1170222_1.jpg','','','',0,15,'0000-00-00','2010-12-10','2020-08-28 09:04:14',0,0,0,'','kuyichi, leather, leather belt, unisex, used','','',1,'','oxarticle',0,1,0),
('f805daf76a1f8614a7972ab51c22634b','7f0b6ef39c4e76c04a0f75232489bb65',1,'f33d5bcc7135908fd36fc736c643aa1c','1506','KiteFix Glue GLUFIX (30g)','Specially developed for fixing kites','',10.5,12.49,1.99,19,'',12.49,12.49,10.5,'','','','','glufix_z1a_th_th.jpg','glufix_z1a.jpg','','','','',0,27,'0000-00-00','2011-03-24','2020-08-28 09:04:14',0,0,0,'','kite, kitefix, glue, glufix','','',1,'','oxarticle',0,1,0),
('677688370a4a64d8336107bcf174f330','_order_with_non_existing_product',1,'non_existing_product_id','621','Product 1','','',8.4,10,1.6,19,'',10,10,8.4,'','','','','','','','','','',0,0,'0000-00-00','2020-05-25','2015-07-02 07:31:37',0,0,0,'','','','',1,'','oxarticle',0,2,0),
('677688370a4a64d8336107bcf174f331','_order_with_deleted_product',1,'_test_product_for_basket','621','Product 1','','',8.4,10,1.6,19,'',10,10,8.4,'','','','','','','','','','',0,0,'0000-00-00','2020-05-25','2015-07-02 07:31:37',0,0,0,'','','','',1,'','oxarticle',0,2,0),
('c5b7fd8dff99f066c168cd720212075a','8c726d3f42ff1a6ea2828d5f309de881',1,'oiaa81b5e002fc2f73b9398c361c0b97','10101','Online shops with OXID eShop','','',0,0,0,10,'',0,0,0,'','','','','','oxid_book_cover_1.jpg','','','','',0,600,'0000-00-00','2012-04-25','2020-09-10 09:13:36',0,0,0,'','','','',1,'','',0,1,0),
('4ad5c368c9c7715ac800adb27e079ebe','7f0b6ef39c4e76c04a0f75232489bb65',1,'oiaa81b5e002fc2f73b9398c361c0b97','10101','Online shops with OXID eShop','','',0,0,0,10,'',0,0,0,'','','','','','oxid_book_cover_1.jpg','','','','',0,600,'0000-00-00','2012-04-25','2020-09-10 09:13:36',0,0,0,'','','','',1,'','',0,1,0);

REPLACE INTO `oxvoucherseries` (`OXID`, `OXSHOPID`, `OXSERIENR`, `OXDISCOUNT`, `OXDISCOUNTTYPE`, `OXBEGINDATE`, `OXENDDATE`, `OXSERIEDESCRIPTION`, `OXALLOWOTHERSERIES`) VALUES
('voucherserie1', 1, 'voucherserie1', 21.6, 'absolute', '2000-01-01', '2050-12-31', '', 1),
('serie2', 1, 'serie2', 2.0, 'absolute', '2000-01-01', '2050-12-31', 'serie2 description', 1),
('serie3', 1, 'serie3', 3.0, 'absolute', '2000-01-01', '2050-12-31', 'serie3 description', 1),
('personal_voucher', 1, 'myVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'personal voucher', 0),
('personal_series_voucher', 1, 'mySeriesVoucher', 6.0, 'absolute', '2000-01-01', '2050-12-31', 'personal voucher', 1),
('series_voucher', 1, 'seriesVoucher', 8.0, 'absolute', '2000-01-01', '2050-12-31', 'series voucher', 0),
('used_voucher', 1, 'used_voucher', 3.0, 'absolute', '2000-01-01', '2050-12-31', 'used voucher', 0),
('shop_2_voucher_series', 2, 'shop2voucher', 10.0, 'percent', '2000-01-01', '2050-12-31', 'shop 2 voucher', 0);

REPLACE INTO `oxvouchers` (`OXDATEUSED`, `OXORDERID`, `OXUSERID`, `OXRESERVED`, `OXVOUCHERNR`, `OXVOUCHERSERIEID`, `OXDISCOUNT`, `OXID`, `OXTIMESTAMP`, `OEGQL_BASKETID`) VALUES
('2020-08-28', '_149bc776dd339a83d863c4f64693bb6', '_45ad3b5380202966df6ff128e9eecaq', 1, 'voucher1', 'voucherserie1', 21.6, 'usedvoucherid', now(), null),
(null, null, null, 0, 'voucher2', 'voucherserie1', 0, 'notusedvoucherid', now(), null),
(null, null, null, 1601551714, 'serie2voucher', 'serie2', 0, 'serie2voucher', now(), '_test_basket_private'),
(null, null, null, 1601551714, 'serie3voucher', 'serie3', 0, 'serie3voucher', now(), '_test_basket_private'),
(null, null, null, 0, 'myVoucher', 'personal_voucher', 0, 'personal_voucher_1', now(), null),
(null, null, null, 0, 'myVoucher', 'personal_voucher', 0, 'personal_voucher_2', now(), null),
(null, null, null, 0, 'mySeriesVoucher', 'personal_series_voucher', 0, 'personal_series_voucher_1', now(), null),
(null, null, null, 0, 'mySeriesVoucher', 'personal_series_voucher', 0, 'personal_series_voucher_2', now(), null),
(null, null, null, 0, 'seriesVoucher', 'series_voucher', 0, 'series_voucher_1', now(), null),
('2020-10-10', '_test_order', 'e7af1c3b786fd02906ccd75698f4e6b9', 0, 'used_voucher', 'used_voucher', 0, 'used_voucher', now(), ''),
(null, null, null, 0, 'shop2voucher', 'shop_2_voucher_series', 0, 'shop_2_voucher_series', now(), '');

REPLACE INTO `oxuserpayments` (`OXID`, `OXUSERID`, `OXPAYMENTSID`, `OXVALUE`, `OXTIMESTAMP`) VALUES
('direct_debit_order_payment',  'e7af1c3b786fd02906ccd75698f4e6b9', 'oxiddebitnote', ENCODE('lsbankname__Pro Credit Bank@@lsblz__PRCBBGSF456@@lsktonr__DE89 3704 0044 0532 0130 00@@lsktoinhaber__Marc Muster@@', 'sd45DF09_sdlk09239DD'), '2020-09-10 08:15:00');

REPLACE INTO `oxorderfiles` (`OXID`, `OXORDERID`, `OXFILENAME`, `OXFILEID`, `OXSHOPID`, `OXORDERARTICLEID`, `OXFIRSTDOWNLOAD`, `OXLASTDOWNLOAD`, `OXDOWNLOADCOUNT`, `OXMAXDOWNLOADCOUNT`, `OXDOWNLOADEXPIRATIONTIME`, `OXLINKEXPIRATIONTIME`, `OXRESETCOUNT`, `OXVALIDUNTIL`, `OXTIMESTAMP`) VALUES
('729aafa296783575ddfd8e9527355b3b',	'8c726d3f42ff1a6ea2828d5f309de881',	'ch03.pdf',	'oiaad7812ae7127283b8fd6d309ea5d5',	1,	'c5b7fd8dff99f066c168cd720212075a',	'2020-09-10 09:14:15',	'2020-09-10 09:14:15',	1,	0,	24,	168,	0,	'2020-09-11 09:14:15',	'2020-09-10 09:14:15'),
('886deb7e49bb2e51b4fb939f6ed7655c',	'7f0b6ef39c4e76c04a0f75232489bb65',	'ch03.pdf',	'non_existing_file',	1,	'c5b7fd8dff99f066c168cd720212075a',	'2020-09-10 09:14:15',	'2020-09-10 09:14:15',	1,	0,	24,	168,	0,	'2020-09-11 09:14:15',	'2020-09-10 09:14:15');

REPLACE INTO `oxpricealarm` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXEMAIL`, `OXARTID`, `OXPRICE`, `OXCURRENCY`, `OXLANG`, `OXINSERT`, `OXSENDED`, `OXTIMESTAMP`) VALUES
('_test_wished_price_8_',	2,	'123ad3b5380202966df6ff128e9eecaq',	'user@oxid-esales.com',	'_test_product_5_',	10,	'EUR',	1,	'2020-05-26 00:00:00',	'0000-00-00 00:00:00',	'2020-05-26 11:48:20');

UPDATE `oxarticles` SET `OXMAPID` = 3333, `OXVPE` = 1 WHERE OXID = '_test_product_wished_price_3_';
UPDATE `oxarticles` SET `OXMAPID` = 4444, `OXVPE` = 1 WHERE OXID = '_test_product_wished_price_4_';
UPDATE `oxarticles` SET `OXMAPID` = 1234, `OXVPE` = 1 WHERE OXID = '_test_product_for_rating_5_';
UPDATE `oxarticles` SET `OXMAPID` = 2345, `OXVPE` = 1 WHERE OXID = '_test_product_for_rating_6_';
UPDATE `oxarticles` SET `OXMAPID` = 4567, `OXVPE` = 1 WHERE OXID = '_test_product_for_rating_avg';
UPDATE `oxarticles` SET `OXMAPID` = 1123, `OXVPE` = 1 WHERE OXID='_test_product_for_wish_list';
UPDATE `oxarticles` SET `OXMAPID` = 2123, `OXVPE` = 1, `OXISDOWNLOADABLE` = '1' WHERE OXID='_test_product_for_basket';
REPLACE INTO `oxarticles` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXPARENTID`, `OXACTIVE`, `OXHIDDEN`, `OXACTIVEFROM`, `OXACTIVETO`, `OXARTNUM`, `OXEAN`, `OXDISTEAN`, `OXMPN`, `OXTITLE`, `OXSHORTDESC`, `OXPRICE`, `OXBLFIXEDPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXBPRICE`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXEXTURL`, `OXURLDESC`, `OXURLIMG`, `OXVAT`, `OXTHUMB`, `OXICON`, `OXPIC1`, `OXPIC2`, `OXPIC3`, `OXPIC4`, `OXPIC5`, `OXPIC6`, `OXPIC7`, `OXPIC8`, `OXPIC9`, `OXPIC10`, `OXPIC11`, `OXPIC12`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`, `OXDELIVERY`, `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXFILE`, `OXSEARCHKEYS`, `OXTEMPLATE`, `OXQUESTIONEMAIL`, `OXISSEARCH`, `OXISCONFIGURABLE`, `OXVARNAME`, `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`, `OXVARMINPRICE`, `OXVARMAXPRICE`, `OXVARNAME_1`, `OXVARSELECT_1`, `OXVARNAME_2`, `OXVARSELECT_2`, `OXVARNAME_3`, `OXVARSELECT_3`, `OXTITLE_1`, `OXSHORTDESC_1`, `OXURLDESC_1`, `OXSEARCHKEYS_1`, `OXTITLE_2`, `OXSHORTDESC_2`, `OXURLDESC_2`, `OXSEARCHKEYS_2`, `OXTITLE_3`, `OXSHORTDESC_3`, `OXURLDESC_3`, `OXSEARCHKEYS_3`, `OXBUNDLEID`, `OXFOLDER`, `OXSUBCLASS`, `OXSTOCKTEXT_1`, `OXSTOCKTEXT_2`, `OXSTOCKTEXT_3`, `OXNOSTOCKTEXT_1`, `OXNOSTOCKTEXT_2`, `OXNOSTOCKTEXT_3`, `OXSORT`, `OXSOLDAMOUNT`, `OXNONMATERIAL`, `OXFREESHIPPING`, `OXREMINDACTIVE`, `OXREMINDAMOUNT`, `OXAMITEMID`, `OXAMTASKID`, `OXVENDORID`, `OXMANUFACTURERID`, `OXSKIPDISCOUNTS`, `OXORDERINFO`, `OXPIXIEXPORT`, `OXPIXIEXPORTED`, `OXVPE`, `OXRATING`, `OXRATINGCNT`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`, `OXUPDATEPRICE`, `OXUPDATEPRICEA`, `OXUPDATEPRICEB`, `OXUPDATEPRICEC`, `OXUPDATEPRICETIME`, `OXISDOWNLOADABLE`, `OXSHOWCUSTOMAGREEMENT`) VALUES
('_test_product_wp1_',	6666,	1,	'',	1,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'123',	'',	'',	'',	'Product wp1',	'',	15,	0,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:25:26',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	10,	10,	'',	'',	'',	'',	'',	'',	'Product 5',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	'',	0,	'0000-00-00 00:00:00',	1,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0,	1),
('_test_product_wp2_',	7777,	2,	'',	1,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'213',	'',	'',	'',	'Product wp2',	'',	15,	0,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:25:26',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	10,	10,	'',	'',	'',	'',	'',	'',	'Product 5',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	'',	0,	'0000-00-00 00:00:00',	1,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0,	1),
('_test_product_5_',	5555,	2,	'',	1,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'555',	'',	'',	'',	'Product 5',	'',	15,	0,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:25:26',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	10,	10,	'',	'',	'',	'',	'',	'',	'Product 5',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	'',	0,	'0000-00-00 00:00:00',	1,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0,	1);

REPLACE INTO `oxarticles2shop` (`OXSHOPID`, `OXMAPOBJECTID`, `OXTIMESTAMP`) VALUES
(1, 3333, '2020-01-01 00:00:00'),
(1, 4444, '2020-01-01 00:00:00'),
(2, 5555, '2020-01-01 00:00:00'),
(1, 6666, '2020-01-01 00:00:00'),
(2, 7777, '2020-01-01 00:00:00'),
(1, 1234, '2020-01-01 00:00:00'),
(2, 1234, '2020-01-01 00:00:00'),
(1, 2345, '2020-01-01 00:00:00'),
(1, 1123, '2020-01-01 00:00:00'),
(1, 2123, '2020-01-01 00:00:00'),
(1, 4567, '2020-01-01 00:00:00'),
(2, 2123, '2020-01-01 00:00:00');

REPLACE INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`, `OXCUSTNR`, `OXUSTID`, `OXUSTIDSTATUS`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`, `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXLDAPKEY`, `OXWRONGLOGINS`, `OXUPDATEKEY`, `OXUPDATEEXP`, `OXPOINTS`) VALUES
('245ad3b5380202966df6ff128e9eecaq', 1, 'user', 1, 'otheruser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, '', 0, 0),
('123ad3b5380202966df6ff128e9eecaq', 1, 'user', 2, 'user@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73dd6', 1, 'user', 2, 'existinguser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73ddx', 1, 'user', 2, 'foremailchange@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73cc8', 1, 'user', 1, 'tobedeleted@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73dd7', 1, 'user', 1, 'tempuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73dd8', 1, 'user', 2, 'tempuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73dd9', 1, 'user', 2, 'tempMalluser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73ff5', 1, 'user', 1, 'deletebytest@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, '', 0, 0),
('_09db395b6c85c3881fcb9b437a73gg6', 1, 'user', 1, 'multishopuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', '', 0, '', 0, 0),
('_09db395b6c85c3881fcb9b437a73hh9', 1, 'user', 2, 'multishopuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', '', 0, '', 0, 0),
('245ad3b5380202966df6ff12dodo9caq', 1, 'user', 1, 'dodo@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, '', 0, 0),
('_678b395b6c85c3881fcb9b437a73hh9', 1, 'user', 2, 'newsletter@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', '', 0, '', 0, 0);


REPLACE INTO `oxaddress` (`OXID`, `OXUSERID`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXCITY`, `OXCOUNTRY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXSAL`, `OXTIMESTAMP`) VALUES
('_delete_delivery_address',	'_09db395b6c85c3881fcb9b437a73gg6',	'Marc',	'Muster',	'Hauptstr',	'13',	'Freiburg',	'Germany',	'a7c40f631fc920687.20179984', '',	'79098',	'MR',	'2020-07-14 14:12:48'),
('_delete_delivery_address_2',	'_09db395b6c85c3881fcb9b437a73hh9',	'Marc',	'Muster',	'Hauptstr2',	'132',	'Freiburg',	'Austria',	'a7c40f6320aeb2ec2.72885259', '',	'79098',	'MR',	'2020-07-14 14:44:06');

REPLACE INTO `oxratings` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXTYPE`, `OXOBJECTID`, `OXRATING`) VALUES
('test_rating_8_', 2, '123ad3b5380202966df6ff128e9eecaq', 'oxarticle', '_test_product_5_', 4);
REPLACE INTO oxconfig (OXID, OXSHOPID, OXVARNAME, OXVARTYPE, OXVARVALUE) SELECT
MD5(RAND()), 2, OXVARNAME, OXVARTYPE, OXVARVALUE from oxconfig;

REPLACE INTO `oxaddress` (`OXID`, `OXUSERID`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXCITY`, `OXCOUNTRY`, `OXCOUNTRYID`, `OXZIP`, `OXSAL`, `OXTIMESTAMP`) VALUES
('test_delivery_address_shop_2', '123ad3b5380202966df6ff128e9eecaq', 'Marc2', 'Muster2', 'Hauptstr2', '2', 'Freiburg2', 'Germany2', 'a7c40f631fc920687.20179984', '790982', 'MR', '2020-07-14 14:12:48');

REPLACE INTO `oxuserbaskets` (`OXID`, `OXUSERID`, `OXTITLE`, `OXPUBLIC`) VALUES
('_test_shop2_basket_public', '123ad3b5380202966df6ff128e9eecaq', 'buy_these', true);

REPLACE INTO `oxuserbasketitems` (`OXID`, `OXBASKETID`, `OXARTID`, `OXAMOUNT`, `OXSELLIST`, `OXPERSPARAM`) VALUES
('_test_shop2_basket_item_1', '_test_shop2_basket_public', '_test_product_for_basket', 1, 'N;', '');

REPLACE INTO `oxorder` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`, `OXORDERNR`, `OXBILLCOMPANY`, `OXBILLEMAIL`, `OXBILLFNAME`,
 `OXBILLLNAME`, `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLADDINFO`, `OXBILLUSTID`, `OXBILLUSTIDSTATUS`, `OXBILLCITY`,
  `OXBILLCOUNTRYID`, `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLFON`, `OXBILLFAX`, `OXBILLSAL`, `OXDELCOMPANY`, `OXDELFNAME`,
  `OXDELLNAME`, `OXDELSTREET`, `OXDELSTREETNR`, `OXDELADDINFO`, `OXDELCITY`, `OXDELCOUNTRYID`, `OXDELSTATEID`, `OXDELZIP`,
   `OXDELFON`, `OXDELFAX`, `OXDELSAL`, `OXPAYMENTID`, `OXPAYMENTTYPE`, `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`,
   `OXARTVAT1`, `OXARTVATPRICE1`, `OXARTVAT2`, `OXARTVATPRICE2`, `OXDELCOST`, `OXDELVAT`, `OXPAYCOST`, `OXPAYVAT`, `OXWRAPCOST`,
   `OXWRAPVAT`, `OXGIFTCARDCOST`, `OXGIFTCARDVAT`, `OXCARDID`, `OXCARDTEXT`, `OXDISCOUNT`, `OXEXPORT`, `OXBILLNR`, `OXBILLDATE`,
    `OXTRACKCODE`, `OXSENDDATE`, `OXREMARK`, `OXVOUCHERDISCOUNT`, `OXCURRENCY`, `OXCURRATE`, `OXFOLDER`, `OXTRANSID`, `OXPAYID`,
    `OXXID`, `OXPAID`, `OXSTORNO`, `OXIP`, `OXTRANSSTATUS`, `OXLANG`, `OXINVOICENR`, `OXDELTYPE`, `OXPIXIEXPORT`, `OXTIMESTAMP`,
    `OXISNETTOMODE`) VALUES
('7d090db46a124f48cb7e6836ceef3f66',1,'e7af1c3b786fd02906ccd75698f4e6b9','2011-03-30 10:55:13',1,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','bill vat id',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','7d011a153655ef215558cddd43dc65a8','oxidinvoice',1639.15,2108.39,1950.59,19,311.44,0,0,0,19,0,0,0,0,0,0,'','',157.8,0,'7661','2020-03-31','track_me','2020-08-24 11:11:11','Hier können Sie uns noch etwas mitteilen.',0,'EUR',1,'ORDERFOLDER_NEW','','','','2020-04-01 12:12:12',0,'','OK',0,661,'oxidstandard',0,'2020-08-21 09:39:46',0),
('8c69bc776dd339a83d863c4f64693bb6',1,'e7af1c3b786fd02906ccd75698f4e6b9','2019-08-21 11:41:41',2,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','bill vat id',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-21 09:41:41',0),
('0c99bad495d00254a936ccee2391f763',1,'e7af1c3b786fd02906ccd75698f4e6b9','2020-04-22 14:07:12',3,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','bill vat id',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','del company','Marcia','Pattern','Nebenstraße','123','del addinfo','Freiburg','a7c40f631fc920687.20179984','HH','79106','04012345678','04012345679','MRS','c3260a603ed4e2d3b01981cbc05e8dfd','oxidinvoice',226.05,269,269,19,42.95,0,0,0,19,0,0,0,0,0,19,'','',0,0,'7663','2020-08-24','trick','2020-08-24 11:11:13','Hej, greetings to graphQL! ',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,663,'oxidstandard',0,'2020-08-21 12:07:12',0),
('0c99bad495d00254a936ccee2391f637',2,'e7af1c3b786fd02906ccd75698f4e6b9','2020-04-22 14:07:12',5,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','bill vat id',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','del company','Marcia','Pattern','Nebenstraße','123','del addinfo','Freiburg','a7c40f631fc920687.20179984','HH','79106','04012345678','04012345679','MRS','invoice_order_payment','oxidinvoice',226.05,269,269,19,42.95,0,0,0,19,0,0,0,0,0,19,'','',0,0,'7663','2020-08-24','trick','2020-08-24 11:11:13','Hej, greetings to graphQL! ',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,663,'oxidstandard',0,'2020-08-21 12:07:12',0),
('85ecbd1d5e56172ff5af6917894d4a31',2,'123ad3b5380202966df6ff128e9eecaq','2015-07-02 07:31:37',6,'','user@oxid-esales.com','Marc','Muster','Hauptstr.','13','','',1,'Freiburg','a7c40f631fc920687.20179984','','79098','','','MR','','','','','','','','','','','','','','fada11bc485e15e5b999c7776ef90592','oxempty',8.4,10,10,19,1.6,0,0,0,19,0,0,0,0,0,19,'','',0,0,'','0000-00-00','','0000-00-00 00:00:00','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,0,'',0,'2020-09-02 07:31:37',0),
('85ecbd1d5e56172ff5af6917894d4a32',2,'245ad3b5380202966df6ff128e9eecaq','2015-07-02 07:31:37',7,'','otheruser@oxid-esales.com','Marc','Muster','Hauptstr.','13','','',1,'Freiburg','a7c40f631fc920687.20179984','','79098','','','MR','','','','','','','','','','','','','','fada11bc485e15e5b999c7776ef90592','oxempty',8.4,10,10,19,1.6,0,0,0,19,0,0,0,0,0,19,'','',0,0,'','0000-00-00','','0000-00-00 00:00:00','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,0,'',0,'2020-09-02 07:31:37',0);

UPDATE `oxorder` SET `OXBILLUSTID` = 'bill vat id', `OXBILLUSTIDSTATUS` = 1, `OXPIXIEXPORT` = 0 WHERE OXID = '8c726d3f42ff1a6ea2828d5f309de881';

REPLACE INTO `oxorderarticles` (`OXID`, `OXORDERID`, `OXAMOUNT`, `OXARTID`, `OXARTNUM`, `OXTITLE`, `OXSHORTDESC`, `OXSELVARIANT`, `OXNETPRICE`, `OXBRUTPRICE`, `OXVATPRICE`, `OXVAT`, `OXPERSPARAM`, `OXPRICE`, `OXBPRICE`, `OXNPRICE`, `OXWRAPID`, `OXEXTURL`, `OXURLDESC`, `OXURLIMG`, `OXTHUMB`, `OXPIC1`, `OXPIC2`, `OXPIC3`, `OXPIC4`, `OXPIC5`, `OXWEIGHT`, `OXSTOCK`, `OXDELIVERY`, `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXFILE`, `OXSEARCHKEYS`, `OXTEMPLATE`, `OXQUESTIONEMAIL`, `OXISSEARCH`, `OXFOLDER`, `OXSUBCLASS`, `OXSTORNO`, `OXORDERSHOPID`, `OXERPSTATUS`, `OXISBUNDLE`) VALUES
('677688370a4a64d8336107bcf174fdeb','85ecbd1d5e56172ff5af6917894d4a31',1,'_test_product_for_basket','621','Product 1','','',8.4,10,1.6,19,'',10,10,8.4,'','','','','','','','','','',0,0,'0000-00-00','2020-05-25','2015-07-02 07:31:37',0,0,0,'','','','',1,'','oxarticle',0,2,'',0),
('677688370a4a64d8336107bcf174fde1','85ecbd1d5e56172ff5af6917894d4a32',1,'_test_product_for_basket','621','Product 1','','',8.4,10,1.6,19,'',10,10,8.4,'','','','','','','','','','',0,0,'0000-00-00','2020-05-25','2015-07-02 07:31:37',0,0,0,'','','','',1,'','oxarticle',0,2,'',0);

REPLACE INTO `oxuserpayments` (`OXID`, `OXUSERID`, `OXPAYMENTSID`, `OXVALUE`, `OXTIMESTAMP`) VALUES
('invoice_order_payment',  'e7af1c3b786fd02906ccd75698f4e6b9', 'oxidinvoice', '', '2020-09-11 08:15:00');

REPLACE INTO `oxfiles` (`OXID`, `OXARTID`, `OXFILENAME`) VALUES ('48d949cb0af6076f841aea5cb5b703ed', '_test_product_for_basket', 'ch99.pdf');

REPLACE INTO `oxorderfiles` (`OXID`, `OXORDERID`, `OXFILENAME`, `OXFILEID`, `OXSHOPID`, `OXORDERARTICLEID`, `OXFIRSTDOWNLOAD`, `OXLASTDOWNLOAD`, `OXDOWNLOADCOUNT`, `OXMAXDOWNLOADCOUNT`, `OXDOWNLOADEXPIRATIONTIME`, `OXLINKEXPIRATIONTIME`, `OXRESETCOUNT`, `OXVALIDUNTIL`, `OXTIMESTAMP`) VALUES
('729aafa296783575ddfd8e9527355b9b',	'85ecbd1d5e56172ff5af6917894d4a31',	'ch99.pdf',	'48d949cb0af6076f841aea5cb5b703ed',	2,	'677688370a4a64d8336107bcf174fdeb',	'2020-09-10 09:14:15',	'2020-09-10 09:14:15',	1,	0,	24,	168,	0,	'2020-09-11 09:14:15',	'2020-09-10 09:14:15');

REPLACE INTO `oxshops` (`OXID`, `OXPARENTID`, `OXISINHERITED`, `OXISMULTISHOP`, `OXISSUPERSHOP`, `OXACTIVE`, `OXNAME`, `OXEDITION`, `OXVERSION`, `OXORDEREMAIL`, `OXINFOEMAIL`) VALUES
(2, 0, 0, 0, 0, 1, 'Second Shop', 'EE', '6.0.0', 'reply@myoxideshop.com', 'info@myoxideshop.com'),
(3, 1, 1, 1, 1, 1, 'Third Shop', 'EE', '6.0.0', 'reply@myoxideshop.com', 'info@myoxideshop.com');

REPLACE INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`, `OXMODULE`)
SELECT MD5(RAND()), 2, oxvarname, oxvartype, oxvarvalue, oxmodule FROM oxconfig
WHERE oxshopid = '1'
AND oxvarname IN ( 'aLanguages');

UPDATE `oxcountry` SET `oxorder` = 1 where `OXID` = 'a7c40f631fc920687.20179984';
UPDATE `oxcountry` SET `oxorder` = 2 where `OXID` = '8f241f11096877ac0.98748826';
UPDATE `oxcountry` SET `oxorder` = 3 where `OXID` = 'a7c40f6321c6f6109.43859248';
UPDATE `oxcountry` SET `oxorder` = 4 where `OXID` = 'a7c40f6320aeb2ec2.72885259';
UPDATE `oxcountry` SET `oxorder` = 5 where `OXID` = 'a7c40f632a0804ab5.18804076';

REPLACE INTO `oxorderarticles` (`OXID`, `OXORDERID`, `OXAMOUNT`, `OXARTID`, `OXARTNUM`, `OXTITLE`, `OXSHORTDESC`, `OXSELVARIANT`, `OXNETPRICE`, `OXBRUTPRICE`, `OXVATPRICE`, `OXVAT`, `OXPERSPARAM`, `OXPRICE`, `OXBPRICE`, `OXNPRICE`, `OXWRAPID`, `OXEXTURL`, `OXURLDESC`, `OXURLIMG`, `OXTHUMB`, `OXPIC1`, `OXPIC2`, `OXPIC3`, `OXPIC4`, `OXPIC5`, `OXWEIGHT`, `OXSTOCK`, `OXDELIVERY`, `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXFILE`, `OXSEARCHKEYS`, `OXTEMPLATE`, `OXQUESTIONEMAIL`, `OXISSEARCH`, `OXFOLDER`, `OXSUBCLASS`, `OXSTORNO`, `OXORDERSHOPID`, `OXISBUNDLE`) VALUES
('1d48d8339e49f906700b520ceb4c79aa','7f0b6ef39c4e76c04a0f75232489bb65',1,'05848170643ab0deb9914566391c0c63','1402','Harness MADTRIXX','New freestyle harness with a lean cut outline','',133.61,159,25.39,19,'',159,159,133.61,'','','','','','ion_madtrixx_kite_waist_2011_1.jpg','','','','',0,15,'0000-00-00','2010-12-06','2020-08-28 09:04:14',0,0,0,'','harness, hip harness, madtrixx','','',1,'','oxarticle',0,1,0),
('6c220c5d926c5092035234285f3c8fc3','7f0b6ef39c4e76c04a0f75232489bb65',1,'058c7b525aad619d8b343c0ffada0247','400-03','Sticky Tape DACRON KITEFIX','ideal for small repairs of the kite','black',7.01,7.99,0.98,14,'',7.99,7.99,7.01,'','','','','','kitefix_self-adhesive_dacron_1.jpg','','','','',0,5,'0000-00-00','2010-12-06','2020-08-28 09:04:14',0,0,0,'','sticky tape, kite, repair, kitefix','','',0,'','oxarticle',0,1,0),
('b4b9f8dd0be567d6fcfc59d9b7bb266b','7f0b6ef39c4e76c04a0f75232489bb65',1,'dc5ffdf380e15674b56dd562a7cb6aec','3503','Kuyichi leather belt JEVER','Leather belt, unisex','',27.18,29.9,2.72,10,'',29.9,29.9,27.18,'','','','','','p1170221_1.jpg','p1170222_1.jpg','','','',0,15,'0000-00-00','2010-12-10','2020-08-28 09:04:14',0,0,0,'','kuyichi, leather, leather belt, unisex, used','','',1,'','oxarticle',0,1,0),
('f805daf76a1f8614a7972ab51c22634b','7f0b6ef39c4e76c04a0f75232489bb65',1,'f33d5bcc7135908fd36fc736c643aa1c','1506','KiteFix Glue GLUFIX (30g)','Specially developed for fixing kites','',10.5,12.49,1.99,19,'',12.49,12.49,10.5,'','','','','glufix_z1a_th_th.jpg','glufix_z1a.jpg','','','','',0,27,'0000-00-00','2011-03-24','2020-08-28 09:04:14',0,0,0,'','kite, kitefix, glue, glufix','','',1,'','oxarticle',0,1,0),
('677688370a4a64d8336107bcf174f330','_order_with_non_existing_product',1,'non_existing_product_id','621','Product 1','','',8.4,10,1.6,19,'',10,10,8.4,'','','','','','','','','','',0,0,'0000-00-00','2020-05-25','2015-07-02 07:31:37',0,0,0,'','','','',1,'','oxarticle',0,2,0),
('677688370a4a64d8336107bcf174f331','_order_with_deleted_product',1,'_test_product_for_basket','621','Product 1','','',8.4,10,1.6,19,'',10,10,8.4,'','','','','','','','','','',0,0,'0000-00-00','2020-05-25','2015-07-02 07:31:37',0,0,0,'','','','',1,'','oxarticle',0,2,0),
('c5b7fd8dff99f066c168cd720212075a','8c726d3f42ff1a6ea2828d5f309de881',1,'oiaa81b5e002fc2f73b9398c361c0b97','10101','Online shops with OXID eShop','','',0,0,0,10,'',0,0,0,'','','','','','oxid_book_cover_1.jpg','','','','',0,600,'0000-00-00','2012-04-25','2020-09-10 09:13:36',0,0,0,'','','','',1,'','',0,1,0),
('4ad5c368c9c7715ac800adb27e079ebe','7f0b6ef39c4e76c04a0f75232489bb65',1,'oiaa81b5e002fc2f73b9398c361c0b97','10101','Online shops with OXID eShop','','',0,0,0,10,'',0,0,0,'','','','','','oxid_book_cover_1.jpg','','','','',0,600,'0000-00-00','2012-04-25','2020-09-10 09:13:36',0,0,0,'','','','',1,'','',0,1,0);

REPLACE INTO  `oxnewssubscribed` ( `OXID`,  `OXUSERID`,  `OXDBOPTIN`,  `OXSHOPID`) VALUES
('_newssubscribed_1', '245ad3b5380202966df6ff128e9eecaq', 1, 1),
('_newssubscribed_2', '245ad3b5380202966df6ff128e9eecaq', 2, 2);

REPLACE INTO `oxvoucherseries2shop` (`OXSHOPID`, `OXMAPOBJECTID`) VALUES
('1', '1'),
('1', '2'),
('1', '3'),
('1', '4'),
('1', '5'),
('1', '6'),
('1', '7'),
('2', '8');

UPDATE `oxuserbaskets` SET `OEGQL_PAYMENTID` = 'oxidcashondel' WHERE `OXID` = '_test_basket_public';

REPLACE INTO  `oxdelivery2shop` ( `OXSHOPID`,  `OXMAPOBJECTID`) VALUES
(2, 901),
(2, 902),
(2, 903),
(2, 904),
(2, 905);

REPLACE INTO  `oxdeliveryset2shop` ( `OXSHOPID`,  `OXMAPOBJECTID`) VALUES
(2, 901),
(2, 902),
(2, 903);
SET @@session.sql_mode = '';

REPLACE INTO `oxvoucherseries` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXSERIENR`, `OXDISCOUNT`, `OXDISCOUNTTYPE`, `OXBEGINDATE`, `OXENDDATE`, `OXSERIEDESCRIPTION`, `OXALLOWOTHERSERIES`) VALUES
('basket_payment_cost_voucher', 3, 1, 'basket_payment_cost_voucher', 10.0, 'absolute', '2000-01-01', '2050-12-31', 'basket payment cost voucher', 0);

REPLACE INTO `oxvouchers` (`OXDATEUSED`, `OXORDERID`, `OXUSERID`, `OXRESERVED`, `OXVOUCHERNR`, `OXVOUCHERSERIEID`, `OXDISCOUNT`, `OXID`, `OXTIMESTAMP`, `OEGQL_BASKETID`) VALUES
('2020-10-10',	'',	'',	0,	'basket_payment_cost_voucher',	'basket_payment_cost_voucher',	10.00,	'basket_payment_cost_voucher_1',	'2020-11-16 11:26:01',	'basket_payment_cost');

REPLACE INTO `oxvoucherseries2shop` (`OXSHOPID`, `OXMAPOBJECTID`) VALUES
('1', '3');

REPLACE INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`, `OXCUSTNR`, `OXUSTID`, `OXUSTIDSTATUS`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`, `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXLDAPKEY`, `OXWRONGLOGINS`, `OXUPDATEKEY`, `OXUPDATEEXP`, `OXPOINTS`) VALUES
('e7af1c3b786fd02906ccd75698f4e6b9', 1, 'user', 1, 'user@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'User', 'User', 'Street', '13', '', 'City', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', '', 0, '', 0, 0),
('otheruser', 1, 'user', 1, 'otheruser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 18, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', '', 0, '', 0, 0),
('user_2', 1, 'user', 2, 'user@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', '', 0, '', 0, 0),
('checkoutuser', 1, 'user', 1, 'checkoutuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 88, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', '', 0, '', 0, 0);

REPLACE INTO `oxuserbaskets` (`OXID`, `OXUSERID`, `OXTITLE`, `OXPUBLIC`, `OEGQL_PAYMENTID`, `OEGQL_DELIVERYMETHODID`, `OEGQL_DELADDRESSID`) VALUES
('basket_user', 'e7af1c3b786fd02906ccd75698f4e6b9', 'savedbasket', true, null, null, null),
('basket_user_2', 'user_2', 'savedbasket', true, null, null, null),
('basket_otheruser', 'otheruser', 'savedbasket', true, null, null, null),
('basket_otheruser_2', 'otheruser', 'savedbasket', true, null, null, null),
('basket_user_address_payment', 'e7af1c3b786fd02906ccd75698f4e6b9', 'basketPayment', true, 'oxiddebitnote', 'oxidstandard', 'address_user'),
('basket_user_3', 'e7af1c3b786fd02906ccd75698f4e6b9', 'basketPayment', true, null, null, null),
('basket_payment', 'e7af1c3b786fd02906ccd75698f4e6b9', 'basketPaymentMethod', true, 'oxiddebitnote', 'oxidstandard', 'address_user'),
('basket_payment_cost', 'e7af1c3b786fd02906ccd75698f4e6b9', 'basketPaymentCost', true, 'oxidgraphql', '_deliveryset', 'address_user'),
('basket_shipping', 'e7af1c3b786fd02906ccd75698f4e6b9', 'basketShippingMethod', true, 'oxiddebitnote', 'oxidstandard', 'address_user');

REPLACE INTO `oxuserbasketitems` (`OXID`, `OXBASKETID`, `OXARTID`, `OXAMOUNT`, `OXSELLIST`, `OXPERSPARAM`) VALUES
('_test_basket_payment_item_1', 'basket_payment', 'dc5ffdf380e15674b56dd562a7cb6aec', 1, 'N;', ''),
('_test_basket_payment_cost_item_1', 'basket_payment_cost', 'f4f2d8eee51b0fd5eb60a46dff1166d8', 2, 'N;', ''),
('_test_basket_shipping_item_1', 'basket_shipping', 'dc5ffdf380e15674b56dd562a7cb6aec', 1, 'N;', ''),
('_test_basket_shipping_item_2', 'basket_shipping', 'f4f73033cf5045525644042325355732', 2, 'N;', '');

REPLACE INTO `oxpayments` (`OXID`, `OXACTIVE`, `OXDESC`, `OXADDSUM`, `OXADDSUMTYPE`, `OXADDSUMRULES`, `OXFROMBONI`, `OXFROMAMOUNT`, `OXTOAMOUNT`, `OXVALDESC`, `OXCHECKED`, `OXDESC_1`, `OXVALDESC_1`, `OXDESC_2`, `OXVALDESC_2`, `OXDESC_3`, `OXVALDESC_3`, `OXLONGDESC`, `OXLONGDESC_1`, `OXLONGDESC_2`, `OXLONGDESC_3`, `OXSORT`) VALUES
('oxidgraphql', 1, 'GraphQL', 7.77, 'abs', 0, 0, 0, 1000000, '', 1, 'GraphQL (coconuts)', '', '', '', '', '', '', '', '', '', 700);

REPLACE INTO `oxobject2payment` (`OXID`, `OXPAYMENTID`, `OXOBJECTID`, `OXTYPE`) VALUES
('_paymentrelation1', 'oxidgraphql', 'a7c40f631fc920687.20179984', 'oxcountry'),
('_paymentrelation2', 'oxidgraphql', '_deliveryset', 'oxdelset');

REPLACE INTO `oxobject2delivery` (`OXID`, `OXDELIVERYID`, `OXOBJECTID`, `OXTYPE`) VALUES
('_deliveryrelation1', '_deliveryset', 'a7c40f631fc920687.20179984', 'oxdelset'),
('_deliveryrelation2', '_graphqldel', 'a7c40f631fc920687.20179984', 'oxcountry');

REPLACE INTO `oxdel2delset` (`OXID`, `OXDELID`, `OXDELSETID`) VALUES
('_setrelation1', '_graphqldel', '_deliveryset');

REPLACE INTO `oxdeliveryset` (`OXID`, `OXSHOPID`, `OXMAPID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXPOS`) VALUES
('_deliveryset', 1, 906, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'graphql set', 'graphql set', '', '', 50),
('_unavailabledeliveryset', 1, 907, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'unavailable graphql set', 'unavailable graphql set', '', '', 60);

REPLACE INTO `oxdeliveryset2shop` (`OXSHOPID`, `OXMAPOBJECTID`) VALUES
(1, 906),
(1, 907);

REPLACE INTO `oxdelivery` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`,
`OXADDSUMTYPE`, `OXADDSUM`, `OXDELTYPE`, `OXPARAM`, `OXPARAMEND`, `OXFIXED`, `OXSORT`, `OXFINALIZE`, `OXTIMESTAMP`) VALUES
('_graphqldel', 909, 1, 1,'0000-00-00 00:00:00','0000-00-00 00:00:00','Versandkosten für GraphQL: 6,66 Euro','Shipping costs for GraphQL: 6.66 Euro','','','abs',6.66,'p',0,999999,0,2000,1,'2020-07-16 14:21:45'),
('_unavailablegraphqldel',910,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00','Versandkosten für UA GraphQL: 6,66 Euro','Shipping costs for UA GraphQL: 6.66 Euro','','','abs',6.66,'p',0,99999,0,2000,1,'2020-07-16 14:21:45');

REPLACE INTO `oxdelivery2shop` (`OXSHOPID`, `OXMAPOBJECTID`) VALUES
(1, 909),
(1, 910);

REPLACE INTO `oxaddress` (`OXID`, `OXUSERID`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXCITY`, `OXCOUNTRY`, `OXCOUNTRYID`, `OXZIP`, `OXSAL`, `OXTIMESTAMP`) VALUES
('address_user', 'e7af1c3b786fd02906ccd75698f4e6b9', 'User Del', 'User Del', 'Street Del', '2', 'City Del', 'Germany', 'a7c40f631fc920687.20179984', '790982', 'MR', '2020-07-14 14:12:48'),
('address_otheruser', 'otheruser', 'Marc2', 'Muster2', 'Hauptstr2', '2', 'Freiburg2', 'Germany2', 'a7c40f631fc920687.20179984', '790982', 'MR', '2020-07-14 14:12:48'),
('address_user_2', 'user_2', 'Marc2', 'Muster2', 'Hauptstr2', '2', 'Freiburg2', 'Germany2', 'a7c40f631fc920687.20179984', '790982', 'MR', '2020-07-14 14:12:48');

REPLACE INTO `oxshops` (`OXID`, `OXACTIVE`, `OXNAME`, `OXEDITION`, `OXVERSION`, `OXORDEREMAIL`, `OXINFOEMAIL`) VALUES
(2, 1, 'Second Shop', 'EE', '6.0.0', 'reply@myoxideshop.com', 'info@myoxideshop.com');

REPLACE INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`, `OXMODULE`)
SELECT MD5(RAND()), 2, oxvarname, oxvartype, oxvarvalue, oxmodule FROM oxconfig
WHERE oxshopid = '1'
  AND oxvarname IN ( 'aLanguages');

REPLACE INTO `oxvoucherseries` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXSERIENR`, `OXDISCOUNT`, `OXDISCOUNTTYPE`, `OXBEGINDATE`, `OXENDDATE`, `OXSERIEDESCRIPTION`, `OXALLOWOTHERSERIES`) VALUES
('voucherserie1', 9765, 1, 'voucherserie1', 5.0, 'absolute', '2000-01-01', '2050-12-31', '', 1),
('personal_voucher', 9764, 1, 'myVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'personal voucher', 0),
('product_voucher', 9763, 1, 'productVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'product voucher', 0),
('category_voucher', 9762, 1, 'categoryVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'category voucher', 0),
('user_voucher', 9761, 1, 'userVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'user voucher', 0),
('minvalue_voucher', 9760, 1, 'minvalueVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'min value voucher', 0);

REPLACE INTO `oxvouchers` (`OXDATEUSED`, `OXORDERID`, `OXUSERID`, `OXRESERVED`, `OXVOUCHERNR`, `OXVOUCHERSERIEID`, `OXDISCOUNT`, `OXID`, `OXTIMESTAMP`, `OEGQL_BASKETID`) VALUES
(null, '', '', 0, 'voucher1', 'voucherserie1', 5, 'voucher1id', now(), ''),
(null, '', '', 0, 'myVoucher', 'personal_voucher', 0, 'personal_voucher_1', now(), null),
(null, '', '', 0, 'productVoucher', 'product_voucher', 0, 'product_voucher_1', now(), null),
(null, '', '', 0, 'categoryVoucher', 'category_voucher', 0, 'category_voucher_1', now(), null),
(null, '', '', 0, 'userVoucher', 'user_voucher', 0, 'user_voucher_1', now(), null),
(null, '', '', 0, 'minvalueVoucher', 'minvalue_voucher', 0, 'minvalue_voucher_1', now(), null);

REPLACE INTO `oxvoucherseries2shop` (`OXSHOPID`, `OXMAPOBJECTID`) VALUES
(1, 9765),
(1, 9764),
(1, 9763),
(1, 9762),
(1, 9761),
(1, 9760);
