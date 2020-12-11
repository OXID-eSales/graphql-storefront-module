SET @@session.sql_mode = '';

INSERT INTO `oxvendor` (`OXID`, `OXSHOPID`, `OXACTIVE`, `OXICON`, `OXTITLE`, `OXSHORTDESC`, `OXTITLE_1`, `OXSHORTDESC_1`, `OXTITLE_2`, `OXSHORTDESC_2`, `OXTITLE_3`, `OXSHORTDESC_3`, `OXSHOWSUFFIX`, `OXTIMESTAMP`) VALUES
('fe07958b49de225bd1dbc7594fb9a6b0', 1, 1, '', 'https://fashioncity.com/de', 'Fashion city', 'https://fashioncity.com/en', 'Fashion city', '', '', '', '', 1, '2020-01-10 15:00:00'),
('05833e961f65616e55a2208c2ed7c6b8', 1, 0, '', 'https://demo.com', 'Demo vendor', 'https://demo.com', 'Demo vendor', '', '', '', '', 1, '2020-01-10 15:00:00');

INSERT INTO oxseo (OXOBJECTID,OXIDENT,OXSHOPID,OXLANG,OXSTDURL,OXSEOURL,OXTYPE,OXFIXED,OXEXPIRED,OXPARAMS,OXTIMESTAMP) VALUES
('3a909e7c886063857e86982c7a3c5b84','c11c29d926de486b5ce80520da25e47b',1,0,'index.php?cl=manufacturerlist&amp;mnid=3a909e7c886063857e86982c7a3c5b84','Nach-Hersteller/Mauirippers/','oxmanufacturer',0,0,'','2020-01-09 15:54:06'),
('3a97c94553428daed76ba83e54d3876f','72dce378114a143e848aef67d0ae28d7',1,0,'index.php?cl=manufacturerlist&amp;mnid=3a97c94553428daed76ba83e54d3876f','Nach-Hersteller/Big-Matsol/','oxmanufacturer',0,0,'','2020-01-09 15:54:06'),
('3a9fd0ec4b41d001e770b1d2d7af3e73','0e3d2fdcfe72c8cdd5670b6b2497cf51',1,0,'index.php?cl=manufacturerlist&amp;mnid=3a9fd0ec4b41d001e770b1d2d7af3e73','Nach-Hersteller/Jucker-Hawaii/','oxmanufacturer',0,0,'','2020-01-09 15:54:06'),
('90a0b84564cde2394491df1c673b6aa0','a080a0622ad64fe032c7c2dde4282e41',1,0,'index.php?cl=manufacturerlist&amp;mnid=90a0b84564cde2394491df1c673b6aa0','Nach-Hersteller/ION/','oxmanufacturer',0,0,'','2020-01-09 15:37:39'),
('90a3eccf9d7121a9ca7d659f29021b7a','44d07810b897a415dee6584e57bda35d',1,0,'index.php?cl=manufacturerlist&amp;mnid=90a3eccf9d7121a9ca7d659f29021b7a','Nach-Hersteller/Cabrinha/','oxmanufacturer',0,0,'','2020-01-09 15:54:06'),
('90a8a18dd0cf0e7aec5238f30e1c6106','f43a56850960a9b53ab1cbccbf56602a',1,0,'index.php?cl=manufacturerlist&amp;mnid=90a8a18dd0cf0e7aec5238f30e1c6106','Nach-Hersteller/Naish/','oxmanufacturer',0,0,'','2020-01-09 15:54:06'),
('9434afb379a46d6c141de9c9e5b94fcf','08b373dc43691a65bcf12184b719ef11',1,0,'index.php?cl=manufacturerlist&amp;mnid=9434afb379a46d6c141de9c9e5b94fcf','Nach-Hersteller/Kuyichi/','oxmanufacturer',0,0,'','2020-01-09 15:54:06'),
('adc566c366db8eaf30c6c124a09e82b3','8e798a64d958dfa059c39093a5e43cda',1,0,'index.php?cl=manufacturerlist&amp;mnid=adc566c366db8eaf30c6c124a09e82b3','Nach-Hersteller/Core-Kiteboarding/','oxmanufacturer',0,0,'','2020-01-09 15:54:06'),
('adc6df0977329923a6330cc8f3c0a906','74ac133a0e9403952de061c9fd735449',1,0,'index.php?cl=manufacturerlist&amp;mnid=adc6df0977329923a6330cc8f3c0a906','Nach-Hersteller/Liquid-Force/','oxmanufacturer',0,0,'','2020-01-09 15:54:06'),
('dc5ec524a9aa6175cf7a498d70ce510a','83c2a9997f022c249da68174f2cc5746',1,0,'index.php?cl=manufacturerlist&amp;mnid=dc5ec524a9aa6175cf7a498d70ce510a','Nach-Hersteller/NPX/','oxmanufacturer',0,0,'','2020-01-09 15:54:06'),
('oiaf6ab7e12e86291e86dd3ff891fe40','dc84430f5673d3c1a560d19fffc3b1fc',1,0,'index.php?cl=manufacturerlist&amp;mnid=oiaf6ab7e12e86291e86dd3ff891fe40','Nach-Hersteller/O-Reilly/','oxmanufacturer',0,0,'','2020-01-09 15:37:39'),
('root','9d52dd3016f5f797bb7f86be69ed06eb',1,0,'index.php?cl=manufacturerlist&amp;mnid=root','Nach-Hersteller/','oxmanufacturer',0,0,'','2020-01-09 15:54:06'),
('root','9411d92bed92a131712b1f0f03d9fb42',1,1,'index.php?cl=manufacturerlist&amp;mnid=root','en/By-manufacturer/','oxmanufacturer',0,0,'','2020-01-09 15:54:14'),
('05833e961f65616e55a2208c2ed7c6b8',	'b5a8c2a04e56e4e824bd8a19c73a0441',	1,	0,	'index.php?cl=vendorlist&amp;cnid=v_05833e961f65616e55a2208c2ed7c6b8',	'Nach-Lieferant/https-demo-com/',	'oxvendor',	0,	0,	'',	'2020-01-21 13:04:37'),
('05833e961f65616e55a2208c2ed7c6b8',	'4418e67c61addcec06dc84366315fd1c',	1,	1,	'index.php?cl=vendorlist&amp;cnid=v_05833e961f65616e55a2208c2ed7c6b8',	'en/By-distributor/https-demo-com/',	'oxvendor',	0,	0,	'',	'2020-01-21 13:04:35'),
('a57c56e3ba710eafb2225e98f058d989',	'8cddec2c98b7186e94fea7e0dbfc66ed',	1,	0,	'index.php?cl=vendorlist&amp;cnid=v_a57c56e3ba710eafb2225e98f058d989',	'Nach-Lieferant/www-true-fashion-com/',	'oxvendor',	0,	0,	'',	'2020-01-21 13:04:45'),
('a57c56e3ba710eafb2225e98f058d989',	'9c4de227950cb0b7e15e03acc60c704a',	1,	1,	'index.php?cl=vendorlist&amp;cnid=v_a57c56e3ba710eafb2225e98f058d989',	'en/By-distributor/www-true-fashion-com/',	'oxvendor',	0,	0,	'',	'2020-01-21 13:04:43'),
('fe07958b49de225bd1dbc7594fb9a6b0',	'6a1bd3d7c1981181b02ef99f5b914cae',	1,	0,	'index.php?cl=vendorlist&amp;cnid=v_fe07958b49de225bd1dbc7594fb9a6b0',	'Nach-Lieferant/https-fashioncity-com-de/',	'oxvendor',	0,	0,	'',	'2020-01-21 13:04:39'),
('fe07958b49de225bd1dbc7594fb9a6b0',	'b3b9076081cefb087149f241f708e0ae',	1,	1,	'index.php?cl=vendorlist&amp;cnid=v_fe07958b49de225bd1dbc7594fb9a6b0',	'en/By-distributor/https-fashioncity-com-en/',	'oxvendor',	0,	0,	'',	'2020-01-21 13:04:42'),
('2e0f674a78622c5796f9bb36f13078e2',	'ac8213d45bfe3b74cbee755efccd99c6',	1,	1,	'index.php?cl=content&amp;oxloadid=graphqlcontent&amp;oxcid=2e0f674a78622c5796f9bb36f13078e2',	'en/GraphQL-content-EN/',	'oxcontent',	0,	0,	'',	'2020-05-20 11:07:26'),
('2e0f674a78622c5796f9bb36f13078e2',	'c949ba5cf8caff4f17c9dd0cf8f66762',	1,	0,	'index.php?cl=content&amp;oxloadid=graphqlcontent&amp;oxcid=2e0f674a78622c5796f9bb36f13078e2',	'GraphQL-content-DE/',	'oxcontent',	0,	0,	'',	'2020-05-20 11:07:23'),
('e6fc3fe89d5da58da9bfcfba451fd365',	'0dc67fa9df7a7c02899ed6e1946f0cf7',	1,	0,	'index.php?cl=content&amp;oxloadid=graphqlcontentwithcategory&amp;oxcid=e6fc3fe89d5da58da9bfcfba451fd365&amp;cnid=943a9ba3050e78b443c16e043ae60ef3',	'Kiteboarding/GraphQL-content-with-category-DE/',	'oxcontent',	0,	0,	'',	'2020-05-20 11:13:42'),
('e6fc3fe89d5da58da9bfcfba451fd365',	'ab4dfeb43f892c3113b7a342f7b9bb37',	1,	1,	'index.php?cl=content&amp;oxloadid=graphqlcontentwithcategory&amp;oxcid=e6fc3fe89d5da58da9bfcfba451fd365&amp;cnid=943a9ba3050e78b443c16e043ae60ef3',	'en/Kiteboarding/GraphQL-content-with-category-EN/',	'oxcontent',	0,	0,	'',	'2020-05-20 11:13:33'),
('e3ab0a5f8598f24dbb3a56b30c472844',	'24fc72d20dba2355f4b48ce90d05ce3f',	1,	1,	'index.php?cl=content&amp;oxloadid=graphqlcontentwithoutcategory&amp;oxcid=e3ab0a5f8598f24dbb3a56b30c472844',	'en/GraphQL-content-without-category-EN/',	'oxcontent',	0,	0,	'',	'2020-05-20 11:18:20'),
('e3ab0a5f8598f24dbb3a56b30c472844',	'60e2b80f57c6f4a1c7e5e1301b027388',	1,	0,	'index.php?cl=content&amp;oxloadid=graphqlcontentwithoutcategory&amp;oxcid=e3ab0a5f8598f24dbb3a56b30c472844',	'GraphQL-content-without-category-DE/',	'oxcontent',	0,	0,	'',	'2020-05-20 11:18:23');

