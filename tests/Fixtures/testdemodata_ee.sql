SET @@session.sql_mode = '';

REPLACE INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`, `OXCUSTNR`, `OXUSTID`, `OXUSTIDSTATUS`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`, `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXWRONGLOGINS`, `OXUPDATEKEY`, `OXUPDATEEXP`, `OXPOINTS`) VALUES
('oxdefaultadmin', 1, 'malladmin', 1, 'admin', 'e3a8a383819630e42d9ef90be2347ea70364b5efbb11dfc59adbf98487e196fffe4ef4b76174a7be3f2338581e507baa61c852b7d52f4378e21bd2de8c1efa5e', '61646D696E61646D696E61646D696E', 1, '', 1, 'Your Company Name', 'John', 'Doe', 'Maple Street', '2425', '', 'Any City', 'a7c40f631fc920687.20179984', '', '9041', '217-8918712', '217-8918713', 'MR', 1000, '2003-01-01 00:00:00', '2003-01-01 00:00:00', '', '', '0000-00-00', '', 0, '', 0, 0),
('e7af1c3b786fd02906ccd75698f4e6b9', 1, 'user', 1, 'user@oxid-esales.com', '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 2, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-21', '', 0, '', 0, 0),
('123ad3b5380202966df6ff128e9eecaq', 1, 'user', 2, 'user@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', 0, '', 0, 0),
('_45ad3b5380202966df6ff128e9eecaq', 1, 'user', 1, 'differentuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('245ad3b5380202966df6ff128e9eecaq', 1, 'user', 1, 'otheruser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('_09db395b6c85c3881fcb9b437a73gg6', 1, 'user', 1, 'multishopuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', 0, '', 0, 0),
('_09db395b6c85c3881fcb9b437a73hh9', 1, 'user', 2, 'multishopuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', 0, '', 0, 0),
('245ad3b5380202966df6ff12dodo9caq', 1, 'user', 1, 'dodo@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('9119cc8cd9593c214be93ee558235f3c', 1, 'user', 1, 'existinguser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Eleanor', 'Rodriguez', 'Jerry Dove Drive', '1118', '', 'Tuscon', '8f241f11096877ac0.98748826', 'AZ', '85713', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73dd6', 1, 'user', 2, 'existinguser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('_678b395b6c85c3881fcb9b437a73hh9', 1, 'user', 2, 'newsletter@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', 0, '', 0, 0),
('9119cc8cd9593c214be93ee558235g5x', 1, 'user', 1, 'foremailchangeCE@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Eleanor', 'Rodriguez', 'Jerry Dove Drive', '1118', '', 'Tuscon', '8f241f11096877ac0.98748826', 'AZ', '85713', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73cc8', 1, 'user', 1, 'tobedeleted@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73dd7', 1, 'user', 1, 'tempuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73dd8', 1, 'user', 2, 'tempuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73dd9', 1, 'user', 2, 'tempMalluser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('9119cc8cd9593c214be93ee558235f3x', 1, 'user', 1, 'foremailchange@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Eleanor', 'Rodriguez', 'Jerry Dove Drive', '1118', '', 'Tuscon', '8f241f11096877ac0.98748826', 'AZ', '85713', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73ddx', 1, 'user', 2, 'foremailchange@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('309db395b6c85c3881fcb9b437a73ff5', 1, 'user', 1, 'deletebytest@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0),
('standarduser', 1, 'user', 1, 'standarduser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'User', 'User', 'Street', '13', '', 'City', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', 0, '', 0, 0),
('standarduser2', 1, 'user', 2, 'standarduser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'User', 'User', 'Street', '13', '', 'City', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', 0, '', 0, 0),
('anotheruser', 1, 'user', 1, 'anotheruser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 18, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', 0, '', 0, 0),
('checkoutuser', 1, 'user', 1, 'checkoutuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 88, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '1984-12-22', '', 0, '', 0, 0),
('_678d3b5380202966df6ff128e9eecaq', 1, 'user', 1, 'exampleuser@oxid-esales.com',  '$2y$10$b186f117054b700a89de9uXDzfahkizUucitfPov3C2cwF5eit2M2', 'b186f117054b700a89de929ce90c6aef', 8, '', 1, '', 'Marc', 'Muster', 'Hauptstr.', '13', '', 'Freiburg', 'a7c40f631fc920687.20179984', '', '79098', '', '', 'MR', 1000, '2011-02-01 08:41:25', '2011-02-01 08:41:25', '', '', '0000-00-00', '', 0, '', 0, 0);

REPLACE INTO `oxnewssubscribed` (`OXID`, `OXUSERID`, `OXSAL`, `OXFNAME`, `OXLNAME`, `OXEMAIL`, `OXDBOPTIN`, `OXSUBSCRIBED`, `OXSHOPID`) VALUES
('e7a194e983a31c078c6d5527a7b9f9ba','e7af1c3b786fd02906ccd75698f4e6b9','MR','Marc','Muster','user@oxid-esales.com',1,'2011-02-01 08:41:25',1),
('_newssubscribed_1', '245ad3b5380202966df6ff128e9eecaq',null,null,null,null, 1, null, 1),
('_newssubscribed_2', '245ad3b5380202966df6ff128e9eecaq',null,null,null,null, 2, null, 2);

REPLACE INTO `oxobject2group` (`OXID`, `OXSHOPID`, `OXOBJECTID`, `OXGROUPSID`) VALUES
('test_admin', 1, 'oxdefaultadmin', 'oxidadmin'),
('e7a3bc0ffde37901c6c1be9bdd43b9a5',1,'e7af1c3b786fd02906ccd75698f4e6b9','oxidcustomer'),
('123ad3b5380202966df6ff128e9eeca1', 2, '123ad3b5380202966df6ff128e9eecaq', 'oxidcustomer'),
('123ad3b5380202966df6ff128e9eeca7', 1, '9119cc8cd9593c214be93ee558235f3c', 'oxidcustomer'),
('123ad3b5380202966df6ff128e9eeca9', 1, '_45ad3b5380202966df6ff128e9eecaq', 'oxidcustomer'),
('123ad3b5380202966df6ff128e9eeca0', 1, '245ad3b5380202966df6ff128e9eecaq', 'oxidnotyetordered'),
('123ad3b5380202966df6ff128e9eeca3', 1, '309db395b6c85c3881fcb9b437a73ff5', 'oxidcustomer'),
('f1d3fdd845d646ce0.54037160',1,'oxidcashondel','oxidcustomer'),
('g7a3bc0ffde37901c6c1be9bdd43b9a5', 1, 'standarduser', 'oxidcustomer'),
('g7a3bc0ffde37901c6c1be9bdd43b9x6', 1, 'standarduser', 'oxidgoodcust'),
('g7a3bc0ffde37901c6c1be9bdd43b9a6', 2, 'standarduser2', 'oxidcustomer'),
('g7a3bc0ffde37901c6c1be9bdd43b9ax', 2, '309db395b6c85c3881fcb9b437a73dd6', 'oxidcustomer'),
('123ad3b5380202966df6ff128e9eeca4', 1, 'anotheruser', 'oxidcustomer'),
('123ad3b5380202966df6ff128e9eeca6', 1, 'checkoutuser', 'oxidcustomer'),
('f1d3fdd845d66bfa6.86175113',1,'oxidcashondel','oxidnewcustomer');

REPLACE INTO `oxaddress` (`OXID`, `OXUSERID`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXCITY`, `OXCOUNTRY`, `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXSAL`, `OXTIMESTAMP`) VALUES
('test_delivery_address', 'e7af1c3b786fd02906ccd75698f4e6b9', 'Marc', 'Muster', 'Hauptstr', '13', 'Freiburg', 'Germany', 'a7c40f631fc920687.20179984', '', '79098', 'MR', '2020-07-14 14:12:48'),
('test_delivery_address_2', 'e7af1c3b786fd02906ccd75698f4e6b9', 'Marc', 'Muster', 'Hauptstr2', '132', 'Freiburg', 'Austria', 'a7c40f6320aeb2ec2.72885259', '', '79098', 'MR', '2020-07-14 14:44:06'),
('test_delivery_address_us', '9119cc8cd9593c214be93ee558235f3c', 'Eleanor', 'Rodriguez', 'Cook Hill Road', '4631', 'Apalachin', 'United States', '8f241f11096877ac0.98748826', 'AZ', '13732', 'MR', '2020-07-14 14:44:06'),
('_delete_delivery_address', '_09db395b6c85c3881fcb9b437a73gg6', 'Marc', 'Muster', 'Hauptstr', '13', 'Freiburg', 'Germany', 'a7c40f631fc920687.20179984', '', '79098', 'MR', '2020-07-14 14:12:48'),
('_delete_delivery_address_2', '_09db395b6c85c3881fcb9b437a73hh9', 'Marc', 'Muster', 'Hauptstr2', '132', 'Freiburg', 'Austria', 'a7c40f6320aeb2ec2.72885259', '', '79098', 'MR', '2020-07-14 14:44:06'),
('test_delivery_address_shop_2', '123ad3b5380202966df6ff128e9eecaq', 'Marc2', 'Muster2', 'Hauptstr2', '2', 'Freiburg2', 'Germany2', 'a7c40f631fc920687.20179984', '', '790982', 'MR', '2020-07-14 14:12:48'),
('address_user', 'standarduser', 'User Del', 'User Del', 'Street Del', '2', 'City Del', 'Germany', 'a7c40f631fc920687.20179984', '', '790982', 'MR', '2020-07-14 14:12:48'),
('address_otheruser', 'anotheruser', 'Marc2', 'Muster2', 'Hauptstr2', '2', 'Freiburg2', 'Germany2', 'a7c40f631fc920687.20179984', '', '790982', 'MR', '2020-07-14 14:12:48'),
('address_user_2', 'user_2', 'Marc2', 'Muster2', 'Hauptstr2', '2', 'Freiburg2', 'Germany2', 'a7c40f631fc920687.20179984', '', '790982', 'MR', '2020-07-14 14:12:48');

REPLACE INTO `oxshops` (`OXID`, `OXPARENTID`, `OXISINHERITED`, `OXISMULTISHOP`, `OXISSUPERSHOP`, `OXACTIVE`, `OXDEFCURRENCY`, `OXNAME`, `OXEDITION`, `OXVERSION`, `OXORDEREMAIL`, `OXINFOEMAIL`) VALUES
(2, 0, 0, 0, 0, 1, '', 'Second Shop', 'EE', '6.0.0', 'reply@myoxideshop.com', 'info@myoxideshop.com'),
(3, 1, 1, 1, 1, 1, '', 'Third Shop', 'EE', '6.0.0', 'reply@myoxideshop.com', 'info@myoxideshop.com');

REPLACE INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXMODULE`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`, `OXTIMESTAMP`) VALUES
('3c4f033dfb8fd4fe692715dda19ecdxx', 1, '', 'sTheme', 'string', 'twig', '2021-05-28 14:24:39'),
('2e244d9a2f7834a31.62749934',1,'','bl_perfLoadCurrency','bool','1','2021-05-28 14:24:39'),
('3c4f033dfb8fd4fe692715dda19ecd28',1,'','aCurrencies','arr','a:4:{i:0;s:23:"EUR@ 1.00@ ,@ .@ €@ 2";i:1;s:24:"GBP@ 0.8565@ .@  @ £@ 2";i:2;s:40:"CHF@ 1.4326@ ,@ .@ <small>CHF</small>@ 2";i:3;s:23:"USD@ 1.2994@ .@  @ $@ 2";}','2021-05-28 14:24:39'),
('8b831f739c5d16cf4571b14a76006568',1,'','aSEOReservedWords','arr','a:7:{i:0;s:5:\"admin\";i:1;s:4:\"core\";i:2;s:6:\"export\";i:3;s:7:\"modules\";i:4;s:3:\"out\";i:5;s:5:\"setup\";i:6;s:5:\"views\";}','2021-05-28 14:24:39'),
('a1544b76735f0d486.95460273',1,'','aHomeCountry','arr','a:1:{i:0;s:26:\"a7c40f631fc920687.20179984\";}','2021-05-28 14:24:39'),
('6ec7ea4f386eff9883b9fde0f040e559',1,'','bl_showVouchers','bool','1','2021-05-28 14:24:39');

REPLACE INTO oxconfig (OXID, OXSHOPID, OXVARNAME, OXVARTYPE, OXVARVALUE) SELECT
MD5(RAND()), 2, OXVARNAME, OXVARTYPE, OXVARVALUE from oxconfig WHERE OXSHOPID=1;

INSERT INTO `oxattribute` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXPOS`, `OXTIMESTAMP`, `OXDISPLAYINBASKET`) VALUES
('8a142c3e9cd961518.80299776',901,1,'Design','Design','','',0,'2016-07-19 14:38:25',1),
('8a142c3ee0edb75d4.80743302',902,1,'Anzeige','Display','','',0,'2016-07-19 14:38:25',1),
('8a142c3f0a792c0c3.93013584',903,1,'Modell','Model','','',0,'2016-07-19 14:38:25',1),
('8a142c3f0e2cf1a34.78041155',904,1,'Material','Material','','',0,'2016-07-19 14:38:25',1),
('8a142c3f14ef22a14.79693851',905,1,'Einsatzbereich','Area of Application','','',0,'2016-07-19 14:38:25',1),
('d8842e3b7c5e108c1.63072778',906,1,'Beschaffenheit','Texture','','',0,'2016-07-19 14:38:25',1),
('943d32fd45d6eba3e5c8cce511cc0e74',907,1,'Größe','Size','','',0,'2016-07-19 14:38:25',1),
('9438ac75bac3e344628b14bf7ed82c15',908,1,'Farbe','Color','','',0,'2016-07-19 14:38:25',1),
('943e7f5d33e9a78d4b71906270e3d0c6',909,1,'Schnitt','Cut','','',0,'2016-07-19 14:38:25',1),
('6b6e77de7a04de54f1aa63cfeca2f487',910,1,'Washing','Washing','','',0,'2016-07-19 14:38:25',1),
('6b6bc9f9ab8b153d9bebc2ad6ca2aa13',911,1,'EU-Größe','EU-Size','','',0,'2016-07-19 14:38:25',1),
('6cf89d2d73e666457d167cebfc3eb492',912,1,'Lieferumfang','Included in delivery','','',0,'2016-07-19 14:38:25',1);

REPLACE INTO `oxobject2attribute` (`OXID`, `OXOBJECTID`, `OXATTRID`, `OXVALUE`, `OXPOS`, `OXVALUE_1`, `OXVALUE_2`, `OXVALUE_3`, `OXTIMESTAMP`) VALUES
('00548c6a19725fc1a0f0891c8e504b69',	'b56369b1fc9d7b97f9c5fc343b349ece',	'9438ac75bac3e344628b14bf7ed82c15',	'Blau',	9998,	'Blue',	'',	'',	'2022-11-22 10:44:50'),
('00548c6a19725fc1a0f0891c8e504b70',	'b56369b1fc9d7b97f9c5fc343b349ece',	'8a142c3e9cd961518.80299776',	'Modern',	9999,	'Modern',	'',	'',	'2022-11-22 10:44:50'),
('00548c6a19725fc1a0f0891c8e504b71',	'b56597806428de2f58b1c6c7d3e0e093',	'9438ac75bac3e344628b14bf7ed82c15',	'Grün',	9998,	'Green',	'',	'',	'2022-11-22 10:44:50');

INSERT INTO `oxattribute2shop` (`OXSHOPID`, `OXMAPOBJECTID`, `OXTIMESTAMP`) VALUES
(1,901,'2016-07-19 14:38:26'),
(1,902,'2016-07-19 14:38:26'),
(1,903,'2016-07-19 14:38:26'),
(1,904,'2016-07-19 14:38:26'),
(1,905,'2016-07-19 14:38:26'),
(1,906,'2016-07-19 14:38:26'),
(1,907,'2016-07-19 14:38:26'),
(1,908,'2016-07-19 14:38:26'),
(1,909,'2016-07-19 14:38:26'),
(1,910,'2016-07-19 14:38:26'),
(1,911,'2016-07-19 14:38:26'),
(1,912,'2016-07-19 14:38:26');

INSERT INTO `oxactions` (`OXID`, `OXSHOPID`, `OXTYPE`, `OXSORT`, `OXACTIVE`, `OXTITLE`) VALUES
('_test_second_shop_banner_1', 2, 3, 2, 1, 'subshop banner 1'),
('_test_second_shop_banner_2', 2, 3, 1, 1, 'subshop banner 2');

INSERT INTO `oxcontents` (`OXID`, `OXLOADID`, `OXSHOPID`, `OXSNIPPET`, `OXTYPE`, `OXACTIVE`, `OXACTIVE_1`, `OXPOSITION`, `OXTITLE`, `OXCONTENT`, `OXTITLE_1`, `OXCONTENT_1`, `OXACTIVE_2`, `OXTITLE_2`, `OXCONTENT_2`, `OXACTIVE_3`, `OXTITLE_3`, `OXCONTENT_3`, `OXCATID`, `OXFOLDER`, `OXTERMVERSION`, `OXTIMESTAMP`) VALUES
('4d4106027b63b623b2c4ee1ea6838d7f', 'graphqlcontenttemplate', 1, 1, 0, 1, 1, '', 'GraphQL content with template DE', 'GraphQL {% if true %}rendered {% endif %}content DE', 'GraphQL content with template EN', '', 0, '', '', 0, '', '', NULL, 'CMSFOLDER_USERINFO', '', '2020-05-20 11:08:32'),
('9f825347decfdb7008d162700be95dc1', 'graphqlcontentvcms', 1, 1, 0, 1, 1, '', 'GraphQL content with VCMS template DE', '{% veparse %}[row][col size="12" offset="0" class=""][text]GraphQL VCMS {% if true %}rendered {% endif %}content DE[/text][/col][/row][{% endveparse %}', 'GraphQL content with template EN', '', 0, '', '', 0, '', '', NULL, 'CMSFOLDER_USERINFO', '', '2020-05-20 11:08:32');

UPDATE `oxcountry` SET `oxorder` = 1, `oxactive` = 1 where `OXID` = 'a7c40f631fc920687.20179984';
UPDATE `oxcountry` SET `oxorder` = 2, `oxactive` = 1 where `OXID` = '8f241f11096877ac0.98748826';
UPDATE `oxcountry` SET `oxorder` = 3, `oxactive` = 1 where `OXID` = 'a7c40f6321c6f6109.43859248';
UPDATE `oxcountry` SET `oxorder` = 4, `oxactive` = 1 where `OXID` = 'a7c40f6320aeb2ec2.72885259';
UPDATE `oxcountry` SET `oxorder` = 5, `oxactive` = 1 where `OXID` = 'a7c40f632a0804ab5.18804076';

INSERT INTO `oxmanufacturers` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXACTIVE`, `OXICON`, `OXTITLE`, `OXSHORTDESC`, `OXTITLE_1`, `OXSHORTDESC_1`, `OXTITLE_2`, `OXSHORTDESC_2`, `OXTITLE_3`, `OXSHORTDESC_3`, `OXSHOWSUFFIX`, `OXTIMESTAMP`) VALUES
('90a8a18dd0cf0e7aec5238f30e1c6106',901,1,1,'naish_1_mico.png','Naish','','Naish','','','','','',0,'2016-07-19 14:38:26'),
('9434afb379a46d6c141de9c9e5b94fcf',902,1,1,'logo3_ico.png','Kuyichi','Eine stilbewusste Marke','Kuyichi','A style conscious brand','','','','',0,'2016-07-19 14:38:26'),
('dc5ec524a9aa6175cf7a498d70ce510a',903,1,1,'npx_logo_1_mico.png','NPX','','NPX','','','','','',0,'2016-07-19 14:38:26'),
('dc59459d4d67189182c53ed0e4e777bc',904,1,0,'','Flyboards','','Flyboards','','','','','',0,'2016-07-19 14:38:26'),
('90a0b84564cde2394491df1c673b6aa0',905,1,1,'ion_logo_black_1_mico.png','ION','','ION','','','','','',0,'2016-07-19 14:38:26'),
('90a3eccf9d7121a9ca7d659f29021b7a',906,1,1,'cabrinha_logo2011_1_mico.png','Cabrinha','','Cabrinha','','','','','',0,'2016-07-19 14:38:26'),
('dc50589ad69b6ec71721b25bdd403171',907,1,0,'','Flysurfer','','Flysurfer','','','','','',0,'2016-07-19 14:38:26'),
('adc566c366db8eaf30c6c124a09e82b3',908,1,1,'core_logo_1_mico.png','Core Kiteboarding','','Core Kiteboarding','','','','','',0,'2016-07-19 14:38:26'),
('adc6df0977329923a6330cc8f3c0a906',909,1,1,'lf_kite_logo_1_mico.png','Liquid Force','','Liquid Force Kite','','','','','',0,'2016-07-19 14:38:26'),
('adca6aa4df3f95b6b46e28d4fc5855ba',910,1,0,'','Spleene','','Spleene','','','','','',0,'2016-07-19 14:38:26'),
('adca51c88a3caa1c7b939fd6a229ae3a',911,1,0,'','RRD','','RRD','','','','','',0,'2016-07-19 14:38:26'),
('3a97c94553428daed76ba83e54d3876f',912,1,1,'big_matsol_1_mico.png','Big Matsol','','Big Matsol','','','','','',0,'2016-07-19 14:38:26'),
('3a909e7c886063857e86982c7a3c5b84',913,1,1,'mauirippers_1_mico.png','Mauirippers','','Mauirippers','','','','','',0,'2016-07-19 14:38:26'),
('3a9fd0ec4b41d001e770b1d2d7af3e73',914,1,1,'mikejucker_hawaii_1_mico.png','Jucker Hawaii','','Jucker Hawaii','','','','','',0,'2016-07-19 14:38:26'),
('oiaf6ab7e12e86291e86dd3ff891fe40',915,1,1,'oreilly_1_mico.png','O\'Reilly','','O\'Reilly','','','','','',1,'2016-07-19 14:38:26');

INSERT INTO `oxmanufacturers2shop` (`OXSHOPID`, `OXMAPOBJECTID`, `OXTIMESTAMP`) VALUES
(1,901,'2016-07-19 14:38:26'),
(1,902,'2016-07-19 14:38:26'),
(1,903,'2016-07-19 14:38:26'),
(1,904,'2016-07-19 14:38:26'),
(1,905,'2016-07-19 14:38:26'),
(1,906,'2016-07-19 14:38:26'),
(1,907,'2016-07-19 14:38:26'),
(1,908,'2016-07-19 14:38:26'),
(1,909,'2016-07-19 14:38:26'),
(1,910,'2016-07-19 14:38:26'),
(1,911,'2016-07-19 14:38:26'),
(1,912,'2016-07-19 14:38:26'),
(1,913,'2016-07-19 14:38:26'),
(1,914,'2016-07-19 14:38:26'),
(1,915,'2016-07-19 14:38:26');


INSERT INTO `oxcategories` (`OXID`, `OXMAPID`, `OXPARENTID`, `OXLEFT`, `OXRIGHT`, `OXROOTID`, `OXSORT`, `OXACTIVE`, `OXHIDDEN`, `OXSHOPID`, `OXTITLE`, `OXDESC`, `OXLONGDESC`, `OXTHUMB`, `OXTHUMB_1`, `OXTHUMB_2`, `OXTHUMB_3`, `OXEXTLINK`, `OXTEMPLATE`, `OXDEFSORT`, `OXDEFSORTMODE`, `OXPRICEFROM`, `OXPRICETO`, `OXACTIVE_1`, `OXTITLE_1`, `OXDESC_1`, `OXLONGDESC_1`, `OXACTIVE_2`, `OXTITLE_2`, `OXDESC_2`, `OXLONGDESC_2`, `OXACTIVE_3`, `OXTITLE_3`, `OXDESC_3`, `OXLONGDESC_3`, `OXICON`, `OXPROMOICON`, `OXVAT`, `OXSKIPDISCOUNTS`, `OXSHOWSUFFIX`, `OXTIMESTAMP`) VALUES
('0f4fb00809cec9aa0910aa9c8fe36751',903,'943a9ba3050e78b443c16e043ae60ef3',2,3,'943a9ba3050e78b443c16e043ae60ef3',101,1,0,1,'Kites','','','','','','','','','',0,0,0,1,'Kites','','',0,'','','',0,'','','','kites_1_cico.jpg','',NULL,0,1,'2016-07-19 14:38:25'),
('fad4d7e2b47d87bb6a2773d93d4ae9be',924,'fad181ad64642b955becd0759345161e',25,26,'30e44ab83fdee7564.23264141',30203,1,0,1,'Accessoires','','','','','','','','','',0,0,0,1,'Accessories','','',0,'','','',0,'','','','access_1_cico.jpg','',NULL,0,1,'2016-07-19 14:38:25');

INSERT INTO `oxcategories2shop` (`OXSHOPID`, `OXMAPOBJECTID`, `OXTIMESTAMP`) VALUES
(1,903,'2016-07-19 14:38:26'),
(1,924,'2016-07-19 14:38:26');

REPLACE INTO `oxcategory2attribute` (`OXID`, `OXOBJECTID`, `OXATTRID`, `OXSORT`, `OXTIMESTAMP`) VALUES
('6f21e4486448cfe0382cb074190f186d',	'0f4fb00809cec9aa0910aa9c8fe36751',	'9438ac75bac3e344628b14bf7ed82c15',	0,	'2023-02-17 10:53:28'),
('6f21e4486448cfe0382cb074190f186f',	'0f4fb00809cec9aa0910aa9c8fe36751',	'8a142c3e9cd961518.80299776',	0,	'2023-02-17 10:53:28');

INSERT INTO `oxarticles` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXPARENTID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXARTNUM`, `OXEAN`, `OXDISTEAN`, `OXMPN`, `OXTITLE`, `OXSHORTDESC`, `OXPRICE`, `OXBLFIXEDPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXBPRICE`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXEXTURL`, `OXURLDESC`, `OXURLIMG`, `OXVAT`, `OXTHUMB`, `OXICON`, `OXPIC1`, `OXPIC2`, `OXPIC3`, `OXPIC4`, `OXPIC5`, `OXPIC6`, `OXPIC7`, `OXPIC8`, `OXPIC9`, `OXPIC10`, `OXPIC11`, `OXPIC12`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`, `OXDELIVERY`, `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXFILE`, `OXSEARCHKEYS`, `OXTEMPLATE`, `OXQUESTIONEMAIL`, `OXISSEARCH`, `OXISCONFIGURABLE`, `OXVARNAME`, `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`, `OXVARMINPRICE`, `OXVARMAXPRICE`, `OXVARNAME_1`, `OXVARSELECT_1`, `OXVARNAME_2`, `OXVARSELECT_2`, `OXVARNAME_3`, `OXVARSELECT_3`, `OXTITLE_1`, `OXSHORTDESC_1`, `OXURLDESC_1`, `OXSEARCHKEYS_1`, `OXTITLE_2`, `OXSHORTDESC_2`, `OXURLDESC_2`, `OXSEARCHKEYS_2`, `OXTITLE_3`, `OXSHORTDESC_3`, `OXURLDESC_3`, `OXSEARCHKEYS_3`, `OXBUNDLEID`, `OXFOLDER`, `OXSUBCLASS`, `OXSTOCKTEXT_1`, `OXSTOCKTEXT_2`, `OXSTOCKTEXT_3`, `OXNOSTOCKTEXT_1`, `OXNOSTOCKTEXT_2`, `OXNOSTOCKTEXT_3`, `OXSORT`, `OXSOLDAMOUNT`, `OXNONMATERIAL`, `OXFREESHIPPING`, `OXREMINDACTIVE`, `OXREMINDAMOUNT`, `OXAMITEMID`, `OXAMTASKID`, `OXVENDORID`, `OXMANUFACTURERID`, `OXSKIPDISCOUNTS`, `OXORDERINFO`, `OXPIXIEXPORT`, `OXPIXIEXPORTED`, `OXVPE`, `OXRATING`, `OXRATINGCNT`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`, `OXUPDATEPRICE`, `OXUPDATEPRICEA`, `OXUPDATEPRICEB`, `OXUPDATEPRICEC`, `OXUPDATEPRICETIME`, `OXISDOWNLOADABLE`, `OXSHOWCUSTOMAGREEMENT`, `OXHIDDEN`) VALUES
('f4f73033cf5045525644042325355732',1109,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','3788','','','','Transportcontainer THE BARREL','Wasserdichter Container für nasse Neos, kühle Getränke oder Klamotten',24.95,0,0,0,0,0,0,'',0,'','','',NULL,'mikejucker_textilcontainer_2_th.jpg','','mikejucker_textilcontainer_1.jpg','','','','','','','','','','','',0,49,1,'','','0000-00-00','2010-12-06','2016-07-19 14:38:25',0,0,0,'','transport, tasche, fass, getränke, kleidung','','',1,1,'',0,0,'',24.95,0,'','','','','','','Transport container BARREL','Waterproof container for wetsuits, cool beverages or gear','','transport, bag, barrel, beverages, gear, clothes','','','','','','','','','','','oxarticle','','','','','','',0,1,0,0,0,0,'','','','3a9fd0ec4b41d001e770b1d2d7af3e73',0,'',0,'0000-00-00 00:00:00',0,0,0,2,3,'WEEK',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('b56164c54701f07df14b141da197c207',1080,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','2402','','','','Bindung LIQUID FORCE TRANSIT BOOT','Bewährte Qualität in neuem Design',259,0,0,0,0,0,0,'',0,'','','',NULL,'','','liquid_force_transit_boot_2010_1.jpg','','','','','','','','','','','',0,0,1,'','','0000-00-00','2010-12-15','2020-04-06 12:45:08',0,0,0,'','bindung, boot, liquid force, transit','','',1,0,'',0,0,'',259,0,'','','','','','','Binding LIQUID FORCE TRANSIT BOOT','Proven quality in a new design','','bindung, boot, liquid force, transit','','','','','','','','','','','oxarticle','','','','','','',0,0,0,0,0,0,'','','','adc6df0977329923a6330cc8f3c0a906',0,'',0,'0000-00-00 00:00:00',0,0,0,3,5,'WEEK',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('b5685a5230f5050475f214b4bb0e239b',1086,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','2403','','','','Bindung LIQUID FORCE INDEX BOOT','Neues Design des Index Boot',159,0,0,0,0,0,0,'',0,'','','',NULL,'','','liquid_force_index_boot_2010_1.jpg','','','','','','','','','','','',0,0,1,'','','0000-00-00','2010-12-15','2020-04-06 12:45:15',0,0,0,'','bindung, boot, liquid force, index','','',1,0,'',0,0,'',159,0,'','','','','','','Binding LIQUID FORCE INDEX BOOT ','New design of the Index BOOT','','binding, boot, liquid force, index','','','','','','','','','','','oxarticle','','','','','','',0,0,0,0,0,0,'','','','adc6df0977329923a6330cc8f3c0a906',0,'',0,'0000-00-00 00:00:00',0,0,0,1,3,'DAY',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('d86e244c8114c8214fbf83da8d6336b3',1093,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','2102','','','','Wakeboard LIQUID FORCE SHANE','Das PRO-Modell von Shane Bonifay',389,0,0,0,0,0,0,'',0,'','','',NULL,'','','lf_shane_1.jpg','lf_shane_deck_1.jpg','lf_shane_bottom_1.jpg','','','','','','','','','',0,2,1,'','','0000-00-00','2010-12-08','2020-04-06 12:38:42',0,0,0,'','wakeboarding, wake, board, liquid force, shane, bonifay','','',1,0,'',0,0,'',389,0,'','','','','','','Wakeboard SHANE','The professional model by Shane Bonifay','','wakeboarding, wake, board, liquid force, shane, bonifay','','','','','','','','','','','oxarticle','','','','','','',0,1,0,0,0,0,'','','','adc6df0977329923a6330cc8f3c0a906',0,'',0,'0000-00-00 00:00:00',0,4,1,1,3,'DAY',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('b56597806428de2f58b1c6c7d3e0e093',1083,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','1211','','','','Kite NBK EVO','Die EVOlution geht weiter',699,0,0,0,0,0,0,'',0,'','','',NULL,'','','nkb_evo_2010_1.jpg','','','','','','','','','','','',0,10,1,'','','0000-00-00','2010-12-15','2020-04-06 12:39:58',0,0,0,'','kite, nbk, evo, kiteboarding','','',1,0,'',0,0,'',699,0,'','','','','','','Kite NBK EVO','The EVOlution goes on','','kite, nbk, evo, kiteboarding','','','','','','','','','','','oxarticle','','','','','','',0,2,0,0,0,0,'','','','',0,'',0,'0000-00-00 00:00:00',0,5,1,2,3,'DAY',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('_test_product_for_rating_avg',    9999,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','987', '','','','Product 987','',10,0,0,0,0,0,0,'', 0,'','', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:25:26', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, '', 10, 10, '', '', '', '', '', '', 'Product 1', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '','','', 0,'',0, '0000-00-00 00:00:00', 0, 2, 2, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('_test_product_for_rating_5_',     1234,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','555', '', '', '', 'Product 5', '', 10, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:25:26', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, '', 10, 10, '', '', '', '', '', '', 'Product 1', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '',  '', '', 0,'', 0, '0000-00-00 00:00:00', 0,  0, 0, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('_test_product_wp1_',             6666,1,'',1,'0000-00-00 00:00:00', '0000-00-00 00:00:00', '123', '', '', '', 'Product wp1', '', 15, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:25:26', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, '', 10, 10, '', '', '', '', '', '', 'Product 5', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '0', '', '', 0, '', 0, '0000-00-00 00:00:00', 1, 0, 0, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('_test_product_wp2_',             7777,2,'',1,'0000-00-00 00:00:00', '0000-00-00 00:00:00', '213', '', '', '', 'Product wp2', '', 15, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:25:26', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, '', 10, 10, '', '', '', '', '', '', 'Product 5', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '0', '', '', 0, '', 0, '0000-00-00 00:00:00', 1, 0, 0, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('dc5ffdf380e15674b56dd562a7cb6aec',1102,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','3503','','','','Kuyichi Ledergürtel JEVER','Ledergürtel, unisex',29.9,0,0,0,0,0,39.9,'',0,'','','',NULL,'','','p1170221_1.jpg','p1170222_1.jpg','','','','','','','','','','',0,15,1,'','','0000-00-00','2010-12-10','2016-07-19 14:38:25',0,0,0,'','kuyichi, leder, ledergürtel, unisex, used','','',1,0,'',0,0,'',29.9,0,'','','','','','','Kuyichi leather belt JEVER','Leather belt, unisex','','kuyichi, leather, leather belt, unisex, used','','','','','','','','','','','oxarticle','','','','','','',0,3,0,0,0,0,'','','a57c56e3ba710eafb2225e98f058d989','9434afb379a46d6c141de9c9e5b94fcf',0,'',0,'0000-00-00 00:00:00',0,0,1,1,2,'DAY',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('058e613db53d782adfc9f2ccb43c45fe',906,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','2401','','','','Bindung O\'BRIEN DECADE CT','Geringes Gewicht, beste Performance!',359,0,0,0,0,0,399,'',0,'','','',NULL,'','','obrien_decade_ct_boot_2010_1.jpg','','','','','','','','','','','',0,16,1,'','','0000-00-00','2010-12-06','2020-04-06 12:39:47',0,0,0,'','bindung, decade, schuh, wakeboarding','','',1,0,'',0,0,'',359,0,'','','','','','','Binding O\'BRIEN DECADE CT','Less weight, best performance!','','binding, decade, boot, wakeboarding','','','','','','','','','','','oxarticle','','','','','','',0,1,0,0,0,0,'','','','',0,'',0,'0000-00-00 00:00:00',0,0,0,4,5,'WEEK',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('_test_product_wished_price_3_', 3333,1, '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '333', '', '', '', 'Product 3', '', 10, 1, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:25:26', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, '', 10, 10, '', '', '', '', '', '', 'Product 1', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', 0, '', 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('_test_product_wished_price_4_', 4444,1, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '444', '', '', '', 'Product 4', '', 10, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:25:26', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, '', 10, 10, '', '', '', '', '', '', 'Product 1', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', 0, '', 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('_test_product_5_',             5555,2, '',  1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '555', '', '', '', 'Product 5', '', 15, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:25:26', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, '', 10, 10, '', '', '', '', '', '', 'Product 5', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '0', '', '', 0, '', 0, '0000-00-00 00:00:00', 1, 0, 0, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('_test_product_77',             7721, 2, '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '721', '', '', '', 'Product 721', '', 15, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:25:26', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, '', 10, 10, '', '', '', '', '', '', 'Product 5', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '0', '', '', 0, '', 0, '0000-00-00 00:00:00', 1, 0, 0, 0, 0, '', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('b56369b1fc9d7b97f9c5fc343b349ece',1081,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','1208','0012345600','00004567','coregts123','Kite CORE GTS','Die Sportversion des GT',879,0,0,0,0,0,999,'',0,'','','',NULL,'','','core_gts_1.jpg','','','','','','','','','','','',0,12,1,'','','0000-00-00','2010-12-15','2016-07-19 14:38:25',0,0,0,'','kite, core, gts, kiteboarding','','',1,0,'',0,0,'',879,0,'','','','','','','Kite CORE GTS','The sports version of the GT','','kite, core, gts, kiteboarding','','','','','','','','','','','oxarticle','','','','','','',0,3,0,0,0,0,'','','','adc566c366db8eaf30c6c124a09e82b3',0,'',0,'0000-00-00 00:00:00',0,5,2,1,2,'WEEK',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('_test_product_for_basket',     2123, 1,'', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '621', '', '', '', 'Product 621', '', 10, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:25:26', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, '', 10, 10, '', '', '', '', '', '', 'Product 1', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '0', '', '', 0,'', 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 1, 1, 0),
('_test_active_main_bundle',        1118, 1, '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '111', '', '', '', 'Product 1', '', 10, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:25:26', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, '', 10, 10, '', '', '', '', '', '', 'Product 1', '', '', '', '', '', '', '', '', '', '', '', '_test_inactive_bundle', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '0', '', '', 0, '', 0,'0000-00-00 00:00:00', 0, 0, 0, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('_test_inactive_bundle',           1119, 1, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '222', '', '', '', 'Product 2', '', 20, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:26:20', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, '', 20, 20, '', '', '', '', '', '', 'Product 2', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '0', '', '', 0, '', 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('531b537118f5f4d7a427cdb825440922',956,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','3570','','','','Kuyichi Jeans ANNA','Lässige Damenjeans von Kuyichi',99.9,0,0,0,0,0,0,'',0,'','','',NULL,'front(4)_v_th_th.jpg','front(4)_v_ico_ico.jpg','front(4)_v_pi.jpg','','','','','','','','','','','',0,17,1,'','','0000-00-00','2009-12-14','2016-07-19 14:38:25',0,0,0,'','jeans,anna,kuyichi,lässig,locker','','',1,0,'Größe | Farbe',123,36,'',92.9,109.9,'Size | Color','','','','','','Kuyichi Jeans ANNA','Cool lady jeans by Kuyichi','','jeans,anna,kuyichi,cool,casual','','','','','','','','','','','oxarticle','','','','','','',0,0,0,0,0,0,'','','a57c56e3ba710eafb2225e98f058d989','9434afb379a46d6c141de9c9e5b94fcf',0,'',0,'0000-00-00 00:00:00',0,0,0,4,5,'WEEK',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('6b6efaa522be53c3e86fdb41f0542a8a',1071,1,'531b537118f5f4d7a427cdb825440922',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','0702-85-853-1-1','','','','','Lässige Damenjeans von Kuyichi',99.9,0,0,0,0,0,0,'',0,'','','',NULL,'thumb_th(4)(2)_th.jpg','icon_ico(4)(2)_ico.jpg','front_z1(4)(2).jpg','back_z2(4)(2).jpg','detail1_z3(4)(2).jpg','detail2_z4(4)(2).jpg','','','','','','','','',0,15,1,'','','0000-00-00','0000-00-00','2016-07-19 14:38:25',0,0,0,'','jeans,anna,kuyichi,lässig,locker','','',0,0,'',0,0,'W 30/L 30 | Blau',0,0,'','W 30/L 30 | Blue ','','','','','','Cool lady jeans by Kuyichi','','jeans,anna,kuyichi,cool,casual','','','','','','','','','','','','','','','','','',10101,0,0,0,0,0,'','','a57c56e3ba710eafb2225e98f058d989','9434afb379a46d6c141de9c9e5b94fcf',0,'',0,'0000-00-00 00:00:00',0,0,0,2,4,'DAY',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('_test_product_with_variant', 8888, 1, '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1402', '', '', '', 'Parentproduct 631', '', 159, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2020-05-25 09:25:26', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 1, '', 10, 10, '', '', '', '', '', '', 'Parentproduct 631', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '0', '', '', 0, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('_test_variant_for_product', 8889, 1, '_test_product_with_variant', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '1414', '', '', '', 'Variant 1', '', 10, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '2020-05-25', '2022-04-14 13:07:46', 0, 0, 0, '', '', '', '', 1, 0, '', 0, 0, 'Größe', 0, 0, '', '', '', '', '', '', 'Variant 1', '', '', '', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '0', '', '', 0, 0, 0, '0000-00-00 00:00:00',  0, 0, 0, 0, 0, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('f4f2d8eee51b0fd5eb60a46dff1166d8',1108,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','1401','','','','Trapez ION SOL KITE','Neues Damen Freestyle-Trapez mit einer schlank geschnittenen Outline',129,0,0,0,0,0,169,'',0,'','','',NULL,'','','ion_sol_kite_waist_2011_1.jpg','','','','','','','','','','','',0,3,1,'','','0000-00-00','2010-12-06','2020-04-06 12:39:03',0,0,0,'','trapez, hüfttrapez, sol kite','','',1,0,'',0,0,'',129,0,'','','','','','','Harness SOL KITE','A new ladies freestyle harness with a lean cut outline','','harness, hip harness, sol kite','','','','','','','','','','','oxarticle','','','','','','',0,1,0,0,0,0,'','','','90a0b84564cde2394491df1c673b6aa0',0,'',0,'0000-00-00 00:00:00',0,0,0,3,5,'WEEK',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('058de8224773a1d5fd54d523f0c823e0',905,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','1302','','','','Kiteboard CABRINHA CALIBER','Freestyle und Freeride Board',479,0,0,0,0,0,0,'',0,'','','',NULL,'','','cabrinha_caliber_2011.jpg','cabrinha_caliber_2011_deck.jpg','cabrinha_caliber_2011_bottom.jpg','','','','','','','','','',0,13,1,'','','0000-00-00','2010-12-06','2020-04-06 12:39:42',0,0,0,'','kiteboard, kite, board, caliber, cabrinha','','',1,0,'',0,0,'',479,0,'','','','','','','Kiteboard CABRINHA CALIBER','Freestyle und Freeride Board','','kiteboard, kite, board, caliber, cabrinha','','','','','','','','','','','oxarticle','','','','','','',0,2,0,0,0,0,'','','','90a3eccf9d7121a9ca7d659f29021b7a',0,'',0,'0000-00-00 00:00:00',0,0,0,3,4,'DAY',0,0,0,0,'0000-00-00 00:00:00',0,1,0),
('oiaa81b5e002fc2f73b9398c361c0b97',1117,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','10101','978386899153','','','Online-Shops mit OXID eShop','In diesem Buch erfahren die Benutzer des OXID eShop, wie sie ihren eigenen Online-Shop installieren, konfigurieren, mit Produkten bestücken und betreiben - inkl. verschiedener Zahlungs- und Versandarten sowie der Einbindung von Social Media-Funktionen.',0,0,0,0,0,0,0,'',0,'','','',7,'','','oxid_book_cover_1.jpg','','','','','','','','','','','',0,600,1,'','','0000-00-00','2012-04-25','2016-07-19 14:38:25',0,0,0,'','OXID, Buch, download, O\'Reilley','','',1,0,'',0,0,'',0,0,'','','','','','','Online shops with OXID eShop','In this book, users of OXID eShop learn how to install, to configure and run their own online store incl. different payment and delivery methods and social media implementations.','','OXID, book, download, O\'Reilley','','','','','','','','','','','','','','','','','',1,0,0,1,0,0,'','0','','oiaf6ab7e12e86291e86dd3ff891fe40',0,'',0,'0000-00-00 00:00:00',1,0,0,3,4,'WEEK',0,0,0,0,'0000-00-00 00:00:00',1,1,0),
('6b63ed599fcfa07768dbfbd93991543b', 991, 1, '6b66d82af984e5ad46b9cb27b1ef8aae', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '85-8573-846-8-4', '', '', '', '', 'Spitzenjeans in verschiedenen Waschungen', 89.9, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, 'thumb_th(9)(1)_th.jpg', 'icon_ico(9)(1)_ico.jpg', 'front_z1(9)(2).jpg', 'back_z2(9)(2).jpg', 'detail1_z3(9)(2).jpg', 'detail2_z4(9)(2).jpg', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '0000-00-00', '2016-07-19 14:38:25', 0, 0, 0, '', 'kuyichi,jeans,dunkel,gerade,hüftjeans', '', '', 0, 0, '', 0, 0, 'W 32/L 32 | Dark Blue | Bangle Blue', 0, 0, '', 'W 32/L 32 | Dark Blue | Bangle Blue ', '', '', '', '', '', 'Leading jeans for repeated washing', '', 'kuyichi,jeans,dark,straight', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 13004, 0, 0, 0, 0, 0, '', '', 'a57c56e3ba710eafb2225e98f058d989', '9434afb379a46d6c141de9c9e5b94fcf', 0, '', 0, '0000-00-00 00:00:00', 0, 0, 0, 1, 3, 'DAY', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('6b66d82af984e5ad46b9cb27b1ef8aae', 1017, 1, '', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '3572', '', '', '', 'Kuyichi Jeans SUGAR', 'Spitzenjeans in verschiedenen Waschungen', 89.9, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, 'thumb_th(9).jpg', 'icon_ico(9).jpg', 'front_z1(9).jpg', 'back_z2(9).jpg', 'detail1_z3(9).jpg', 'detail2_z4(9).jpg', '', '', '', '', '', '', '', '', 0, 14, 1, '', '', '0000-00-00', '2009-12-17', '2023-07-17 13:18:40', 0, 0, 0, '', 'kuyichi,jeans,dunkel,gerade,hüftjeans', '', '', 1, 0, 'Größe | Farbe | Washing', 5, 5, '', 89.9, 89.9, 'Size | Color | Washing', '', '', '', '', '', 'Kuyichi Jeans SUGAR', 'Leading jeans for repeated washing', '', 'kuyichi,jeans,dark,straight', '', '', '', '', '', '', '', '', '', '', 'oxarticle', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, '', '', 'a57c56e3ba710eafb2225e98f058d989', '9434afb379a46d6c141de9c9e5b94fcf', 0, '', 0, '0000-00-00 00:00:00', 0, 0, 0, 3, 4, 'WEEK', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('6b66f4b02ad619cdadb7ea04b6c19cc2', 1018, 1, '6b66d82af984e5ad46b9cb27b1ef8aae', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '85-8573-846-5-4-3', '', '', '', '', '', 89.9, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '0000-00-00', '2023-07-17 13:18:15', 0, 0, 0, '', '', '', '', 0, 0, '', 0, 0, 'W 31/L 32 | Dark Blue | Predded Green', 0, 0, '', 'W 31/L 32 | Dark Blue | Predded Green ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 10004, 0, 0, 0, 0, 0, '', '', '', '', 0, '', 0, '0000-00-00 00:00:00', 0, 0, 0, 1, 2, 'WEEK', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('6b6b42499614ce3bfbee01f6eaba2f30', 1043, 1, '6b66d82af984e5ad46b9cb27b1ef8aae', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '85-8573-846-12-4', '', '', '', '', 'Spitzenjeans in verschiedenen Waschungen', 89.9, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, 'thumb_th(9)(2)_th.jpg', 'icon_ico(9)(2)_ico.jpg', 'front_z1(9)(3).jpg', 'back_z2(9)(3).jpg', 'detail1_z3(9)(3).jpg', 'detail2_z4(9)(3).jpg', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '0000-00-00', '2016-07-19 14:38:25', 0, 0, 0, '', 'kuyichi,jeans,dunkel,gerade,hüftjeans', '', '', 0, 0, '', 0, 0, 'W 34/L 34 | Dark Blue | Bangle Blue', 0, 0, '', 'W 34/L 34 | Dark Blue | Bangle Blue ', '', '', '', '', '', 'Leading jeans for repeated washing', '', 'kuyichi,jeans,dark,straight', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 17004, 0, 0, 0, 0, 0, '', '', 'a57c56e3ba710eafb2225e98f058d989', '9434afb379a46d6c141de9c9e5b94fcf', 0, '', 0, '0000-00-00 00:00:00', 0, 0, 0, 1, 3, 'WEEK', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('6b6b9f89cb8decee837d1a4c60742875', 1047, 1, '6b66d82af984e5ad46b9cb27b1ef8aae', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '85-8573-846-6-4', '', '', '', '', 'Spitzenjeans in verschiedenen Waschungen', 89.9, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, 'thumb_th(9)_th.jpg', 'icon_ico(9)_ico.jpg', 'front_z1(9)(1).jpg', 'back_z2(9)(1).jpg', 'detail1_z3(9)(1).jpg', 'detail2_z4(9)(1).jpg', '', '', '', '', '', '', '', '', 0, 5, 1, '', '', '0000-00-00', '0000-00-00', '2016-07-19 14:38:25', 0, 0, 0, '', 'kuyichi,jeans,dunkel,gerade,hüftjeans', '', '', 0, 0, '', 0, 0, 'W 31/L 34 | Dark Blue | Bangle Blue', 0, 0, '', 'W 31/L 34 | Dark Blue | Bangle Blue ', '', '', '', '', '', 'Leading jeans for repeated washing', '', 'kuyichi,jeans,dark,straight', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 11004, 0, 0, 0, 0, 0, '', '', 'a57c56e3ba710eafb2225e98f058d989', '9434afb379a46d6c141de9c9e5b94fcf', 0, '', 0, '0000-00-00 00:00:00', 0, 0, 0, 1, 3, 'WEEK', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0),
('6b6eb34fcceb69efafddaeeedb81d9a4', 1066, 1, '6b66d82af984e5ad46b9cb27b1ef8aae', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '85-8573-846-5-4', '', '', '', '', '', 89.9, 0, 0, 0, 0, 0, 0, '', 0, '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 1, '', '', '0000-00-00', '0000-00-00', '2023-07-17 13:18:55', 0, 0, 0, '', '', '', '', 0, 0, '', 0, 0, 'W 31/L 32 | Dark Red | Bangle Blue', 0, 0, '', 'W 31/L 32 | Dark Red | Bangle Blue ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 10004, 0, 0, 0, 0, 0, '', '', '', '', 0, '', 0, '0000-00-00 00:00:00', 0, 0, 0, 2, 3, 'WEEK', 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0);

INSERT INTO `oxarticles2shop` (`OXSHOPID`, `OXMAPOBJECTID`, `OXTIMESTAMP`) VALUES
(1,1109,'2016-07-19 14:38:26'),
(1,1080,'2016-07-19 14:38:26'),
(1,1086,'2016-07-19 14:38:26'),
(1,1093,'2016-07-19 14:38:26'),
(1,1083,'2016-07-19 14:38:26'),
(1,9999,'2016-07-19 14:38:26'),
(1,1234,'2016-07-19 14:38:26'),
(2,1234,'2020-01-01 00:00:00'),
(1,6666,'2016-07-19 14:38:26'),
(2,7777,'2016-07-19 14:38:26'),
(1,1102,'2016-07-19 14:38:26'),
(1,906,'2016-07-19 14:38:26'),
(1,3333,'2016-07-19 14:38:26'),
(1,4444,'2016-07-19 14:38:26'),
(2,5555,'2016-07-19 14:38:26'),
(1,1081,'2016-07-19 14:38:26'),
(1,2123,'2016-07-19 14:38:26'),
(2,2123,'2020-01-01 00:00:00'),
(1,1118,'2020-01-01 00:00:00'),
(1,1119,'2020-01-01 00:00:00'),
(1,956,'2020-01-01 00:00:00'),
(1,1071,'2020-01-01 00:00:00'),
(1,8888,'2020-01-01 00:00:00'),
(1,8889,'2020-01-01 00:00:00'),
(1,1108,'2020-01-01 00:00:00'),
(1,905,'2020-01-01 00:00:00'),
(1,1117,'2020-01-01 00:00:00'),
(1,991,'2020-01-01 00:00:00'),
(1,1017,'2020-01-01 00:00:00'),
(1,1018,'2020-01-01 00:00:00'),
(1,1043,'2020-01-01 00:00:00'),
(1,1047,'2020-01-01 00:00:00'),
(1,1066,'2020-01-01 00:00:00'),
(2,7721,'2020-01-01 00:00:00');

INSERT INTO `oxobject2category` (`OXID`, `OXSHOPID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`, `OXTIMESTAMP`) VALUES
('b56a5554a328d329aa2b2e65b6e870e0',1,'b56369b1fc9d7b97f9c5fc343b349ece','0f4fb00809cec9aa0910aa9c8fe36751',0,1292398522,'2016-07-19 14:38:26'),
('b56a5554a328d329aa2b2e65b6e870e1',1,'b56597806428de2f58b1c6c7d3e0e093','0f4fb00809cec9aa0910aa9c8fe36751',0,1292398522,'2016-07-19 14:38:26'),
('b27b75b0a0706ef5d6ca5d75353a8c53',1,'f4f981b0d9e34d2aeda82d79412480a4','fad4d7e2b47d87bb6a2773d93d4ae9be',0,1299483314,'2016-07-19 14:38:26'),
('dc5ccdc702cd905d24a58b1c1d26e4ab',1,'dc55b2b2e633527f9a8b2408a032f28f','fad4d7e2b47d87bb6a2773d93d4ae9be',0,1291971010,'2016-07-19 14:38:26'),
('dc5fe9b7d8c00d060f6a629e15fedaf0',1,'dc5ffdf380e15674b56dd562a7cb6aec','fad4d7e2b47d87bb6a2773d93d4ae9be',0,1291970285,'2016-07-19 14:38:26');

INSERT INTO `oxreviews` (`OXID`, `OXACTIVE`, `OXOBJECTID`, `OXTYPE`, `OXTEXT`, `OXUSERID`, `OXCREATE`, `OXLANG`, `OXRATING`, `OXTIMESTAMP`) VALUES
('94415306f824dc1aa2fce0dc4f12783d',0,'b56597806428de2f58b1c6c7d3e0e093','oxarticle','Fantastic kite with great performance!','e7af1c3b786fd02906ccd75698f4e6b9','2011-03-25 16:51:05',1,5,'2016-07-19 14:38:26'),
('bcb341381858129f7412beb11c827a25',0,'f4fe754e1692b9f79f2a7b1a01bb8dee','oxarticle','Solides Board mit coolem Design','e7af1c3b786fd02906ccd75698f4e6b9','2011-02-01 15:44:03',0,4,'2016-07-19 14:38:26'),
('e7af435915814c63c0d6e9084804ac04',0,'b56369b1fc9d7b97f9c5fc343b349ece','oxarticle','Dieser Kite ist sehr gut verarbeitet und überzeugt mit hervorragenden Allround-Eigenschaften. Vor allem aber im Freestyle-Bereich kommt seine Qualität voll zur Geltung. Ich kann ihn nur weiterempfehlen!','e7af1c3b786fd02906ccd75698f4e6b9','2011-02-01 09:03:56',0,5,'2016-07-19 14:38:26');

INSERT INTO `oxratings` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXTYPE`, `OXOBJECTID`, `OXRATING`) VALUES
('test_user_rating', 1, '245ad3b5380202966df6ff128e9eecaq', 'oxarticle', '_test_product_for_rating_avg', 3),
('test_rating_1_', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'oxarticle', '_test_product_for_rating_avg', 1);

INSERT INTO `oxvendor` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXACTIVE`, `OXICON`, `OXTITLE`, `OXSHORTDESC`, `OXTITLE_1`, `OXSHORTDESC_1`, `OXTITLE_2`, `OXSHORTDESC_2`, `OXTITLE_3`, `OXSHORTDESC_3`, `OXSHOWSUFFIX`, `OXTIMESTAMP`) VALUES
('a57c56e3ba710eafb2225e98f058d989', 902, 1, 1,'','www.true-fashion.com','Ethical style outlet','www.true-fashion.com','Ethical style outlet','','','','',0,'2016-07-19 14:38:26'),
('fe07958b49de225bd1dbc7594fb9a6b0', 903, 1, 1, '', 'https://fashioncity.com/de', 'Fashion city', 'https://fashioncity.com/en', 'Fashion city', '', '', '', '', 1, '2020-01-10 15:00:00'),
('05833e961f65616e55a2208c2ed7c6b8', 904, 1, 0, '', 'https://demo.com', 'Demo vendor', 'https://demo.com', 'Demo vendor', '', '', '', '', 1, '2020-01-10 15:00:00');

INSERT INTO `oxvendor2shop` (`OXSHOPID`, `OXMAPOBJECTID`, `OXTIMESTAMP`) VALUES
(1,902,'2016-07-19 14:38:26'),
(1,903,'2016-07-19 14:38:26'),
(1,904,'2016-07-19 14:38:26');

INSERT INTO `oxuserbaskets` (`OXID`, `OXUSERID`, `OXTITLE`, `OXPUBLIC`, `OEGQL_PAYMENTID`, `OEGQL_DELIVERYMETHODID`, `OEGQL_DELADDRESSID`) VALUES
('_test_basket_public', 'e7af1c3b786fd02906ccd75698f4e6b9', 'buy_these', true, 'oxidcashondel', null, null),
('_test_wish_list_public', 'e7af1c3b786fd02906ccd75698f4e6b9', 'wishlist', true, null, null, null),
('_test_wish_list_private', '245ad3b5380202966df6ff128e9eecaq', 'wishlist', false, null, null, null),
('test_make_wishlist_private', 'e7af1c3b786fd02906ccd75698f4e6b9', 'wishlist', true, null, null, null),
('_test_voucher_public', 'e7af1c3b786fd02906ccd75698f4e6b9', 'test_voucher', true, null, null, null),
('_test_basket_private', '245ad3b5380202966df6ff128e9eecaq', 'buy_these_later', false, null, null, null),
('_test_basket_private_ex', '309db395b6c85c3881fcb9b437a73dd6', 'buy_these_later', false, null, null, null),
('_test_noticelist_public', '_678d3b5380202966df6ff128e9eecaq', 'noticelist', true, null, null, null),
('_test_shop2_basket_public', '123ad3b5380202966df6ff128e9eecaq', 'buy_these', true, null, null, null),
('basket_otheruser', 'anotheruser', 'savedbasket', true, null, null, null),
('basket_otheruser_2', 'anotheruser', 'savedbasket', true, null, null, null),
('basket_user_3', 'standarduser', 'basketPayment', true, null, null, null),
('basket_user_address_payment', 'standarduser', 'basketPayment', true, 'oxiddebitnote', 'oxidstandard', 'address_user'),
('basket_payment', 'standarduser', 'basketPaymentMethod', true, 'oxiddebitnote', 'oxidstandard', 'address_user'),
('basket_payment_cost', 'standarduser', 'basketPaymentCost', true, 'oxidgraphql', '_deliveryset', 'address_user'),
('basket_shipping', 'standarduser', 'basketShippingMethod', true, 'oxiddebitnote', 'oxidstandard', 'address_user');

INSERT INTO `oxuserbasketitems` (`OXID`, `OXBASKETID`, `OXARTID`, `OXAMOUNT`, `OXSELLIST`, `OXPERSPARAM`) VALUES
('_test_basket_item_1', '_test_basket_public', '_test_product_for_basket', 1, 'N;', ''),
('_test_wish_list_item_2', '_test_wish_list_private', '_test_product_for_wish_list', 1, 'N;', ''),
('_test_voucherbasket_item_1', '_test_voucher_public', '_test_product_for_basket', 1, 'N;', ''),
('_test_basket_item_2', '_test_basket_private', '_test_product_for_basket', 1, 'N;', ''),
('_test_shop2_basket_item_1', '_test_shop2_basket_public', '_test_product_for_basket', 1, 'N;', ''),
('_test_basket_shipping_item_1', 'basket_shipping', 'dc5ffdf380e15674b56dd562a7cb6aec', 1, 'N;', ''),
('_test_basket_shipping_item_2', 'basket_shipping', 'f4f73033cf5045525644042325355732', 2, 'N;', ''),
('_test_basket_payment_item_1', 'basket_payment', 'dc5ffdf380e15674b56dd562a7cb6aec', 1, 'N;', ''),
('_test_basket_payment_cost_item_1', 'basket_payment_cost', 'f4f2d8eee51b0fd5eb60a46dff1166d8', 2, 'N;', '');

REPLACE INTO `oxuserpayments` (`OXID`, `OXUSERID`, `OXPAYMENTSID`, `OXVALUE`, `OXTIMESTAMP`) VALUES
('invoice_order_payment',  'e7af1c3b786fd02906ccd75698f4e6b9', 'oxidinvoice', '', '2020-09-11 08:15:00'),
('direct_debit_order_payment',  'e7af1c3b786fd02906ccd75698f4e6b9', 'oxiddebitnote', 'lsbankname__Pro Credit Bank@@lsblz__PRCBBGSF456@@lsktonr__DE89 3704 0044 0532 0130 00@@lsktoinhaber__Marc Muster@@', '2020-09-10 08:15:00');

INSERT INTO `oxorder` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`, `OXORDERNR`, `OXBILLCOMPANY`, `OXBILLEMAIL`, `OXBILLFNAME`,`OXBILLLNAME`, `OXBILLSTREET`, `OXBILLSTREETNR`, `OXBILLADDINFO`, `OXBILLUSTID`, `OXBILLUSTIDSTATUS`, `OXBILLCITY`,`OXBILLCOUNTRYID`, `OXBILLSTATEID`, `OXBILLZIP`, `OXBILLFON`, `OXBILLFAX`, `OXBILLSAL`, `OXDELCOMPANY`, `OXDELFNAME`,`OXDELLNAME`, `OXDELSTREET`, `OXDELSTREETNR`, `OXDELADDINFO`, `OXDELCITY`, `OXDELCOUNTRYID`, `OXDELSTATEID`, `OXDELZIP`,`OXDELFON`, `OXDELFAX`, `OXDELSAL`, `OXPAYMENTID`, `OXPAYMENTTYPE`, `OXTOTALNETSUM`, `OXTOTALBRUTSUM`, `OXTOTALORDERSUM`,`OXARTVAT1`, `OXARTVATPRICE1`, `OXARTVAT2`, `OXARTVATPRICE2`, `OXDELCOST`, `OXDELVAT`, `OXPAYCOST`, `OXPAYVAT`, `OXWRAPCOST`,`OXWRAPVAT`, `OXGIFTCARDCOST`, `OXGIFTCARDVAT`, `OXCARDID`, `OXCARDTEXT`, `OXDISCOUNT`, `OXEXPORT`, `OXBILLNR`, `OXBILLDATE`,`OXTRACKCODE`, `OXSENDDATE`, `OXREMARK`, `OXVOUCHERDISCOUNT`, `OXCURRENCY`, `OXCURRATE`, `OXFOLDER`, `OXTRANSID`, `OXPAYID`,`OXXID`, `OXPAID`, `OXSTORNO`, `OXIP`, `OXTRANSSTATUS`, `OXLANG`, `OXINVOICENR`, `OXDELTYPE`, `OXPIXIEXPORT`, `OXTIMESTAMP`,`OXISNETTOMODE`) VALUES
('8c726d3f42ff1a6ea2828d5f309de881',1,'e7af1c3b786fd02906ccd75698f4e6b9','2020-05-23 14:08:55',4,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','del company','Marcia','Pattern','Nebenstraße','123','del addinfo','Freiburg','a7c40f631fc920687.20179984','HH','79106','04012345678','04012345679','MRS','direct_debit_order_payment','oxiddebitnote',35.62,42.39,46.29,19,6.77,0,0,3.9,19,0,0,0,0,0,19,'','',0,0,'7664','2020-08-24','track','2020-08-24 11:11:14','please deliver as fast as you can',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,664,'oxidstandard',0,'2020-08-21 12:08:55',0),
('_019bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-01 11:41:41',100,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-01 09:41:41',0),
('_029bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-02 11:41:41',101,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-02 09:41:41',0),
('_039bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-03 11:41:41',102,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-03 09:41:41',0),
('_049bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-04 11:41:41',103,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-04 09:41:41',0),
('_059bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-05 11:41:41',104,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-05 09:41:41',0),
('_069bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-06 11:41:41',105,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-06 09:41:41',0),
('_079bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-07 11:41:41',106,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-07 09:41:41',0),
('_089bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-08 11:41:41',107,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-08 09:41:41',0),
('_099bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-09 11:41:41',108,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-09 09:41:41',0),
('_109bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-10 11:41:41',109,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-10 09:41:41',0),
('_119bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-11 11:41:41',110,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-11 09:41:41',0),
('_129bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-12 11:41:41',111,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-12 09:41:41',0),
('_139bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-13 11:41:41',112,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-13 09:41:41',0),
('_149bc776dd339a83d863c4f64693bb6',1,'_45ad3b5380202966df6ff128e9eecaq','2019-08-14 11:41:41',113,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-14 09:41:41',0),
('7f0b6ef39c4e76c04a0f75232489bb65',1,'245ad3b5380202966df6ff128e9eecaq','2020-08-28 11:04:14',200,'','user@oxid-esales.com','Marc','Muster','Hauptstr.','13','','',1,'Freiburg','a7c40f631fc920687.20179984','','79098','','','MR','','','','','','','','','','','','','','8ebefe11f18f4e6457d01ca9785d2c98','oxidcashondel',178.3,209.38,220.78,10,2.72,19,27.38,3.9,19,7.5,19,0,0,0,19,'','',123.4,0,'','2020-09-01','tracking_code','2020-09-02 12:12:12','',0.0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,0,'oxidstandard',0,'2020-08-28 09:04:14',0),
('8g0b6ef39c4e76c04a0f75232489bb65',1,'_678d3b5380202966df6ff128e9eecaq','2020-08-28 11:04:14',201,'','user@oxid-esales.com','Marc','Muster','Hauptstr.','13','','',1,'Freiburg','a7c40f631fc920687.20179984','','79098','','','MR','','','','','','','','','','','','','','8ebefe11f18f4e6457d01ca9785d2c98','oxidcashondel',178.3,209.38,220.78,10,2.72,19,27.38,3.9,19,7.5,19,0,0,0,19,'','',0,0,'','0000-00-00 00:00:00','','0000-00-00 00:00:00','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,0,'unknownshipping',0,'2020-08-28 09:04:14',0),
('_order_with_non_existing_product',1,'245ad3b5380202966df6ff128e9eecaq','2019-08-14 11:41:41',113,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-14 09:41:41',0),
('_order_with_deleted_product',     1,'245ad3b5380202966df6ff128e9eecaq','2019-08-14 11:41:41',113,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-14 09:41:41',0),
('7d090db46a124f48cb7e6836ceef3f66',1,'e7af1c3b786fd02906ccd75698f4e6b9','2011-03-30 10:55:13',1,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','bill vat id',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','7d011a153655ef215558cddd43dc65a8','oxidinvoice',1639.15,2108.39,1950.59,19,311.44,0,0,0,19,0,0,0,0,0,0,'','',157.8,0,'7661','2020-03-31','track_me','2020-08-24 11:11:11','Hier können Sie uns noch etwas mitteilen.',0,'EUR',1,'ORDERFOLDER_NEW','','','','2020-04-01 12:12:12',0,'','OK',0,661,'oxidstandard',0,'2020-08-21 09:39:46',0),
('8c69bc776dd339a83d863c4f64693bb6',1,'e7af1c3b786fd02906ccd75698f4e6b9','2019-08-21 11:41:41',2,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','bill vat id',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','','','','','','','','','','','','','','5b4b2226735704859055607e98a257e7','oxidcashondel',25.13,29.9,46.75,19,4.77,0,0,3.9,19,7.5,19,2.95,18.951612903226,2.5,19,'81b40cf076351c229.14252649','asdfasdf',0,0,'7662','2020-08-23','tick','2020-08-24 11:11:12','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,662,'oxidstandard',0,'2020-08-21 09:41:41',0),
('0c99bad495d00254a936ccee2391f763',1,'e7af1c3b786fd02906ccd75698f4e6b9','2020-04-22 14:07:12',3,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','bill vat id',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','del company','Marcia','Pattern','Nebenstraße','123','del addinfo','Freiburg','a7c40f631fc920687.20179984','HH','79106','04012345678','04012345679','MRS','c3260a603ed4e2d3b01981cbc05e8dfd','oxidinvoice',226.05,269,269,19,42.95,0,0,0,19,0,0,0,0,0,19,'','',0,0,'7663','2020-08-24','trick','2020-08-24 11:11:13','Hej, greetings to graphQL! ',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,663,'oxidstandard',0,'2020-08-21 12:07:12',0),
('0c99bad495d00254a936ccee2391f637',2,'e7af1c3b786fd02906ccd75698f4e6b9','2020-04-22 14:07:12',5,'bill company','billuser@oxid-esales.com','Marc','Muster','Hauptstr.','13','additional bill info','bill vat id',1,'Freiburg','a7c40f631fc920687.20179984','BW','79098','1234','4567','MR','del company','Marcia','Pattern','Nebenstraße','123','del addinfo','Freiburg','a7c40f631fc920687.20179984','HH','79106','04012345678','04012345679','MRS','invoice_order_payment','oxidinvoice',226.05,269,269,19,42.95,0,0,0,19,0,0,0,0,0,19,'','',0,0,'7663','2020-08-24','trick','2020-08-24 11:11:13','Hej, greetings to graphQL! ',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,663,'oxidstandard',0,'2020-08-21 12:07:12',0),
('85ecbd1d5e56172ff5af6917894d4a31',2,'123ad3b5380202966df6ff128e9eecaq','2015-07-02 07:31:37',6,'','user@oxid-esales.com','Marc','Muster','Hauptstr.','13','','',1,'Freiburg','a7c40f631fc920687.20179984','','79098','','','MR','','','','','','','','','','','','','','fada11bc485e15e5b999c7776ef90592','oxempty',8.4,10,10,19,1.6,0,0,0,19,0,0,0,0,0,19,'','',0,0,'','0000-00-00','','0000-00-00 00:00:00','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,0,'',0,'2020-09-02 07:31:37',0),
('85ecbd1d5e56172ff5af6917894d4a32',2,'245ad3b5380202966df6ff128e9eecaq','2015-07-02 07:31:37',7,'','otheruser@oxid-esales.com','Marc','Muster','Hauptstr.','13','','',1,'Freiburg','a7c40f631fc920687.20179984','','79098','','','MR','','','','','','','','','','','','','','fada11bc485e15e5b999c7776ef90592','oxempty',8.4,10,10,19,1.6,0,0,0,19,0,0,0,0,0,19,'','',0,0,'','0000-00-00','','0000-00-00 00:00:00','',0,'EUR',1,'ORDERFOLDER_NEW','','','','0000-00-00 00:00:00',0,'','OK',1,0,'',0,'2020-09-02 07:31:37',0);

UPDATE `oxorder` SET `OXBILLUSTID` = 'bill vat id', `OXBILLUSTIDSTATUS` = 1, `OXPIXIEXPORT` = 0 WHERE OXID = '8c726d3f42ff1a6ea2828d5f309de881';

REPLACE INTO `oxorderarticles` (`OXID`, `OXORDERID`, `OXAMOUNT`, `OXARTID`, `OXARTNUM`, `OXTITLE`, `OXSHORTDESC`, `OXSELVARIANT`, `OXNETPRICE`, `OXBRUTPRICE`, `OXVATPRICE`, `OXVAT`, `OXPERSPARAM`, `OXPRICE`, `OXBPRICE`, `OXNPRICE`, `OXWRAPID`, `OXEXTURL`, `OXURLDESC`, `OXURLIMG`, `OXTHUMB`, `OXPIC1`, `OXPIC2`, `OXPIC3`, `OXPIC4`, `OXPIC5`, `OXWEIGHT`, `OXSTOCK`, `OXDELIVERY`, `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXFILE`, `OXSEARCHKEYS`, `OXTEMPLATE`, `OXQUESTIONEMAIL`, `OXISSEARCH`, `OXFOLDER`, `OXSUBCLASS`, `OXSTORNO`, `OXORDERSHOPID`, `OXISBUNDLE`) VALUES
('1d48d8339e49f906700b520ceb4c79aa','7f0b6ef39c4e76c04a0f75232489bb65',1,'05848170643ab0deb9914566391c0c63','1402','Harness MADTRIXX','New freestyle harness with a lean cut outline','',133.61,159,25.39,19,'',159,159,133.61,'','','','','','ion_madtrixx_kite_waist_2011_1.jpg','','','','',0,15,'0000-00-00','2010-12-06','2020-08-28 09:04:14',0,0,0,'','harness, hip harness, madtrixx','','',1,'','oxarticle',0,1,0),
('6c220c5d926c5092035234285f3c8fc3','7f0b6ef39c4e76c04a0f75232489bb65',1,'058c7b525aad619d8b343c0ffada0247','400-03','Sticky Tape DACRON KITEFIX','ideal for small repairs of the kite','black',7.01,7.99,0.98,14,'',7.99,7.99,7.01,'','','','','','kitefix_self-adhesive_dacron_1.jpg','','','','',0,5,'0000-00-00','2010-12-06','2020-08-28 09:04:14',0,0,0,'','sticky tape, kite, repair, kitefix','','',0,'','oxarticle',0,1,0),
('b4b9f8dd0be567d6fcfc59d9b7bb266b','7f0b6ef39c4e76c04a0f75232489bb65',1,'dc5ffdf380e15674b56dd562a7cb6aec','3503','Kuyichi leather belt JEVER','Leather belt, unisex','',27.18,29.9,2.72,10,'',29.9,29.9,27.18,'','','','','','p1170221_1.jpg','p1170222_1.jpg','','','',0,15,'0000-00-00','2010-12-10','2020-08-28 09:04:14',0,0,0,'','kuyichi, leather, leather belt, unisex, used','','',1,'','oxarticle',0,1,0),
('f805daf76a1f8614a7972ab51c22634b','7f0b6ef39c4e76c04a0f75232489bb65',1,'f33d5bcc7135908fd36fc736c643aa1c','1506','KiteFix Glue GLUFIX (30g)','Specially developed for fixing kites','',10.5,12.49,1.99,19,'',12.49,12.49,10.5,'','','','','glufix_z1a_th_th.jpg','glufix_z1a.jpg','','','','',0,27,'0000-00-00','2011-03-24','2020-08-28 09:04:14',0,0,0,'','kite, kitefix, glue, glufix','','',1,'','oxarticle',0,1,0),
('677688370a4a64d8336107bcf174f330','_order_with_non_existing_product',1,'non_existing_product_id','621','Product 1','','',8.4,10,1.6,19,'',10,10,8.4,'','','','','','','','','','',0,0,'0000-00-00','2020-05-25','2015-07-02 07:31:37',0,0,0,'','','','',1,'','oxarticle',0,2,0),
('677688370a4a64d8336107bcf174f331','_order_with_deleted_product',1,'_test_product_for_basket','621','Product 1','','',8.4,10,1.6,19,'',10,10,8.4,'','','','','','','','','','',0,0,'0000-00-00','2020-05-25','2015-07-02 07:31:37',0,0,0,'','','','',1,'','oxarticle',0,2,0),
('c5b7fd8dff99f066c168cd720212075a','8c726d3f42ff1a6ea2828d5f309de881',1,'oiaa81b5e002fc2f73b9398c361c0b97','10101','Online shops with OXID eShop','','',0,0,0,10,'',0,0,0,'','','','','','oxid_book_cover_1.jpg','','','','',0,600,'0000-00-00','2012-04-25','2020-09-10 09:13:36',0,0,0,'','','','',1,'','',0,1,0),
('4ad5c368c9c7715ac800adb27e079ebe','7f0b6ef39c4e76c04a0f75232489bb65',1,'oiaa81b5e002fc2f73b9398c361c0b97','10101','Online shops with OXID eShop','','',0,0,0,10,'',0,0,0,'','','','','','oxid_book_cover_1.jpg','','','','',0,600,'0000-00-00','2012-04-25','2020-09-10 09:13:36',0,0,0,'','','','',1,'','',0,1,0),
('677688370a4a64d8336107bcf174fdeb','85ecbd1d5e56172ff5af6917894d4a31',1,'_test_product_for_basket','621','Product 1','','',8.4,10,1.6,19,'',10,10,8.4,'','','','','','','','','','',0,0,'0000-00-00','2020-05-25','2015-07-02 07:31:37',0,0,0,'','','','',1,'','oxarticle',0,2,0),
('677688370a4a64d8336107bcf174fde1','85ecbd1d5e56172ff5af6917894d4a32',1,'_test_product_for_basket','621','Product 1','','',8.4,10,1.6,19,'',10,10,8.4,'','','','','','','','','','',0,0,'0000-00-00','2020-05-25','2015-07-02 07:31:37',0,0,0,'','','','',1,'','oxarticle',0,2,0),
('7d010996ab5656e369a63cdccb5f56e7','7d090db46a124f48cb7e6836ceef3f66',1,'b56369b1fc9d7b97f9c5fc343b349ece','1208','Kite CORE GTS','Die Sportversion des GT','',738.65546218487,879,140.34453781513,19,'',879,879,738.65546218487,'','','','','0/core_gts_1_th.jpg','1/core_gts_1.jpg','2/nopic.jpg','3/nopic.jpg','4/nopic.jpg','5/nopic.jpg',0,13,'0000-00-00','2015-12-20','0000-00-00 00:00:00',0,0,0,'','kite, core, gts, kiteboarding','','',1,'','oxarticle',0,1,0),
('7d0375c56b9b6ae7f984f8a0d45ef990','7d090db46a124f48cb7e6836ceef3f66',1,'f33d5bcc7135908fd36fc736c643aa1c','1506','KiteFix Kleber GLUFIX (30g)','Speziell für Kites entwickelter Klebstoff','',10.495798319328,12.49,1.9942016806723,19,'',12.49,12.49,10.495798319328,'','','','','0/glufix_z1a_th_th.jpg','1/glufix_z1a.jpg','2/nopic.jpg','3/nopic.jpg','4/nopic.jpg','5/nopic.jpg',0,30,'0000-00-00','2024-03-20','0000-00-00 00:00:00',0,0,0,'','kite, kitefix, kleber, glufix','','',1,'','oxarticle',0,1,0),
('7d05c716386bea333d200d369078fc73','7d090db46a124f48cb7e6836ceef3f66',1,'b56597806428de2f58b1c6c7d3e0e093','1211','Kite NBK EVO','Die EVOlution geht weiter','',587.39495798319,699,111.60504201681,19,'',699,699,587.39495798319,'','','','','0/nkb_evo_2010_1_th.jpg','1/nkb_evo_2010_1.jpg','2/nopic.jpg','3/nopic.jpg','4/nopic.jpg','5/nopic.jpg',0,11,'0000-00-00','2015-12-20','2020-04-14 07:26:44',0,0,0,'','kite, nbk, evo, kiteboarding','','',1,'','oxarticle',0,1,0),
('7d0c2546f294d4d3419607da444ddcdd','7d090db46a124f48cb7e6836ceef3f66',1,'f4f2d8eee51b0fd5eb60a46dff1166d8','1401','Trapez ION SOL KITE','Neues Damen Freestyle-Trapez mit einer schlank geschnittenen Outline','',108.40336134454,129,20.596638655462,19,'',129,129,108.40336134454,'','','','','0/ion_sol_kite_waist_2011_1_th.jpg','1/ion_sol_kite_waist_2011_1.jpg','2/nopic.jpg','3/nopic.jpg','4/nopic.jpg','5/nopic.jpg',0,4,'0000-00-00','2006-12-20','2020-04-14 07:26:48',0,0,0,'','trapez, hüfttrapez, sol kite','','',1,'','oxarticle',0,1,0),
('7d0e255c591fb5062669fd039bcf9f29','7d090db46a124f48cb7e6836ceef3f66',1,'058e613db53d782adfc9f2ccb43c45fe','2401','Bindung O\'BRIEN DECADE CT','Geringes Gewicht, beste Performance!','',301.68067226891,359,57.319327731092,19,'',359,359,301.68067226891,'','','','','0/obrien_decade_ct_boot_2010_1_th.jpg','1/obrien_decade_ct_boot_2010_1.jpg','2/nopic.jpg','3/nopic.jpg','4/nopic.jpg','5/nopic.jpg',0,17,'0000-00-00','2006-12-20','2020-04-14 07:26:52',0,0,0,'','bindung, decade, schuh, wakeboarding','','',1,'','oxarticle',0,1,0),
('7d0fb3c3b31d0e7545740eb771083e65','7d090db46a124f48cb7e6836ceef3f66',1,'dc5ffdf380e15674b56dd562a7cb6aec','3503','Kuyichi Ledergürtel JEVER','Ledergürtel, unisex','',25.126050420168,29.9,4.7739495798319,19,'',29.9,29.9,25.126050420168,'','','','','0/p1170221_1_th.jpg','1/p1170221_1.jpg','2/p1170222_1.jpg','3/nopic.jpg','4/nopic.jpg','5/nopic.jpg',0,16,'0000-00-00','2010-12-20','0000-00-00 00:00:00',0,0,0,'','kuyichi, leder, ledergürtel, unisex, used','','',1,'','oxarticle',0,1,0);

INSERT INTO `oxfiles` (`OXID`, `OXARTID`, `OXFILENAME`, `OXSTOREHASH`, `OXPURCHASEDONLY`, `OXMAXDOWNLOADS`, `OXMAXUNREGDOWNLOADS`, `OXLINKEXPTIME`, `OXDOWNLOADEXPTIME`, `OXTIMESTAMP`) VALUES
('oiaad7812ae7127283b8fd6d309ea5d5','oiaa81b5e002fc2f73b9398c361c0b97','ch03.pdf','e48a1b571bd2d2e60fb2d9b1b76b34d4',0,-1,-1,-1,-1,'2016-07-19 14:38:26'),
('48d949cb0af6076f841aea5cb5b703ed', '_test_product_for_basket', 'ch99.pdf', null, 1, -1, -1, -1, -1, '2016-07-19 14:38:26');

REPLACE INTO `oxorderfiles` (`OXID`, `OXORDERID`, `OXFILENAME`, `OXFILEID`, `OXSHOPID`, `OXORDERARTICLEID`, `OXFIRSTDOWNLOAD`, `OXLASTDOWNLOAD`, `OXDOWNLOADCOUNT`, `OXMAXDOWNLOADCOUNT`, `OXDOWNLOADEXPIRATIONTIME`, `OXLINKEXPIRATIONTIME`, `OXRESETCOUNT`, `OXVALIDUNTIL`, `OXTIMESTAMP`) VALUES
('729aafa296783575ddfd8e9527355b3b', '8c726d3f42ff1a6ea2828d5f309de881', 'ch03.pdf', 'oiaad7812ae7127283b8fd6d309ea5d5', 1, 'c5b7fd8dff99f066c168cd720212075a', '2020-09-10 09:14:15', '2020-09-10 09:14:15', 1, 0, 24, 168, 0, '2020-09-11 09:14:15', '2020-09-10 09:14:15'),
('886deb7e49bb2e51b4fb939f6ed7655c', '7f0b6ef39c4e76c04a0f75232489bb65', 'ch03.pdf', 'non_existing_file', 1, 'c5b7fd8dff99f066c168cd720212075a', '2020-09-10 09:14:15', '2020-09-10 09:14:15', 1, 0, 24, 168, 0, '2020-09-11 09:14:15', '2020-09-10 09:14:15'),
('729aafa296783575ddfd8e9527355b9b', '85ecbd1d5e56172ff5af6917894d4a31', 'ch99.pdf', '48d949cb0af6076f841aea5cb5b703ed', 2, '677688370a4a64d8336107bcf174fdeb', '2020-09-10 09:14:15', '2020-09-10 09:14:15', 1, 0, 24, 168, 0, '2020-09-11 09:14:15', '2020-09-10 09:14:15');

INSERT INTO `oxpricealarm` (`OXID`, `OXSHOPID`, `OXUSERID`, `OXEMAIL`, `OXARTID`, `OXPRICE`, `OXCURRENCY`, `OXLANG`, `OXINSERT`, `OXSENDED`, `OXTIMESTAMP`) VALUES
('_test_wished_price_without_user_', 1, '', 'test-email@test.com', 'dc5ffdf380e15674b56dd562a7cb6aec', 10, 'EUR', 1, '2020-05-26 00:00:00', '0000-00-00 00:00:00', '2020-05-26 10:30:18'),
('_test_wished_price_1_', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'user@oxid-esales.com', 'dc5ffdf380e15674b56dd562a7cb6aec', 10, 'EUR', 1, '2020-05-26 00:00:00', '0000-00-00 00:00:00', '2020-05-26 10:31:33'),
('_test_wished_price_2_', 1, '245ad3b5380202966df6ff128e9eecaq', 'redaktion@redaktion.net', 'dc5ffdf380e15674b56dd562a7cb6aec', 10, 'EUR', 1, '2020-05-26 00:00:00', '0000-00-00 00:00:00', '2020-05-26 11:48:20'),
('_test_wished_price_3_', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'user@oxid-esales.com', '_test_product_wished_price_3_', 10, 'EUR', 1, '2020-05-26 00:00:00', '0000-00-00 00:00:00', '2020-05-26 10:31:33'),
('_test_wished_price_4_', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'user@oxid-esales.com', '_test_product_wished_price_4_', 10, 'EUR', 1, '2020-05-26 00:00:00', '0000-00-00 00:00:00', '2020-05-26 10:31:33'),
('_test_wished_price_5_', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'user@oxid-esales.com', 'does_not_exist', 10, 'EUR', 1, '2020-05-26 00:00:00', '0000-00-00 00:00:00', '2020-05-26 10:31:33'),
('_test_wished_price_6_', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'user@oxid-esales.com', 'dc5ffdf380e15674b56dd562a7cb6aec', 10, 'EUR', 1, '2020-05-26 00:00:00', '2020-05-31 10:31:33', '2020-05-26 10:31:33'),
('_test_wished_price_7_', 1, 'non-existing-user-id', 'user@oxid-esales.com', 'dc5ffdf380e15674b56dd562a7cb6aec', 10, 'EUR', 1, '2020-05-26 00:00:00', '2020-05-31 10:31:33', '2020-05-26 10:31:33'),
('_test_wished_price_8_', 2, '123ad3b5380202966df6ff128e9eecaq', 'user@oxid-esales.com', '_test_product_5_', 10, 'EUR', 1, '2020-05-26 00:00:00', '0000-00-00 00:00:00', '2020-05-26 11:48:20'),
('_test_wished_price_delete_', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'user@oxid-esales.com', 'dc5ffdf380e15674b56dd562a7cb6aec', 10, 'EUR', 1, '2020-05-26 00:00:00', '2020-05-31 10:31:33', '2020-05-26 10:31:33'),
('_test_wished_price_delete_1_', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'user@oxid-esales.com', 'dc5ffdf380e15674b56dd562a7cb6aec', 10, 'EUR', 1, '2020-05-26 00:00:00', '2020-05-31 10:31:33', '2020-05-26 10:31:33'),
('_test_wished_price_delete_2_', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'user@oxid-esales.com', 'dc5ffdf380e15674b56dd562a7cb6aec', 10, 'EUR', 1, '2020-05-26 00:00:00', '2020-05-31 10:31:33', '2020-05-26 10:31:33'),
('_test_wished_price_delete_3_', 1, 'e7af1c3b786fd02906ccd75698f4e6b9', 'user@oxid-esales.com', 'dc5ffdf380e15674b56dd562a7cb6aec', 10, 'EUR', 1, '2020-05-26 00:00:00', '2020-05-31 10:31:33', '2020-05-26 10:31:33');

INSERT INTO `oxvoucherseries` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXSERIENR`, `OXDISCOUNT`, `OXDISCOUNTTYPE`, `OXBEGINDATE`, `OXENDDATE`, `OXSERIEDESCRIPTION`, `OXALLOWOTHERSERIES`) VALUES
('voucherserie1', 1, 1, 'voucherserie1', 21.6, 'absolute', '2000-01-01', '2050-12-31', '', 1),
('serie2', 2, 1, 'serie2', 2.0, 'absolute', '2000-01-01', '2050-12-31', 'serie2 description', 1),
('serie3', 3, 1, 'serie3', 3.0, 'absolute', '2000-01-01', '2050-12-31', 'serie3 description', 1),
('personal_voucher', 4, 1, 'myVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'personal voucher', 0),
('personal_series_voucher', 5, 1, 'mySeriesVoucher', 6.0, 'absolute', '2000-01-01', '2050-12-31', 'personal voucher', 1),
('series_voucher', 6, 1, 'seriesVoucher', 8.0, 'absolute', '2000-01-01', '2050-12-31', 'series voucher', 0),
('used_voucher', 7, 1, 'used_voucher', 3.0, 'absolute', '2000-01-01', '2050-12-31', 'used voucher', 0),
('shop_2_voucher_series', 8, 2, 'shop2voucher', 10.0, 'percent', '2000-01-01', '2050-12-31', 'shop 2 voucher', 0),
('basket_payment_cost_voucher', 333, 1, 'basket_payment_cost_voucher', 10.0, 'absolute', '2000-01-01', '2050-12-31', 'basket payment cost voucher', 0),
('voucherserie1x', 9765, 1, 'voucherserie1x', 5.0, 'absolute', '2000-01-01', '2050-12-31', '', 1),
('my_personal_voucher', 9764, 1, 'myPersonalVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'personal voucher', 0),
('my_delete_voucher', 9768, 1, 'myDeleteVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'delete voucher', 0),
('product_voucher', 9763, 1, 'productVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'product voucher', 0),
('category_voucher', 9762, 1, 'categoryVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'category voucher', 0),
('user_voucher', 9761, 1, 'userVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'user voucher', 0),
('minvalue_voucher', 9760, 1, 'minvalueVoucher', 5.0, 'absolute', '2000-01-01', '2050-12-31', 'min value voucher', 0);

INSERT INTO `oxvouchers` (`OXDATEUSED`, `OXORDERID`, `OXUSERID`, `OXRESERVED`, `OXVOUCHERNR`, `OXVOUCHERSERIEID`, `OXDISCOUNT`, `OXID`, `OXTIMESTAMP`, `OEGQL_BASKETID`) VALUES
('2020-08-28', '_149bc776dd339a83d863c4f64693bb6', '_45ad3b5380202966df6ff128e9eecaq', 1, 'voucher1', 'voucherserie1', 21.6, 'usedvoucherid', now(), null),
(null, null, null, 0, 'voucher2', 'voucherserie1', 0, 'notusedvoucherid', now(), null),
(null, null, null, UNIX_TIMESTAMP(current_timestamp()), 'serie2voucher', 'serie2', 0, 'serie2voucher', now(), '_test_basket_private'),
(null, null, null, UNIX_TIMESTAMP(current_timestamp()), 'serie3voucher', 'serie3', 0, 'serie3voucher', now(), '_test_basket_private'),
(null, null, null, 0, 'myVoucher', 'personal_voucher', 0, 'personal_voucher_1', now(), null),
(null, null, null, 0, 'myVoucher', 'personal_voucher', 0, 'personal_voucher_2', now(), null),
(null, null, null, 0, 'mySeriesVoucher', 'personal_series_voucher', 0, 'personal_series_voucher_1', now(), null),
(null, null, null, 0, 'mySeriesVoucher', 'personal_series_voucher', 0, 'personal_series_voucher_2', now(), null),
(null, null, null, 0, 'seriesVoucher', 'series_voucher', 0, 'series_voucher_1', now(), null),
('2020-10-10', '_test_order', 'e7af1c3b786fd02906ccd75698f4e6b9', 0, 'used_voucher', 'used_voucher', 0, 'used_voucher', now(), ''),
(null, null, null, 0, 'shop2voucher', 'shop_2_voucher_series', 0, 'shop_2_voucher_series', now(), ''),
(null, '', '', 0, 'voucher1x', 'voucherserie1x', 5, 'voucher1xid', now(), ''),
(null, '', '', 0, 'myPersonalVoucher', 'my_personal_voucher', 0, 'my_personal_voucher_1', now(), null),
(null, '', '', 0, 'myDeleteVoucher', 'my_delete_voucher', 0, 'my_delete_voucher_1', now(), null),
(null, '', '', 0, 'productVoucher', 'product_voucher', 0, 'product_voucher_1', now(), null),
(null, '', '', 0, 'categoryVoucher', 'category_voucher', 0, 'category_voucher_1', now(), null),
(null, '', '', 0, 'userVoucher', 'user_voucher', 0, 'user_voucher_1', now(), null),
(null, '', '', 0, 'minvalueVoucher', 'minvalue_voucher', 0, 'minvalue_voucher_1', now(), null),
('2020-10-10', '', '', 0, 'basket_payment_cost_voucher', 'basket_payment_cost_voucher', 10.00, 'basket_payment_cost_voucher_1', '2020-11-16 11:26:01', 'basket_payment_cost');

REPLACE INTO `oxvoucherseries2shop` (`OXSHOPID`, `OXMAPOBJECTID`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(2, 8),
(1, 333),
(1, 9765),
(1, 9764),
(1, 9768),
(1, 9763),
(1, 9762),
(1, 9761),
(1, 9760);

INSERT INTO `oxdelivery` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXADDSUMTYPE`, `OXADDSUM`, `OXDELTYPE`, `OXPARAM`, `OXPARAMEND`, `OXFIXED`, `OXSORT`, `OXFINALIZE`, `OXTIMESTAMP`) VALUES
('1b842e73470578914.54719298',902,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00','Versandkosten für Standard: 3,90 Euro innerhalb Deutschland','Shipping costs for Standard: $3.90 for domestic shipping','','','abs',3.9,'p',0,79.99,0,2000,1,'2016-07-19 14:38:26'),
('1b842e734b62a4775.45738618',901,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00','Versandkosten für Standard: Versandkostenfrei ab 80,-','Shipping costs for Standard: Free shipping for orders over $80','','','abs',0,'p',80,999999,0,1000,1,'2016-07-19 14:38:26'),
('1b842e7352422a708.01472527',903,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00','Versandkosten für Standard: 6,90 Rest EU','Shipping costs for Standard: $6.90 to ship in the rest of the EU','','','abs',6.9,'p',0,999999,0,3000,1,'2016-07-19 14:38:26'),
('1b842e738970d31e3.71258327',904,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00','Versandkosten für Beispiel Set1: UPS 48 Std.: 9,90.-','Shipping costs for Example Set1: UPS 48 hrs: $9.90','','','abs',9.9,'p',0,99999,0,4000,1,'2016-07-19 14:38:26'),
('1b842e738970d31e3.71258328',905,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00','Versandkosten für Beispiel Set2: UPS 24 Std. Express: 12,90.-','Shipping costs for Example Set2: UPS 24 hrs Express: $12.90','','','abs',12.9,'p',0,99999,0,5000,1,'2016-07-19 14:38:26'),
('_graphqldel', 909, 1, 1,'0000-00-00 00:00:00','0000-00-00 00:00:00','Versandkosten für GraphQL: 6,66 Euro','Shipping costs for GraphQL: 6.66 Euro','','','abs',6.66,'p',0,999999,0,2000,1,'2020-07-16 14:21:45'),
('_unavailablegraphqldel',910,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00','Versandkosten für UA GraphQL: 6,66 Euro','Shipping costs for UA GraphQL: 6.66 Euro','','','abs',6.66,'p',0,99999,0,2000,1,'2020-07-16 14:21:45');

INSERT INTO `oxdelivery2shop` (`OXSHOPID`, `OXMAPOBJECTID`) VALUES
(1,901),
(1,902),
(1,903),
(1,904),
(1,905),
(1,909),
(1,910),
(2,902);

REPLACE INTO `oxpayments` (`OXID`, `OXACTIVE`, `OXDESC`, `OXADDSUM`, `OXADDSUMTYPE`, `OXADDSUMRULES`, `OXFROMBONI`, `OXFROMAMOUNT`, `OXTOAMOUNT`, `OXVALDESC`, `OXCHECKED`, `OXDESC_1`, `OXVALDESC_1`, `OXDESC_2`, `OXVALDESC_2`, `OXDESC_3`, `OXVALDESC_3`, `OXLONGDESC`, `OXLONGDESC_1`, `OXLONGDESC_2`, `OXLONGDESC_3`, `OXSORT`) VALUES
('oxempty',1,'Empty',0,'abs',0,0,0,0,'',0,'Empty','','','','','','for other countries','An example. Maybe for use with other countries','','',0),
('oxidcashondel',1,'Nachnahme',7.5,'abs',0,0,0,1000000,'',1,'COD (Cash on Delivery)','','','','','','','','','',0),
('oxiddebitnote',1,'Bankeinzug/Lastschrift',0,'abs',0,0,0,1000000,'lsbankname__@@lsblz__@@lsktonr__@@lsktoinhaber__@@',0,'Direct Debit','lsbankname__@@lsblz__@@lsktonr__@@lsktoinhaber__@@','','','','','Die Belastung Ihres Kontos erfolgt mit dem Versand der Ware.','Your bank account will be charged when the order is shipped.','','',0),
('oxidinvoice',1,'Rechnung',0,'abs',0,800,0,1000000,'',0,'Invoice','','','','','','','','','',0),
('oxidpayadvance',1,'Vorauskasse',0,'abs',0,0,0,1000000,'',1,'Cash in advance','','','','','','','','','',0),
('oxidgraphql', 1, 'GraphQL', 7.77, 'abs', 0, 0, 0, 1000000, '', 1, 'GraphQL (coconuts)', '', '', '', '', '', '', '', '', '', 700);

REPLACE INTO `oxobject2delivery` (`OXID`, `OXDELIVERYID`, `OXOBJECTID`, `OXTYPE`) VALUES
('_deliveryrelation1', '_deliveryset', 'a7c40f631fc920687.20179984', 'oxdelset'),
('_deliveryrelation2', '_graphqldel', 'a7c40f631fc920687.20179984', 'oxcountry');

REPLACE INTO `oxdeliveryset` (`OXID`, `OXSHOPID`, `OXMAPID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXPOS`) VALUES
('_deliveryset', 1, 906, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'graphql set', 'graphql set', '', '', 50),
('_unavailabledeliveryset', 1, 907, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'unavailable graphql set', 'unavailable graphql set', '', '', 60);

REPLACE INTO `oxdeliveryset2shop` (`OXSHOPID`, `OXMAPOBJECTID`) VALUES
(1, 906),
(1, 907),
(2, 901);

INSERT INTO `oxdel2delset` (`OXID`, `OXDELID`, `OXDELSETID`) VALUES
('4ba44c7251a587071.83952129','1b842e73470578914.54719298','oxidstandard'),
('4ba44c72528a26008.03376396','1b842e7352422a708.01472527','oxidstandard'),
('5be44bc9261862fc4.78617917','1b842e734b62a4775.45738618','oxidstandard'),
('_setrelation1', '_graphqldel', '_deliveryset');

INSERT INTO `oxobject2payment` (`OXID`, `OXPAYMENTID`, `OXOBJECTID`, `OXTYPE`) VALUES
('0f941664de07fe713.78180932','oxiddebitnote','a7c40f631fc920687.20179984','oxcountry'),
('0f941664de081d815.03693723','oxiddebitnote','a7c40f6320aeb2ec2.72885259','oxcountry'),
('0f941664de082a1b0.85265324','oxiddebitnote','a7c40f6321c6f6109.43859248','oxcountry'),
('0f941664e9e60f698.58333517','oxidcashondel','a7c40f631fc920687.20179984','oxcountry'),
('0f941664ee2448a22.44967166','oxidinvoice','a7c40f631fc920687.20179984','oxcountry'),
('0f941664ee245e458.07911799','oxidinvoice','a7c40f6320aeb2ec2.72885259','oxcountry'),
('0f941664ee246ac84.39868591','oxidinvoice','a7c40f6321c6f6109.43859248','oxcountry'),
('0f941664efa30a021.06837665','oxidpayadvance','a7c40f631fc920687.20179984','oxcountry'),
('0f941664efa320ca8.35650805','oxidpayadvance','a7c40f6320aeb2ec2.72885259','oxcountry'),
('0f941664efa32d4e5.28625433','oxidpayadvance','a7c40f6321c6f6109.43859248','oxcountry'),
('1b842e737567541b1.16932982','oxidcashondel','oxidstandard','oxdelset'),
('1b842e73756761653.33874589','oxiddebitnote','oxidstandard','oxdelset'),
('1b842e737567681b7.32408586','oxidpayadvance','oxidstandard','oxdelset'),
('1b842e7375676dd84.15824521','oxidinvoice','oxidstandard','oxdelset'),
('92d4214bf673df592.85542338','oxidpayadvance','a434214960877b879.20979568','oxdelset'),
('f324215af5c8be899.90598822','oxiddebitnote','f324215af31591936.94392085','oxdelset'),
('_paymentrelation1', 'oxidgraphql', 'a7c40f631fc920687.20179984', 'oxcountry'),
('_paymentrelation2', 'oxidgraphql', '_deliveryset', 'oxdelset');

REPLACE INTO `oxdiscount` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXAMOUNT`, `OXAMOUNTTO`, `OXPRICETO`, `OXPRICE`, `OXADDSUMTYPE`, `OXADDSUM`, `OXITMARTID`, `OXITMAMOUNT`, `OXITMMULTIPLE`, `OXSORT`, `OXTIMESTAMP`) VALUES
('9fc3e801d40332ae4.08296552',901,1,0,'2003-03-29 00:00:00','2003-03-30 00:00:00','15% auf den gesamten Shop an einem Tag','15% on all articles for one day','','',0,999999,999999,0,'%',15,'',0,0,10,'2021-05-28 14:24:41'),
('9fc3e801da9cdd0b2.74513077',902,1,0,'0000-00-00 00:00:00','0000-00-00 00:00:00','10% ab 200 Euro Einkaufswert','10% on 200 Euro or more','','',0,999999,999999,200,'%',10,'',0,0,20,'2021-05-28 14:24:41'),
('4e542e4e8dd127836.00288451',903,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00','Aktion Schnäppchen','Current Bargain','','',1,99999,0,0,'%',10,'',0,0,30,'2021-05-28 14:24:41');

REPLACE INTO `oxdiscount2shop` (`OXSHOPID`, `OXMAPOBJECTID`, `OXTIMESTAMP`) VALUES
(1,901,'2016-07-19 14:38:26'),
(1,902,'2016-07-19 14:38:26'),
(1,903,'2016-07-19 14:38:26');

INSERT INTO `oxobject2discount` (`OXID`, `OXDISCOUNTID`, `OXOBJECTID`, `OXTYPE`, `OXTIMESTAMP`) VALUES
('f4f2bdd8e8262be70dd2fff6a2733490','4e542e4e8dd127836.00288451','0f4fb00809cec9aa0910aa9c8fe36751','oxcategories','2016-07-19 14:38:26');

#promotions for main shop
REPLACE INTO `oxactions` (`OXID`, `OXSHOPID`, `OXTYPE`, `OXTITLE`, `OXTITLE_1`, `OXTITLE_2`, `OXTITLE_3`, `OXLONGDESC`, `OXLONGDESC_1`, `OXLONGDESC_2`, `OXLONGDESC_3`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXPIC`, `OXPIC_1`, `OXPIC_2`, `OXPIC_3`, `OXLINK`, `OXLINK_1`, `OXLINK_2`, `OXLINK_3`, `OXSORT`, `OXTIMESTAMP`) VALUES
('d51545e80843be666a9326783a73e91d', 1, 2, 'Upcoming Promotion', 'Upcoming Promotion', '', '', '<p>&nbsp;</p>', '<a href=\"[{ oxgetseourl type=\'oxmanufacturer\' oxid=\'9434afb379a46d6c141de9c9e5b94fcf\' }]\"><img alt=\"\" src=\"[{$oViewConf->getPictureDir()}]promo/upcoming_en.jpg\" /></a>', '', '', 0, '2010-10-10 00:00:00', '2011-10-10 00:00:00', '', '', '', '', '', '', '', '', 3, '2016-07-19 14:38:25'),
('d51dbdafb1e51b869f5d8ac233e97814', 1, 2, 'Current Promotion', 'Current Promotion', '', '', '<p>&nbsp;</p>', '<a href=\"[{ oxgetseourl type=\'oxcategory\' oxid=\'30e44ab85808a1f05.26160932\' }]\">          <img alt=\"\" src=\"[{$oViewConf->getPictureDir()}]promo/current_en.jpg\" /></a>', '', '', 0, '2010-01-01 00:00:00', '2011-10-10 00:00:00', '', '', '', '', '', '', '', '', 2, '2016-07-19 14:38:25'),
('d51f5e7446e9193188fb315c9d60520a', 1, 2, 'Expired promotion', 'Expired promotion', '', '', '<a href=\"[{ oxgetseourl type=\'oxarticle\' oxid=\'1651\' }]\">                                 <img alt=\"\" src=\"[{$oViewConf->getPictureDir()}]promo/expired_de.jpg\" /></a>', '<a href=\"[{ oxgetseourl type=\'oxarticle\' oxid=\'1651\' }]\">                                 <img alt=\"\" src=\"[{$oViewConf->getPictureDir()}]promo/expired_en.jpg\" /></a>', '', '', 0, '2010-01-01 00:00:00', '2010-02-01 00:00:00', '', '', '', '', '', '', '', '', 1, '2016-07-19 14:38:25');

#promotions for sub shop
REPLACE INTO `oxactions` (OXID, OXSHOPID, OXTYPE, OXTITLE, OXTITLE_1, OXTITLE_2, OXTITLE_3, OXLONGDESC, OXLONGDESC_1, OXLONGDESC_2, OXLONGDESC_3, OXACTIVE, OXACTIVEFROM, OXACTIVETO, OXPIC, OXPIC_1, OXPIC_2, OXPIC_3, OXLINK, OXLINK_1, OXLINK_2, OXLINK_3, OXSORT, OXTIMESTAMP) VALUES
('test_active_sub_shop_promotion_1', 2, 2, 'Current sub shop Promotion 1 DE', 'Current sub shop Promotion 1 EN', '', '', 'Long description 1 DE', 'Long description 1 EN', '', '', 1, '2010-10-10 00:00:00', '2111-10-10 00:00:00', '', '', '', '', '', '', '', '', 3, '2020-04-23 12:07:10'),
('test_active_sub_shop_promotion_2', 2, 2, 'Current sub shop Promotion 2 DE', 'Current sub shop Promotion 2 EN', '', '', 'Long description 2 DE', 'Long description 2 EN', '', '', 1, '2010-01-01 00:00:00', '2111-10-10 00:00:00', '', '', '', '', '', '', '', '', 2, '2020-04-23 12:07:10'),
('test_inactive_sub_shop_promotion_1', 2, 2, 'Upcoming sub shop promotion DE', 'Upcoming sub shop promotion EN', '', '', 'Long description 3 DE', 'Long description 3 EN', '', '', 0, '2010-01-01 00:00:00', '2010-02-01 00:00:00', '', '', '', '', '', '', '', '', 1, '2020-04-23 12:07:10');