UPDATE `oxcategories` SET `OXACTIVE` = 0, `OXACTIVE_1` = 0, `OXACTIVE_2` = 0, `OXACTIVE_3` = 0 WHERE `OXID` = 'd8665fef35f4d528e92c3d664f4a00c0';

REPLACE INTO `oxobject2seodata` (`OXOBJECTID`, `OXSHOPID`, `OXLANG`, `OXKEYWORDS`, `OXDESCRIPTION`) VALUES
('058de8224773a1d5fd54d523f0c823e0', 1, 0, 'german seo keywords', 'german seo description'),
('058de8224773a1d5fd54d523f0c823e0', 1, 1, 'english seo keywords', 'english seo description'),
('943173edecf6d6870a0f357b8ac84d32', 1, 0, 'german cat seo keywords', 'german cat seo description'),
('943173edecf6d6870a0f357b8ac84d32', 1, 1, 'english cat seo keywords', 'english cat seo description'),
('fe07958b49de225bd1dbc7594fb9a6b0', 1, 0, 'german vendor seo keywords', 'german vendor seo description'),
('fe07958b49de225bd1dbc7594fb9a6b0', 1, 1, 'english vendor seo keywords', 'english vendor seo description'),
('oiaf6ab7e12e86291e86dd3ff891fe40', 1, 0, 'german manufacturer seo keywords', 'german manufacturer seo description'),
('oiaf6ab7e12e86291e86dd3ff891fe40', 1, 1, 'english manufacturer seo keywords', 'english manufacturer seo description'),
('058e613db53d782adfc9f2ccb43c45fe', 1, 0, 'german product seo keywords', 'german product seo description'),
('058e613db53d782adfc9f2ccb43c45fe', 1, 1, 'english product seo keywords', 'english product seo description');

REPLACE INTO `oxselectlist` (`OXID`, `OXSHOPID`, `OXTITLE`, `OXIDENT`, `OXVALDESC`, `OXTITLE_1`, `OXVALDESC_1`) VALUES
('testsellist', 1, 'test selection list [DE] šÄßüл', 'test sellist šÄßüл', 'selvar1 [DE]!P!1__@@selvar2 [DE]__@@selvar3 [DE]!P!-2__@@selvar4 [DE]!P!2%__@@', 'test selection list [EN] šÄßüл', 'selvar1 [EN] šÄßüл!P!1__@@selvar2 [EN] šÄßüл__@@selvar3 [EN] šÄßüл!P!-2__@@selvar4 [EN] šÄßüл!P!2%__@@');

REPLACE INTO `oxobject2selectlist` (`OXID`, `OXOBJECTID`, `OXSELNID`, `OXSORT`) VALUES
('article2testsellis', '058de8224773a1d5fd54d523f0c823e0', 'testsellist', 0);

INSERT INTO `oxratings` (`OXID`, `OXUSERID`, `OXTYPE`, `OXOBJECTID`, `OXRATING`) VALUES
('_test_wrong_user', 'wronguserid', 'oxarticle', 'b56597806428de2f58b1c6c7d3e0e093', 4),
('_test_wrong_product', 'e7af1c3b786fd02906ccd75698f4e6b9', 'oxarticle', 'wrongobjectid', 4),
('_test_wrong_object_type', 'e7af1c3b786fd02906ccd75698f4e6b9', 'oxrecommlist', 'b56597806428de2f58b1c6c7d3e0e093', 4),
('_test_more_ratings', 'e7af1c3b786fd02906ccd75698f4e6b9', 'oxarticle', '058e613db53d782adfc9f2ccb43c45fe', 4),
('_test_more_ratings_2', 'e7af1c3b786fd02906ccd75698f4e6b9', 'oxarticle', '058e613db53d782adfc9f2ccb43c45fe', 4),
('_test_more_ratings_3', 'e7af1c3b786fd02906ccd75698f4e6b9', 'oxarticle', '058e613db53d782adfc9f2ccb43c45fe', 4);

UPDATE `oxreviews` SET `OXACTIVE` = 1 WHERE `OXID` = '94415306f824dc1aa2fce0dc4f12783d';
INSERT INTO `oxreviews` (`OXID`, `OXACTIVE`, `OXOBJECTID`, `OXTYPE`, `OXTEXT`, `OXUSERID`, `OXRATING`, `OXLANG`) VALUES
('_test_wrong_user', 1, 'b56597806428de2f58b1c6c7d3e0e093', 'oxarticle', 'example wrong userid text', 'wronguserid', 4, 0),
('_test_wrong_product', 1, 'wrongobjectid', 'oxarticle', 'example wrong userid text', 'e7af1c3b786fd02906ccd75698f4e6b9', 4, 0),
('_test_wrong_object_type', 1, 'wrongobjectid', 'oxrecommlist', 'example wrong userid text', 'e7af1c3b786fd02906ccd75698f4e6b9', 4, 0),
('_test_real_product_1', 1, '058e613db53d782adfc9f2ccb43c45fe', 'oxarticle', 'example review for product 1', 'e7af1c3b786fd02906ccd75698f4e6b9', 3, 0),
('_test_real_product_2', 1, '058e613db53d782adfc9f2ccb43c45fe', 'oxarticle', 'example review for product 2', 'e7af1c3b786fd02906ccd75698f4e6b9', 4, 0),
('_test_real_product_inactive', 0, '058e613db53d782adfc9f2ccb43c45fe', 'oxarticle', 'example review for product inactive', 'e7af1c3b786fd02906ccd75698f4e6b9', 5, 0),
('_test_lang_0_review', 1, 'notreal', 'oxarticle', 'example lang 0 review', 'e7af1c3b786fd02906ccd75698f4e6b9', 5, 0),
('_test_lang_1_review', 1, 'notreal', 'oxarticle', 'example lang 1 review', 'e7af1c3b786fd02906ccd75698f4e6b9', 5, 1);

UPDATE `oxlinks` SET `OXACTIVE` = 0 WHERE `OXID` = 'ce342e8acb69f1748.25672556';
INSERT INTO `oxlinks` (`OXID`, `OXSHOPID`, `OXACTIVE`, `OXURL`, `OXURLDESC`, `OXURLDESC_1`, `OXURLDESC_2`, `OXURLDESC_3`, `OXINSERT`) VALUES
('test_active', 1, 1, 'http://www.oxid-esales.com', '<p>Deutsche Beschreibung aktiv</p>', '<p>English Description active</p>', '', '', '2012-06-04 07:04:54'),
('test_active_2', 1, 1, 'http://www.oxid-esales.com', '<p>Aktiv link</p>', '<p>Active link</p>', '', '', '2012-06-04 07:04:54'),
('test_inactive', 1, 0, 'http://www.oxid-esales.com', '<p>Deutsche Beschreibung inakitv</p>', '<p>English Description inactive</p>', '', '', '2012-06-04 07:04:54'),
('test_inactive_2', 1, 0, 'http://www.oxid-esales-inactive.com', '<p>Inaktiv link</p>', '<p>Incative link</p>', '', '', '2012-06-05 07:04:54');

UPDATE oxarticles SET oxdelivery = '2999-12-31' WHERE oxid = 'f4fe754e1692b9f79f2a7b1a01bb8dee';
UPDATE oxarticles SET oxtitle = 'Kitefix Kleber GLUFIX' WHERE oxid = 'f33d5bcc7135908fd36fc736c643aa1c';
UPDATE oxarticles SET oxtitle = 'Kite LEINEN VECTOR QUAD PRO' WHERE oxid = 'd86f775338da3228bec9e968f02e7551';

#banners
UPDATE `oxactions` SET `OXTITLE_1` = 'Banner 1 en' WHERE `OXID` = 'b5639c6431b26687321f6ce654878fa5';
UPDATE `oxactions` SET `OXTITLE_1` = 'Banner 4 en' WHERE `OXID` = 'cb34f86f56162d0c95890b5985693710';
UPDATE `oxactions` SET `OXACTIVE` = 0 WHERE `OXID` = 'b56a097dedf5db44e20ed56ac6defaa8';
INSERT INTO `oxactions` (`OXID`, `OXSHOPID`, `OXTYPE`, `OXSORT`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`) VALUES
('_test_active_interval', 1, 3, 5, 0, '2020-01-01 00:00:00', '2035-01-01 00:00:00');
UPDATE `oxactions` SET `OXSORT` = 1 WHERE `OXID` = 'cb34f86f56162d0c95890b5985693710';
UPDATE `oxactions` SET `OXSORT` = 2 WHERE `OXID` = 'b56efaf6c93664b6dca5b1cee1f87057';
UPDATE `oxactions` SET `OXSORT` = 3 WHERE `OXID` = 'b56a097dedf5db44e20ed56ac6defaa8';
UPDATE `oxactions` SET `OXSORT` = 4 WHERE `OXID` = 'b5639c6431b26687321f6ce654878fa5';

#promotions
INSERT INTO `oxactions` (`OXID`, `OXSHOPID`, `OXTYPE`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXLONGDESC`, `OXLONGDESC_1`, `OXLONGDESC_2`, `OXLONGDESC_3`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXPIC`, `OXPIC_1`, `OXPIC_2`, `OXPIC_3`, `OXLINK`, `OXLINK_1`, `OXLINK_2`, `OXLINK_3`, `OXSORT`, `OXTIMESTAMP`) VALUES
('test_active_promotion_1',	    1,	2,	'Current Promotion 1 DE',	'Current Promotion 1 EN',	'',	'',	'Long description 1 DE',	'Long description 1 EN',	'',	'',	1,	'2010-10-10 00:00:00',	'2111-10-10 00:00:00',	'',	'',	'',	'',	'',	'',	'',	'',	3,	'2020-04-23 12:07:10'),
('test_active_promotion_2',	    1,	2,	'Current Promotion 2 DE',	'Current Promotion 2 EN',	'',	'',	'Long description 2 DE',	'Long description 2 EN',	'',	'',	1,	'2010-01-01 00:00:00',	'2111-10-10 00:00:00',	'',	'',	'',	'',	'',	'',	'',	'',	2,	'2020-04-23 12:07:10'),
('test_inactive_promotion_1',	1,	2,	'Upcoming promotion DE',	'Upcoming promotion EN',	'',	'',	'Long description 3 DE',	'Long description 3 EN',	'',	'',	0,	'2010-01-01 00:00:00',	'2010-02-01 00:00:00',	'',	'',	'',	'',	'',	'',	'',	'',	1,	'2020-04-23 12:07:10'),
('test_inactive_promotion_2',	1,	2,	'Expired promotion DE',	    'Expired promotion EN',	    '',	'',	'Long description 4 DE',	'Long description 4 EN',	'',	'',	0,	'2010-01-01 00:00:00',	'2010-02-01 00:00:00',	'',	'',	'',	'',	'',	'',	'',	'',	1,	'2020-04-23 12:07:10');

UPDATE `oxarticles` SET `OXVENDORID` = 'fe07958b49de225bd1dbc7594fb9a6b0' where `OXID` = '10049f9322cf8852f8d567e9662cb12c';
UPDATE `oxarticles` SET `OXVENDORID` = 'fe07958b49de225bd1dbc7594fb9a6b0' where `OXID` = '10067ab25bf275b7e68bc0431b204d24';
UPDATE `oxarticles` SET `OXVENDORID` = 'fe07958b49de225bd1dbc7594fb9a6b0' where `OXID` = '1008b12cef0476f5e941da460ba621e6';

#product_to_manufacturer
UPDATE `oxarticles` SET `OXMANUFACTURERID` = 'oiaf6ab7e12e86291e86dd3ff891fe40' where `OXID` = '058e613db53d782adfc9f2ccb43c45fe';

#product sort direction
UPDATE `oxarticles` SET `OXSORT` = '1' where `OXID` = 'b56369b1fc9d7b97f9c5fc343b349ece';
UPDATE `oxarticles` SET `OXSORT` = '2' where `OXID` = 'dc55b2b2e633527f9a8b2408a032f28f';
UPDATE `oxarticles` SET `OXSORT` = '3' where `OXID` = 'dc5ffdf380e15674b56dd562a7cb6aec';
UPDATE `oxarticles` SET `OXSORT` = '4' where `OXID` = 'f4f981b0d9e34d2aeda82d79412480a4';

# Banner is configured for group
INSERT INTO `oxactions` (`OXID`, `OXSHOPID`, `OXTYPE`, `OXSORT`, `OXACTIVE`) VALUES
('_test_group_banner', 1, 3, 6, 1);
INSERT INTO `oxobject2action` (`OXACTIONID`, `OXOBJECTID`, `OXCLASS`) VALUES
('_test_group_banner','oxidadmin', 'oxgroups');

# Contents with different category values
INSERT INTO `oxcontents` (`OXID`, `OXLOADID`, `OXSHOPID`, `OXSNIPPET`, `OXTYPE`, `OXACTIVE`, `OXACTIVE_1`, `OXPOSITION`, `OXTITLE`, `OXCONTENT`, `OXTITLE_1`, `OXCONTENT_1`, `OXACTIVE_2`, `OXTITLE_2`, `OXCONTENT_2`, `OXACTIVE_3`, `OXTITLE_3`, `OXCONTENT_3`, `OXCATID`, `OXFOLDER`, `OXTERMVERSION`, `OXTIMESTAMP`) VALUES
('2e0f674a78622c5796f9bb36f13078e2',	'graphqlcontent',	1,	1,	0,	1,	1,	'',	'GraphQL content DE',	'',	'GraphQL content EN',	'',	0,	'',	'',	0,	'',	'',	NULL,	'CMSFOLDER_USERINFO',	'',	'2020-05-20 11:08:32'),
('e6fc3fe89d5da58da9bfcfba451fd365',	'graphqlcontentwithcategory',	1,	0,	2,	1,	1,	'',	'GraphQL content with category DE',	'Content DE',	'GraphQL content with category EN',	'',	0,	'',	'',	0,	'',	'',	'0f4fb00809cec9aa0910aa9c8fe36751',	'CMSFOLDER_CATEGORY',	'',	'2020-05-20 11:13:29'),
('e3ab0a5f8598f24dbb3a56b30c472844',	'graphqlcontentwithoutcategory',	1,	1,	0,	1,	1,	'',	'GraphQL content without category DE',	'',	'GraphQL content without category EN',	'',	0,	'',	'',	0,	'',	'',	'943a9ba3050e78b443c16e043ae60ef3',	'CMSFOLDER_USERINFO',	'',	'2020-05-20 11:18:01');

# Product with inactive bundle
INSERT INTO `oxarticles` (`OXID`, `OXSHOPID`, `OXPARENTID`, `OXACTIVE`, `OXHIDDEN`, `OXACTIVEFROM`, `OXACTIVETO`, `OXARTNUM`, `OXEAN`, `OXDISTEAN`, `OXMPN`, `OXTITLE`, `OXSHORTDESC`, `OXPRICE`, `OXBLFIXEDPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXBPRICE`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXEXTURL`, `OXURLDESC`, `OXURLIMG`, `OXVAT`, `OXTHUMB`, `OXICON`, `OXPIC1`, `OXPIC2`, `OXPIC3`, `OXPIC4`, `OXPIC5`, `OXPIC6`, `OXPIC7`, `OXPIC8`, `OXPIC9`, `OXPIC10`, `OXPIC11`, `OXPIC12`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`, `OXDELIVERY`, `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXFILE`, `OXSEARCHKEYS`, `OXTEMPLATE`, `OXQUESTIONEMAIL`, `OXISSEARCH`, `OXISCONFIGURABLE`, `OXVARNAME`, `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`, `OXVARMINPRICE`, `OXVARMAXPRICE`, `OXVARNAME_1`, `OXVARSELECT_1`, `OXVARNAME_2`, `OXVARSELECT_2`, `OXVARNAME_3`, `OXVARSELECT_3`, `OXTITLE_1`, `OXSHORTDESC_1`, `OXURLDESC_1`, `OXSEARCHKEYS_1`, `OXTITLE_2`, `OXSHORTDESC_2`, `OXURLDESC_2`, `OXSEARCHKEYS_2`, `OXTITLE_3`, `OXSHORTDESC_3`, `OXURLDESC_3`, `OXSEARCHKEYS_3`, `OXBUNDLEID`, `OXFOLDER`, `OXSUBCLASS`, `OXSTOCKTEXT_1`, `OXSTOCKTEXT_2`, `OXSTOCKTEXT_3`, `OXNOSTOCKTEXT_1`, `OXNOSTOCKTEXT_2`, `OXNOSTOCKTEXT_3`, `OXSORT`, `OXSOLDAMOUNT`, `OXNONMATERIAL`, `OXFREESHIPPING`, `OXREMINDACTIVE`, `OXREMINDAMOUNT`, `OXAMITEMID`, `OXAMTASKID`, `OXVENDORID`, `OXMANUFACTURERID`, `OXSKIPDISCOUNTS`, `OXRATING`, `OXRATINGCNT`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`, `OXUPDATEPRICE`, `OXUPDATEPRICEA`, `OXUPDATEPRICEB`, `OXUPDATEPRICEC`, `OXUPDATEPRICETIME`, `OXISDOWNLOADABLE`, `OXSHOWCUSTOMAGREEMENT`) VALUES
('_test_active_main_bundle',	1,	'',	1,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'111',	'',	'',	'',	'Product 1',	'',	10,	0,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:25:26',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	10,	10,	'',	'',	'',	'',	'',	'',	'Product 1',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'_test_inactive_bundle',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0,	1),
('_test_inactive_bundle',	1,	'',	0,	0,	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	'222',	'',	'',	'',	'Product 2',	'',	20,	0,	0,	0,	0,	0,	0,	'',	0,	'',	'',	'',	NULL,	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	1,	'',	'',	'0000-00-00',	'2020-05-25',	'2020-05-25 09:26:20',	0,	0,	0,	'',	'',	'',	'',	1,	0,	'',	0,	0,	'',	20,	20,	'',	'',	'',	'',	'',	'',	'Product 2',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'oxarticle',	'',	'',	'',	'',	'',	'',	0,	0,	0,	0,	0,	0,	'',	'0',	'',	'',	0,	0,	0,	0,	0,	'',	0,	0,	0,	0,	'0000-00-00 00:00:00',	0,	1);

# test user
REPLACE INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`, `OXCUSTNR`, `OXUSTID`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`, `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXUPDATEKEY`, `OXUPDATEEXP`, `OXPOINTS`) VALUES
('245ad3b5380202966df6ff128e9eecaq', 1, 'user', 1, 'otheruser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0),
('e7af1c3b786fd02906ccd75698f4e6b9', 1, 'user', 1, 'user@oxid-esales.com', '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 2, '', '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', '', 0, 0);

REPLACE INTO `oxobject2category` (`OXID`, `OXOBJECTID`, `OXCATNID`, `OXTIME`) VALUES
('article2category', 'b56164c54701f07df14b141da197c207', 'fc7e7bd8403448f00a363f60f44da8f2', 9999999999);
