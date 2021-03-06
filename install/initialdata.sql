
REPLACE INTO `gen_lookups` VALUES
('Address_Purpose','1','Home','i',NULL),
('Address_Purpose','2','Work','i',NULL),
('Address_Purpose','3','Alt','i','2012-08-28 19:21:54'),
('Address_Purpose','4','Office','o','2013-02-07 19:00:41'),

('Age_Bracket','2','Infant','','2013-04-06 20:50:11'),
('Age_Bracket','4','Minor','','2013-04-06 20:50:11'),
('Age_Bracket','6','Adult','','2013-04-06 20:50:11'),
('Age_Bracket','8','Senior','','2013-04-06 20:50:11'),

('anomalyTypes','ba','Bad City','`City`=\'\'','2012-03-08 03:51:20'),
('anomalyTypes','bs','Bad State','`State`=\'\'','2012-03-22 15:42:16'),
('anomalyTypes','sa','Bad Street Address','`Street Address`=\'\'','2012-03-08 03:51:20'),
('anomalyTypes','z','Bad Zip Code','Zip=\'\' or Zip=\'0\' or LENGTH(Zip)<5','2012-03-08 03:51:20'),

('Cal_Event_Status','a','Active','','2013-03-27 17:56:36'),
('Cal_Event_Status','d','Deleted','','2013-03-27 17:56:36'),
('Cal_Event_Status','t','Logged','','2013-03-27 17:56:36'),
('Cal_Hide_Add_Members','Vol_Activities1','n','','2012-10-09 22:29:10'),
('Cal_House','Vol_Activitieshou','House Calendar','','2012-08-31 22:51:41'),
('Cal_Select','Vol_Activities1','n','','2012-06-05 20:07:16'),
('Cal_Show_Delete_Email','Vol_Activities1','y','','2012-10-09 22:29:10'),

('Campaign_Status','a','Active','',NULL),
('Campaign_Status','c','Closed','',NULL),
('Campaign_Status','d','Disabled','',NULL),

('Campaign_Type','as','Normal','','2011-11-03 02:18:13'),
('Campaign_Type','pct','Percent Cut Out','','2011-11-03 02:18:14'),
('Campaign_Type','ink','In Kind','','2013-10-03 09:18:14'),
('Campaign_Type', 'sch', 'Scholarship', '', '2014-09-21 14:27:01'),

('Category_Types', '1', 'Items', '', NULL),
('Category_Types', '2', 'Tax', '', NULL),
('Category_Types', '3', 'Penalty', '', NULL),

('Dir_Type_Selector_Code','d','Directory','','2011-09-05 15:43:08'),
('Dir_Type_Selector_Code','e','Email Addresses','','2011-09-05 15:43:08'),
('Dir_Type_Selector_Code','m','Mailing List','','2011-09-05 15:43:08'),

('Distance_Range','50','Up to 50 miles','2','2013-08-22 19:25:41'),
('Distance_Range','100','51 to 100 miles','3','2013-08-22 19:25:41'),
('Distance_Range','150','101 to 150 miles','4','2013-08-22 19:25:41'),
('Distance_Range','200','151 to 200 miles','5','2013-08-22 19:25:41'),
('Distance_Range','30000','More Than 200 miles','7','2013-08-22 19:25:41'),

('Dormant_Selector_Code','act','Active Only','','2011-06-01 19:25:41'),
('Dormant_Selector_Code','both','Dormant & Active','','2011-06-01 19:25:41'),
('Dormant_Selector_Code','dor','Dormant Only','','2011-06-01 19:25:41'),

('Email_Purpose','1','Home','i',null),
('Email_Purpose','2','Work','i',null),
('Email_Purpose','3','Alt','i','2012-08-28 19:27:42'),
('Email_Purpose','4','Office','o','2013-02-07 19:00:41'),

('Ethnicity','c','Caucasian','','2013-06-06 18:07:46'),
('Ethnicity','f','African-American','','2013-06-06 18:07:46'),
('Ethnicity','h','Hispanic','','2013-06-06 18:07:46'),
('Ethnicity','k','Asia-Pacific','','2013-06-06 18:07:46'),
('Ethnicity','x','Other','','2013-06-06 18:07:46'),

('E_Shell_Status','a','Active','','2011-04-10 21:43:19'),
('E_Shell_Status','d','Disabled','','2011-04-10 21:43:19'),

('FB_Status','a','Active','','2011-03-29 19:30:43'),
('FB_Status','d','Disabled','','2011-03-29 19:30:43'),
('FB_Status','w','Waiting','','2011-03-29 19:30:43'),
('FB_Status','x','Prohibited','','2011-03-29 19:30:43'),

('Fees_Item_Type', 'r', 'Room','','2013-01-07 17:25:39'),
('Fees_Item_Type', 'k', 'Key Deposit','','2013-01-07 17:25:39'),

('Gender','f','Female','',NULL),
('Gender','m','Male','',NULL),
('Gender','t','Other','',NULL),


('HourReportType','d','Open & Logged','','2012-06-12 14:44:10'),
('HourReportType','l','Only Logged Hours','','2012-06-12 14:44:10'),
('HourReportType','ul','Only Open Hours','','2012-06-12 14:44:10'),

('Invoice_Status', 'p', 'Paid', '',NULL),
('Invoice_Status', 'up', 'Unpaid', '',NULL),
('Invoice_Status', 'c', 'Carried', '',NULL),

('Media_Source', 'na', 'News Article','','2012-07-25 20:21:39'),
('Media_Source', 'hs', 'Hospital Staff','','2012-07-25 20:21:39'),
('Media_Source', 'fr', 'Friend','','2012-07-25 20:21:39'),
('Media_Source', 'hhn', 'HHN','','2012-07-25 20:21:39'),
('Media_Source', 'ws', 'Web Search','','2012-07-25 20:21:39'),

('Member_Basis','ai','Individual','i','2011-01-08 02:18:10'),
('Member_Basis','c','Company','o','2011-01-08 02:20:24'),
('Member_Basis','np','Non Profit','o','2011-01-08 02:20:24'),
('Member_Basis','og','Government','o','2011-05-14 00:31:12'),

('mem_status','a','Active','m',NULL),
('mem_status','d','Deceased','m',NULL),
('mem_status','in','Inactive','m',NULL),
('mem_status','p','Pending','',NULL),
('mem_status','TBD','To be deleted','','2011-01-11 20:21:17'),
('mem_status','u','Duplicate','','2010-11-09 16:31:25'),

('Name_Prefix','dr','Dr.','',NULL),
('Name_Prefix','mi','Miss.','',NULL),
('Name_Prefix','mr','Mr.','',NULL),
('Name_Prefix','mrs','Mrs.','',NULL),
('Name_Prefix','ms','Ms.','',NULL),
('Name_Prefix','rev','Rev.','',NULL),
('Name_Prefix','The','The','','2010-11-23 16:03:31'),

('Name_Suffix','D.D.S.','D.D.S.','','2010-12-27 18:21:46'),
('Name_Suffix','esq','Esq.','',NULL),
('Name_Suffix','ii','II','',NULL),
('Name_Suffix','iii','III','',NULL),
('Name_Suffix','jd','Jd.','',NULL),
('Name_Suffix','jr','Jr.','',NULL),
('Name_Suffix','md','MD.','',NULL),
('Name_Suffix','phd','Ph.D.','',NULL),

('Order_billing_Type','1','Pre Paid','',NULL),
('Order_billing_Type','1','Post Paid','',NULL),

('Order_Status','a','Active','',NULL),
('Order_Status','f','Finished','',NULL),
('Order_Status','s','Suspended','',NULL),
('Order_Status','sa','Suspended-Ageing','',NULL),

('Page_Type','c','Component','','2011-09-23 16:31:34'),
('Page_Type','p','Web Page','','2011-09-23 16:31:34'),
('Page_Type','s','Web Service','','2011-09-23 16:31:34'),


('Pay_Status', 'c', 'Cleared', '',NULL),
('Pay_Status', 'p', 'Pending', '',NULL),
('Pay_Status', 'd', 'Denied', '',NULL),
('Pay_Status', 'er', 'Error', '',NULL),
('Pay_Status', 'v', 'Void', '',NULL),
('Pay_Status', 'r', 'Returned', '',NULL),

('Pay_Type','ca','Cash','',NULL),
('Pay_Type','cc','Credit Card','',NULL),
('Pay_Type','ck','Check','',NULL),

('Period_Unit', '1', 'Day','',NULL),
('Period_Unit', '2', 'Week','',NULL),
('Period_Unit', '3', 'Month','',NULL),
('Period_Unit', '4', 'Year','',NULL),

('Phone_Type','dh','Home','i',NULL),
('Phone_Type','gw','Work','i',NULL),
('Phone_Type','hw','Office','o','2012-08-28 18:55:48'),
('Phone_Type','mc','Cell','i',NULL),
('Phone_Type','xf','Fax','',NULL),

('rel_type','chd','Child','par',NULL),
('rel_type','par','Parent','chd',NULL),
('rel_type','rltv','Relative','','2011-11-11 22:41:18'),
('rel_type','sib','Sibling','sib',NULL),
('rel_type','sp','Partner','sp',NULL),
('rel_type','frd','Friend','sp',NULL),

('Role_Codes','10','Admin User','','2011-03-29 19:30:44'),
('Role_Codes','100','Web User','','2011-03-29 19:30:44'),
('Role_Codes','700','Guest','','2011-03-29 19:30:44'),

('Salutation','fln','First &Last','','2010-11-04 21:30:52'),
('Salutation','fno','First Name','','2010-10-24 01:32:22'),
('Salutation','for','Formal','','2010-11-04 21:30:52'),
('Salutation','mm','Retro-Mr. & Mrs.','','2010-12-02 16:42:21'),

('Special_Needs','c','Cancer','',NULL),
('Special_Needs','f','Dev. Challenged','',NULL),

('Student_Link', 'slf', 'Student','',NULL),
('Student_Link', 'd', 'Donor','',NULL),

('validMemStatus','a','Active','','2012-03-08 03:33:02'),
('validMemStatus','d','Deceased','','2012-03-08 03:33:02'),
('validMemStatus','in','Inactive','','2012-03-08 03:33:02'),

('Verify_User_Address','done','Verified','','2011-08-08 21:08:12'),
('Verify_User_Address','y','Waiting for verification','','2011-08-08 21:08:12'),

('Vol_Activities','5','Fundraising','black,white',NULL),
('Vol_Activities','6','Special Event Planning/Organizing',',',NULL),
('Vol_Activities','v102','Board Member',',',NULL),
('Vol_Activities','v103','Capital Committee',',',NULL),


('Vol_Category','Vol_Activities','Volunteer Activities','Vol_Type.Vol','2011-04-08 16:39:16'),
('Vol_Category','Vol_Type','Member Type','','2011-04-08 16:39:08'),

('Vol_Rank','c','Chair','','2011-04-24 17:38:02'),
('Vol_Rank','cc','Co-Chair','','2011-04-24 17:38:02'),
('Vol_Rank','m','Member','','2011-04-24 17:38:02'),


('Vol_Status','a','Active','','2011-11-30 22:33:14'),
('Vol_Status','i','Retired','','2011-11-30 22:33:14'),

('Vol_Type','d','Donor','',NULL),
('Vol_Type','stu','Student','',NULL),
('Vol_Type','Vol','Volunteer','','2010-11-23 16:51:49'),

('Web_User_Status','a','active','','2011-03-29 19:30:44'),
('Web_User_Status','d','Disabled','','2011-03-29 19:30:44'),
('Web_User_Status','w','Waiting','','2011-04-21 02:28:20'),
('Web_User_Status','x','Prohibited','','2011-03-29 19:30:44');

-- ;



--
-- insert System configuration
--
REPLACE INTO `sys_config` VALUES 
('DefaultPayType','ca','s','f'),
('EmailBlockSize','200','i','r'),
('FutureLimit','1','i','v'),
('fy_diff_Months','3','i','f'),
('MajorDonation','500','i','d'),
('MaxDonate','100000','i','d'),
('MaxExpected','60','i','h'),
('MaxLifetimeFee','210','i','h'),
('MaxRepeatEvent','53','i','v'),
('PaymentLogoUrl','images/hostpaylogo.jpg','s','f'),
('SolicitBuffer','90','i','r');

-- ;


Replace into `order_period` Values
(1, 0, 1, 2, 'Weekly'),
(2, 0, 2, 2, 'Two Weeks'),
(3, 0, 1, 3, 'Monthly'),
(4, 0, 0, 1, 'One Time');
-- ;


--
-- insert super user
--
REPLACE into name (idName, Name_Last, Member_Type, Member_Status, Record_Member)
	values (-1, 'admin', 'ai', 'a', 1);
-- ;


--
-- Dumping data for table `w_auth`
--
REPLACE INTO `w_auth` VALUES (-1,'10','p',0,'admin','2011-08-12 10:00:00','admin','a','0',now());
-- ;


--
-- Dumping data for table `w_users`
--

REPLACE INTO `w_users` VALUES (-1,'admin','539e17171312c324d3c23908f85f3149','a','','','','','done',NULL,'','',NULL,now());
-- ;


--
-- Table `w_groups`
--
REPLACE INTO `w_groups` VALUES
('db','Maintenance','Configure metadata.','','','','\0','','2013-08-07 16:19:17','ecrane57','2013-07-28 16:34:25'),
('dm','Donation Management','Donation Management','','','','\0','','2013-08-07 16:11:22','ecrane57','2013-07-28 16:34:25'),
('dna','Donors (No Amounts)','View lists of donors but without donation amounts','','','','\0','','2013-08-07 16:16:10','ecrane57','2013-07-28 16:34:25'),
('mm','Member Management','Member Management, basic access to admin site.','','','','\0','','2013-08-07 16:19:40','ecrane57','2013-07-28 16:34:25'),
('pub','Public','Public','','','','\0','','2013-08-07 16:11:22','ecrane57','2013-07-28 16:34:25'),
('v','Volunteer','Volunteer site.','','','','\0','','2013-08-07 16:19:17','ecrane57','2013-07-28 16:34:25');
-- ;


--
-- Dumping data for table `counter`
--
REPLACE INTO `counter` VALUES
(1,'relationship',10,NULL),
(4,'repeater',10,NULL),
(5,'codes',100,NULL),
(6, 'invoice', 1000, NULL);
-- ;


--
-- Mercury Hosted Gateway
--
Insert INTO `cc_hosted_gateway` (`cc_name`, `Merchant_Id`, `Password`, `Credit_Url`, `Trans_Url`, `CardInfo_Url`, `Checkout_Url`, `Mobile_CardInfo_Url`, `Mobile_Checkout_Url`) 
VALUES 
('Test', '', '', 'https://hc.mercurydev.net/hcws/hcservice.asmx?WSDL', 'https://hc.mercurydev.net/tws/TransactionService.asmx?WSDL', 'https://hc.mercurydev.net/CardInfo.aspx', 'https://hc.mercurydev.net/Checkout.aspx', 'https://hc.mercurydev.net/mobile/mCardInfo.aspx', 'https://hc.mercurydev.net/mobile/mCheckout.aspx'),
('Production', '', '', 'https://hc.mercurypay.com/hcws/hcservice.asmx?WSDL', 'https://hc.mercurypay.com/tws/transactionservice.asmx?WSDL', 'https://hc.mercurypay.com/CardInfo.aspx', 'https://hc.mercurypay.com/Checkout.aspx', 'https://hc.mercurypay.com/mobile/mCardInfo.aspx', 'https://hc.mercurypay.com/mobile/mCheckout.aspx');
-- ;


REPLACE into transaction_type values
(1, 'Sale', '', 's'),
(2, 'Void', '', 'vs'),
(3, 'Return', '', 'r'),
(4, 'Void Return', '', 'vr');
-- ;

--
-- Dumping data for table `street_suffix`
--
REPLACE INTO `street_suffix` VALUES ('ALLEE','ALY','Aly'),('ALLEY','ALY','Aly'),('ALLY','ALY','Aly'),('ALY','ALY','Aly'),('ANEX','ANX','Anx'),('ANNEX','ANX','Anx'),('ANNX','ANX','Anx'),('ANX','ANX','Anx'),('ARC','ARC','Arc'),('ARCADE','ARC','Arc'),('AV','AVE','Ave'),('AVE','AVE','Ave'),('AVEN','AVE','Ave'),('AVENU','AVE','Ave'),('AVENUE','AVE','Ave'),('AVN','AVE','Ave'),('AVNUE','AVE','Ave'),('BAYOO','BYU','Byu'),('BAYOU','BYU','Byu'),('BCH','BCH','Bch'),('BEACH','BCH','Bch'),('BEND','BND','Bnd'),('BND','BND','Bnd'),('BLF','BLF','Blf'),('BLUF','BLF','Blf'),('BLUFF','BLF','Blf'),('BLUFFS','BLFS','Blfs'),('BOT','BTM','Btm'),('BOTTM','BTM','Btm'),('BOTTOM','BTM','Btm'),('BTM','BTM','Btm'),('BLVD','BLVD','Blvd'),('BOUL','BLVD','Blvd'),('BOULEVARD','BLVD','Blvd'),('BOULV','BLVD','Blvd'),('BR','BR','Br'),('BRANCH','BR','Br'),('BRNCH','BR','Br'),('BRDGE','BRG','Brg'),('BRG','BRG','Brg'),('BRIDGE','BRG','Brg'),('BRK','BRK','Brk'),('BROOK','BRK','Brk'),('BROOKS','BRKS','Brks'),('BURG','BG','Bg'),('BURGS','BGS','Bgs'),('BYP','BYP','Byp'),('BYPA','BYP','Byp'),('BYPAS','BYP','Byp'),('BYPASS','BYP','Byp'),('BYPS','BYP','Byp'),('CAMP','CP','Cp'),('CMP','CP','Cp'),('CP','CP','Cp'),('CANYN','CYN','Cyn'),('CANYON','CYN','Cyn'),('CNYN','CYN','Cyn'),('CYN','CYN','Cyn'),('CAPE','CPE','Cpe'),('CPE','CPE','Cpe'),('CAUSEWAY','CSWY','Cswy'),('CAUSWAY','CSWY','Cswy'),('CSWY','CSWY','Cswy'),('CEN','CTR','Ctr'),('CENT','CTR','Ctr'),('CENTER','CTR','Ctr'),('CENTR','CTR','Ctr'),('CENTRE','CTR','Ctr'),('CNTER','CTR','Ctr'),('CNTR','CTR','Ctr'),('CTR','CTR','Ctr'),('CENTERS','CTRS','Ctrs'),('CIR','CIR','Cir'),('CIRC','CIR','Cir'),('CIRCL','CIR','Cir'),('CIRCLE','CIR','Cir'),('CRCL','CIR','Cir'),('CRCLE','CIR','Cir'),('CIRCLES','CIRS','Cirs'),('CLF','CLF','Clf'),('CLIFF','CLF','Clf'),('CLFS','CLFS','Clfs'),('CLIFFS','CLFS','Clfs'),('CLB','CLB','Clb'),('CLUB','CLB','Clb'),('COMMON','CMN','Cmn'),('COR','COR','Cor'),('CORNER','COR','Cor'),('CORNERS','CORS','Cors'),('CORS','CORS','Cors'),('COURSE','CRSE','Crse'),('CRSE','CRSE','Crse'),('COURT','CT','Ct'),('CRT','CT','Ct'),('CT','CT','Ct'),('COURTS','CTS','Cts'),('CTS','CTS','Cts'),('COVE','CV','Cv'),('CV','CV','Cv'),('COVES','CVS','Cvs'),('CK','CRK','Crk'),('CR','CRK','Crk'),('CREEK','CRK','Crk'),('CRK','CRK','Crk'),('CRECENT','CRES','Cres'),('CRES','CRES','Cres'),('CRESCENT','CRES','Cres'),('CRESENT','CRES','Cres'),('CRSCNT','CRES','Cres'),('CRSENT','CRES','Cres'),('CRSNT','CRES','Cres'),('CREST','CRST','Crst'),('CROSSING','XING','Xing'),('CRSSING','XING','Xing'),('CRSSNG','XING','Xing'),('XING','XING','Xing'),('CROSSROAD','XRD','Xrd'),('CURVE','CURV','Curv'),('DALE','DL','Dl'),('DL','DL','Dl'),('DAM','DM','Dm'),('DM','DM','Dm'),('DIV','DV','Dv'),('DIVIDE','DV','Dv'),('DV','DV','Dv'),('DVD','DV','Dv'),('DR','DR','Dr'),('DRIV','DR','Dr'),('DRIVE','DR','Dr'),('DRV','DR','Dr'),('DRIVES','DRS','Drs'),('EST','EST','Est'),('ESTATE','EST','Est'),('ESTATES','ESTS','Ests'),('ESTS','ESTS','Ests'),('EXP','EXPY','Expy'),('EXPR','EXPY','Expy'),('EXPRESS','EXPY','Expy'),('EXPRESSWAY','EXPY','Expy'),('EXPW','EXPY','Expy'),('EXPY','EXPY','Expy'),('EXT','EXT','Ext'),('EXTENSION','EXT','Ext'),('EXTN','EXT','Ext'),('EXTNSN','EXT','Ext'),('EXTENSIONS','EXTS','Exts'),('EXTS','EXTS','Exts'),('FALL','FALL','Fall'),('FALLS','FLS','Fls'),('FLS','FLS','Fls'),('FERRY','FRY','Fry'),('FRRY','FRY','Fry'),('FRY','FRY','Fry'),('FIELD','FLD','Fld'),('FLD','FLD','Fld'),('FIELDS','FLDS','Flds'),('FLDS','FLDS','Flds'),('FLAT','FLT','Flt'),('FLT','FLT','Flt'),('FLATS','FLTS','Flts'),('FLTS','FLTS','Flts'),('FORD','FRD','Frd'),('FRD','FRD','Frd'),('FORDS','FRDS','Frds'),('FOREST','FRST','Frst'),('FORESTS','FRST','Frst'),('FRST','FRST','Frst'),('FORG','FRG','Frg'),('FORGE','FRG','Frg'),('FRG','FRG','Frg'),('FORGES','FRGS','Frgs'),('FORK','FRK','Frk'),('FRK','FRK','Frk'),('FORKS','FRKS','Frks'),('FRKS','FRKS','Frks'),('FORT','FT','Ft'),('FRT','FT','Ft'),('FT','FT','Ft'),('FREEWAY','FWY','Fwy'),('FREEWY','FWY','Fwy'),('FRWAY','FWY','Fwy'),('FRWY','FWY','Fwy'),('FWY','FWY','Fwy'),('GARDEN','GDN','Gdn'),('GARDN','GDN','Gdn'),('GDN','GDN','Gdn'),('GRDEN','GDN','Gdn'),('GRDN','GDN','Gdn'),('GARDENS','GDNS','Gdns'),('GDNS','GDNS','Gdns'),('GRDNS','GDNS','Gdns'),('GATEWAY','GTWY','Gtwy'),('GATEWY','GTWY','Gtwy'),('GATWAY','GTWY','Gtwy'),('GTWAY','GTWY','Gtwy'),('GTWY','GTWY','Gtwy'),('GLEN','GLN','Gln'),('GLN','GLN','Gln'),('GLENS','GLNS','Glns'),('GREEN','GRN','Grn'),('GRN','GRN','Grn'),('GREENS','GRNS','Grns'),('GROV','GRV','Grv'),('GROVE','GRV','Grv'),('GRV','GRV','Grv'),('GROVES','GRVS','Grvs'),('HARB','HBR','Hbr'),('HARBOR','HBR','Hbr'),('HARBR','HBR','Hbr'),('HBR','HBR','Hbr'),('HRBOR','HBR','Hbr'),('HARBORS','HBRS','Hbrs'),('HAVEN','HVN','Hvn'),('HAVN','HVN','Hvn'),('HVN','HVN','Hvn'),('HEIGHT','HTS','Hts'),('HEIGHTS','HTS','Hts'),('HGTS','HTS','Hts'),('HT','HTS','Hts'),('HTS','HTS','Hts'),('HIGHWAY','HWY','Hwy'),('HIGHWY','HWY','Hwy'),('HIWAY','HWY','Hwy'),('HIWY','HWY','Hwy'),('HWAY','HWY','Hwy'),('HWY','HWY','Hwy'),('HILL','HL','Hl'),('HL','HL','Hl'),('HILLS','HLS','Hls'),('HLS','HLS','Hls'),('HLLW','HOLW','Holw'),('HOLLOW','HOLW','Holw'),('HOLLOWS','HOLW','Holw'),('HOLW','HOLW','Holw'),('HOLWS','HOLW','Holw'),('INLET','INLT','Inlt'),('INLT','INLT','Inlt'),('IS','IS','Is'),('ISLAND','IS','Is'),('ISLND','IS','Is'),('ISLANDS','ISS','Iss'),('ISLNDS','ISS','Iss'),('ISS','ISS','Iss'),('ISLE','ISLE','Isle'),('ISLES','ISLE','Isle'),('JCT','JCT','Jct'),('JCTION','JCT','Jct'),('JCTN','JCT','Jct'),('JUNCTION','JCT','Jct'),('JUNCTN','JCT','Jct'),('JUNCTON','JCT','Jct'),('JCTNS','JCTS','Jcts'),('JCTS','JCTS','Jcts'),('JUNCTIONS','JCTS','Jcts'),('KEY','KY','Ky'),('KY','KY','Ky'),('KEYS','KYS','Kys'),('KYS','KYS','Kys'),('KNL','KNL','Knl'),('KNOL','KNL','Knl'),('KNOLL','KNL','Knl'),('KNLS','KNLS','Knls'),('KNOLLS','KNLS','Knls'),('LAKE','LK','Lk'),('LK','LK','Lk'),('LAKES','LKS','Lks'),('LKS','LKS','Lks'),('LAND','LAND','Land'),('LANDING','LNDG','Lndg'),('LNDG','LNDG','Lndg'),('LNDNG','LNDG','Lndg'),('LA','LN','Ln'),('LANE','LN','Ln'),('LANES','LN','Ln'),('LN','LN','Ln'),('LGT','LGT','Lgt'),('LIGHT','LGT','Lgt'),('LIGHTS','LGTS','Lgts'),('LF','LF','Lf'),('LOAF','LF','Lf'),('LCK','LCK','Lck'),('LOCK','LCK','Lck'),('LCKS','LCKS','Lcks'),('LOCKS','LCKS','Lcks'),('LDG','LDG','Ldg'),('LDGE','LDG','Ldg'),('LODG','LDG','Ldg'),('LODGE','LDG','Ldg'),('LOOP','LOOP','Loop'),('LOOPS','LOOP','Loop'),('MALL','MALL','Mall'),('MANOR','MNR','Mnr'),('MNR','MNR','Mnr'),('MANORS','MNRS','Mnrs'),('MNRS','MNRS','Mnrs'),('MDW','MDW','Mdw'),('MEADOW','MDW','Mdw'),('MDWS','MDWS','Mdws'),('MEADOWS','MDWS','Mdws'),('MEDOWS','MDWS','Mdws'),('MEWS','MEWS','Mews'),('MILL','ML','Ml'),('ML','ML','Ml'),('MILLS','MLS','Mls'),('MLS','MLS','Mls'),('MISSION','MSN','Msn'),('MISSN','MSN','Msn'),('MSN','MSN','Msn'),('MSSN','MSN','Msn'),('MOTORWAY','MTWY','Mtwy'),('MNT','MT','Mt'),('MOUNT','MT','Mt'),('MT','MT','Mt'),('MNTAIN','MTN','Mtn'),('MNTN','MTN','Mtn'),('MOUNTAIN','MTN','Mtn'),('MOUNTIN','MTN','Mtn'),('MTIN','MTN','Mtn'),('MTN','MTN','Mtn'),('MNTNS','MTNS','Mtns'),('MOUNTAINS','MTNS','Mtns'),('NCK','NCK','Nck'),('NECK','NCK','Nck'),('ORCH','ORCH','Orch'),('ORCHARD','ORCH','Orch'),('ORCHRD','ORCH','Orch'),('OVAL','OVAL','Oval'),('OVL','OVAL','Oval'),('OVERPASS','OPAS','Opas'),('PARK','PARK','Park'),('PK','PARK','Park'),('PRK','PARK','Park'),('PARKS','PARK','Park'),('PARKWAY','PKWY','Pkwy'),('PARKWY','PKWY','Pkwy'),('PKWAY','PKWY','Pkwy'),('PKWY','PKWY','Pkwy'),('PKY','PKWY','Pkwy'),('PARKWAYS','PKWY','Pkwy'),('PKWYS','PKWY','Pkwy'),('PASS','PASS','Pass'),('PASSAGE','PSGE','Psge'),('PATH','PATH','Path'),('PATHS','PATH','Path'),('PIKE','PIKE','Pike'),('PIKES','PIKE','Pike'),('PINE','PNE','Pne'),('PINES','PNES','Pnes'),('PNES','PNES','Pnes'),('PL','PL','Pl'),('PLACE','PL','Pl'),('PLAIN','PLN','Pln'),('PLN','PLN','Pln'),('PLAINES','PLNS','Plns'),('PLAINS','PLNS','Plns'),('PLNS','PLNS','Plns'),('PLAZA','PLZ','Plz'),('PLZ','PLZ','Plz'),('PLZA','PLZ','Plz'),('POINT','PT','Pt'),('PT','PT','Pt'),('POINTS','PTS','Pts'),('PTS','PTS','Pts'),('PORT','PRT','Prt'),('PRT','PRT','Prt'),('PORTS','PRTS','Prts'),('PRTS','PRTS','Prts'),('PR','PR','Pr'),('PRAIRIE','PR','Pr'),('PRARIE','PR','Pr'),('PRR','PR','Pr'),('RAD','RADL','Radl'),('RADIAL','RADL','Radl'),('RADIEL','RADL','Radl'),('RADL','RADL','Radl'),('RAMP','RAMP','Ramp'),('RANCH','RNCH','Rnch'),('RANCHES','RNCH','Rnch'),('RNCH','RNCH','Rnch'),('RNCHS','RNCH','Rnch'),('RAPID','RPD','Rpd'),('RPD','RPD','Rpd'),('RAPIDS','RPDS','Rpds'),('RPDS','RPDS','Rpds'),('REST','RST','Rst'),('RST','RST','Rst'),('RDG','RDG','Rdg'),('RDGE','RDG','Rdg'),('RIDGE','RDG','Rdg'),('RDGS','RDGS','Rdgs'),('RIDGES','RDGS','Rdgs'),('RIV','RIV','Riv'),('RIVER','RIV','Riv'),('RIVR','RIV','Riv'),('RVR','RIV','Riv'),('RD','RD','Rd'),('ROAD','RD','Rd'),('RDS','RDS','Rds'),('ROADS','RDS','Rds'),('ROUTE','RTE','Rte'),('ROW','ROW','Row'),('RUE','RUE','Rue'),('RUN','RUN','Run'),('SHL','SHL','Shl'),('SHOAL','SHL','Shl'),('SHLS','SHLS','Shls'),('SHOALS','SHLS','Shls'),('SHOAR','SHR','Shr'),('SHORE','SHR','Shr'),('SHR','SHR','Shr'),('SHOARS','SHRS','Shrs'),('SHORES','SHRS','Shrs'),('SHRS','SHRS','Shrs'),('SKYWAY','SKWY','Skwy'),('SPG','SPG','Spg'),('SPNG','SPG','Spg'),('SPRING','SPG','Spg'),('SPRNG','SPG','Spg'),('SPGS','SPGS','Spgs'),('SPNGS','SPGS','Spgs'),('SPRINGS','SPGS','Spgs'),('SPRNGS','SPGS','Spgs'),('SPUR','SPUR','Spur'),('SPURS','SPUR','Spur'),('SQ','SQ','Sq'),('SQR','SQ','Sq'),('SQRE','SQ','Sq'),('SQU','SQ','Sq'),('SQUARE','SQ','Sq'),('SQRS','SQS','Sqs'),('SQUARES','SQS','Sqs'),('STA','STA','Sta'),('STATION','STA','Sta'),('STATN','STA','Sta'),('STN','STA','Sta'),('STRA','STRA','Stra'),('STRAV','STRA','Stra'),('STRAVE','STRA','Stra'),('STRAVEN','STRA','Stra'),('STRAVENUE','STRA','Stra'),('STRAVN','STRA','Stra'),('STRVN','STRA','Stra'),('STRVNUE','STRA','Stra'),('STREAM','STRM','Strm'),('STREME','STRM','Strm'),('STRM','STRM','Strm'),('ST','ST','St'),('STR','ST','St'),('STREET','ST','St'),('STRT','ST','St'),('STREETS','STS','Sts'),('SMT','SMT','Smt'),('SUMIT','SMT','Smt'),('SUMITT','SMT','Smt'),('SUMMIT','SMT','Smt'),('TER','TER','Ter'),('TERR','TER','Ter'),('TERRACE','TER','Ter'),('THROUGHWAY','TRWY','Trwy'),('TRACE','TRCE','Trce'),('TRACES','TRCE','Trce'),('TRCE','TRCE','Trce'),('TRACK','TRAK','Trak'),('TRACKS','TRAK','Trak'),('TRAK','TRAK','Trak'),('TRK','TRAK','Trak'),('TRKS','TRAK','Trak'),('TRAFFICWAY','TRFY','Trfy'),('TRFY','TRFY','Trfy'),('TR','TRL','Trl'),('TRAIL','TRL','Trl'),('TRAILS','TRL','Trl'),('TRL','TRL','Trl'),('TRLS','TRL','Trl'),('TUNEL','TUNL','Tunl'),('TUNL','TUNL','Tunl'),('TUNLS','TUNL','Tunl'),('TUNNEL','TUNL','Tunl'),('TUNNELS','TUNL','Tunl'),('TUNNL','TUNL','Tunl'),('TPK','TPKE','Tpke'),('TPKE','TPKE','Tpke'),('TRNPK','TPKE','Tpke'),('TRPK','TPKE','Tpke'),('TURNPIKE','TPKE','Tpke'),('TURNPK','TPKE','Tpke'),('UNDERPASS','UPAS','Upas'),('UN','UN','Un'),('UNION','UN','Un'),('UNIONS','UNS','Uns'),('VALLEY','VLY','Vly'),('VALLY','VLY','Vly'),('VLLY','VLY','Vly'),('VLY','VLY','Vly'),('VALLEYS','VLYS','Vlys'),('VLYS','VLYS','Vlys'),('VDCT','VIA','Via'),('VIA','VIA','Via'),('VIADCT','VIA','Via'),('VIADUCT','VIA','Via'),('VIEW','VW','Vw'),('VW','VW','Vw'),('VIEWS','VWS','Vws'),('VWS','VWS','Vws'),('VILL','VLG','Vlg'),('VILLAG','VLG','Vlg'),('VILLAGE','VLG','Vlg'),('VILLG','VLG','Vlg'),('VILLIAGE','VLG','Vlg'),('VLG','VLG','Vlg'),('VILLAGES','VLGS','Vlgs'),('VLGS','VLGS','Vlgs'),('VILLE','VL','Vl'),('VL','VL','Vl'),('VIS','VIS','Vis'),('VIST','VIS','Vis'),('VISTA','VIS','Vis'),('VST','VIS','Vis'),('VSTA','VIS','Vis'),('WALK','WALK','Walk'),('WALKS','WALK','Walk'),('WALL','WALL','Wall'),('WAY','WAY','Way'),('WY','WAY','Way'),('WAYS','WAYS','Ways'),('WELL','WL','Wl'),('WELLS','WLS','Wls'),('WLS','WLS','Wls');
-- ;


--
-- Dumping data for table `secondary_unit_desig`
--
REPLACE INTO `secondary_unit_desig` VALUES ('APARTMENT','APT','','Apt'),('BASEMENT','BSMT','\0','Bsmt'),('BUILDING','BLDG','','Bldg'),('DEPARTMENT','DEPT','','Dept'),('FLOOR','FL','','Fl'),('FRONT','FRNT','\0','Frnt'),('HANGER','HNGR','','Hngr'),('KEY','KEY','','Key'),('LOBBY','LBBY','\0','Lbby'),('LOT','LOT','','Lot'),('LOWER','LOWR','\0','Lowr'),('OFFICE','OFC','\0','Ofc'),('PENTHOUSE','PH','\0','Ph'),('PIER','PIER','','Pier'),('REAR','REAR','\0','Rear'),('SIDE','SIDE','\0','Side'),('SLIP','SLIP','','Slip'),('SPACE','SPC','','Spc'),('STOP','STOP','','Stop'),('SUITE','STE','','Ste'),('TRAILER','TRLR','','Trlr'),('UNIT','UNIT','','Unit'),('UPPER','UPPR','\0','Uppr'),('APT','APT','\0','Apt'),('BLDG','BLDG','','Bldg'),('DEPT','DEPT','','Dept'),('FL','FL','','Fl'),('FRNT','FRNT','\0','Frnt'),('HNGR','HNGR','','Hngr'),('LBBY','LBBY','\0','Lbby'),('LOWR','LOWR','\0','Lowr'),('OFC','OFC','\0','Ofc'),('PH','PH','\0','Ph'),('SPC','SPC','','Spc'),('STE','STE','','Ste'),('TRLR','TRLR','','Trlr'),('UPPR','UPPR','\0','Uppr'),('RM','RM','','Rm'),('ROOM','RM','','Rm');
-- ;



--
-- Dumping data for table `web_sites`
--
REPLACE INTO `web_sites` VALUES
(1,'a','Admin','/admin/','mm','ui-icon ui-icon-gear','',now(),'admin','NameSch.php','index.php','localhost'),
(3,'v','Volunteer','/volunteer/','v','ui-icon ui-icon-heart','',now(),'admin','VolAction.php','index.php','localhost'),
(4,'r','Root','/','pub','','',now(),'admin','','','localhost');
-- ;


--
-- Dumping data for table `page`
--
INSERT INTO `page` VALUES (1,'index.php',0,'Welcome','r','','','p','','','0000-00-00 00:00:00','2011-09-21 16:56:57'),(2,'index.php',0,'','a','','','p','','admin','2011-09-28 15:52:50','2011-09-21 17:00:18'),(3,'NameEdit.php',2,'Edit Members','a','','','p','','admin','2014-07-25 12:27:19','2011-09-21 17:01:42'),(4,'EventShells.php',2,'Repeat Events','a','35','f','p','','','0000-00-00 00:00:00','2011-09-21 19:52:06'),(5,'KeyStats.php',2,'Key Stats','a','67','g','p','','admin','2012-06-11 14:28:56','2011-09-21 19:52:06'),(6,'Misc.php',2,'Miscellaneous','a','34','a','p','','admin','2012-04-09 12:04:46','2011-09-21 19:52:06'),(7,'PageEdit.php',2,'Edit Pages','a','34','e','p','','','0000-00-00 00:00:00','2011-09-21 19:52:06'),(8,'RegisterUser.php',2,'Register Web Users','a','35','e','p','','admin','2012-03-15 08:51:37','2011-09-21 19:52:06'),(9,'CategoryEdit.php',2,'Edit Categories','a','34','d','p','','admin','2012-01-18 11:55:50','2011-09-21 19:52:06'),
(10,'VolListing.php',2,'Web Users','a','35','c','p','','admin','2011-10-31 14:41:12','2011-09-21 19:52:06'),(11,'campaignEdit.php',2,'Edit Campaigns','a','34','c','p','','','0000-00-00 00:00:00','2011-09-21 19:56:43'),(12,'campaignReport.php',2,'Campaigns','a','32','d','p','','','0000-00-00 00:00:00','2011-09-21 19:56:43'),(13,'checkDateReport.php',2,'Check Date','a','32','j','p','','','0000-00-00 00:00:00','2011-09-21 19:56:43'),(14,'directory.php',2,'Directory','a','32','a','p','','','0000-00-00 00:00:00','2011-09-21 19:56:43'),(15,'donate.php',0,'','a','','','s','','','0000-00-00 00:00:00','2011-09-21 19:56:43'),(16,'donationReport.php',2,'Donations','a','32','b','p','','admin','2011-12-12 11:32:31','2011-09-21 19:56:43'),(17,'dormantEdit.php',2,'Edit Dormancies','a','34','b','p','','','0000-00-00 00:00:00','2011-09-21 19:56:43'),(18,'liveGetCamp.php',0,'','a','','','s','','','0000-00-00 00:00:00','2011-09-21 19:56:43'),(19,'liveNameSearch.php',0,'','a','','','s','','','0000-00-00 00:00:00','2011-09-21 19:56:43'),
(20,'ws_Report.php',0,'','a','','','s','','','0000-00-00 00:00:00','2011-09-21 19:56:43'),(21,'ws_gen.php',0,'','a','','','s','','','0000-00-00 00:00:00','2011-09-21 19:56:43'),(22,'VolNameEdit.php',26,'My Volunteer Info','v','0','d','p','','admin','2011-09-28 15:40:54','2011-09-21 20:01:58'),(23,'forgotpw.php',26,'Forgot My Password','v','','','p','','admin','2011-09-28 15:54:43','2011-09-21 20:01:58'),(24,'gCalFeed.php',0,'','v','','','s','','','0000-00-00 00:00:00','2011-09-21 20:01:58'),(26,'index.php',0,'','v','','','p','','admin','2011-09-28 15:50:30','2011-09-21 20:01:58'),(27,'register_web.php',26,'Register','v','','','p','','admin','2011-09-28 15:53:57','2011-09-21 20:01:58'),(28,'ws_reg_user.php',0,'','v','','','s','','','0000-00-00 00:00:00','2011-09-21 20:01:58'),(29,'ws_vol.php',0,'','v','','','s','','','0000-00-00 00:00:00','2011-09-21 20:01:58'),
(32,'_directory.php',2,'Reports','a','0','e','p','','','0000-00-00 00:00:00','2011-09-22 13:20:36'),(33,'categoryReport.php',2,'Categories','a','32','f','p','','admin','2013-12-10 13:09:01','2011-09-22 13:25:04'),(34,'_Misc.php',2,'DB Maintenance','a','0','k','p','','admin','2011-10-13 10:42:35','2011-09-22 13:26:38'),(35,'_VolListing.php',2,'Web Users','a','0','j','p','','admin','2011-10-31 14:40:58','2011-09-22 13:27:25'),(36,'NameEdit_Donations',0,'','a','','','c','','','0000-00-00 00:00:00','2011-09-23 09:07:22'),(37,'NameEdit_Maint',0,'','a','','','c','','admin','2011-09-26 15:15:27','2011-09-27 12:24:53'),(39,'ws_gen_Maint',0,'','a','','','c','','admin','2011-09-26 15:41:54','2011-09-27 14:41:54'),
(45,'VolNameSearch.php',0,'','v','','','s','','admin','2011-10-09 19:24:53','2011-10-10 18:24:53'),(47,'guestaccess',0,'','v','','','c','','admin','2011-10-17 15:23:29','2011-10-18 14:23:29'),(48,'PrivacyGroup',0,'','a','','','c','','admin','2011-10-31 20:38:16','2011-11-01 19:38:16'),(49,'recent.php',2,'Recent Changes','a','67','r','p','','admin','2012-06-11 14:29:48','2011-11-03 12:20:26'),
(50,'nonReportables.php',2,'Non-Reportables','a','67','v','p','','admin','2012-06-11 14:29:29','2011-12-03 19:06:32'),(51,'donorReport.php',2,'Donors','a','32','c','p','','admin','2011-12-24 17:42:31','2011-12-13 13:59:14'),(52,'procDuplicate.php',2,'','a','3','none','p','','admin','2012-01-16 16:49:18','2012-01-12 19:48:53'),(55,'MemEdit.php',0,'','v','','none','p','','admin','2012-02-07 16:36:02','2012-02-08 18:36:02'),(56,'Cat_Donor',0,'','a','','','c','','admin','2012-02-29 11:19:02','2012-03-01 13:19:02'),(57,'anomalies.php',2,'Anomaly report','a','67','k','p','','admin','2012-06-11 14:29:18','2012-03-08 23:28:42'),
(60,'guestaccess',0,'','a','','','c','','admin','2012-03-26 14:04:37','2012-03-27 13:04:37'),(64,'reportWindow.php',2,'Report','a','','','p','','admin','2012-05-31 11:06:52','2012-05-30 10:28:52'),(65,'timeReport.php',2,'Time Reports','a','32','u','p','','admin','2012-06-04 13:47:31','2012-06-05 12:47:31'),(66,'NameSch.php',2,'Members','a','0','d','p','','admin','2014-11-08 09:25:40','2012-06-12 13:22:04'),(67,'_KeyStats.php',2,'Key Stats','a','0','g','p','','admin','2012-06-11 14:28:41','2012-06-12 13:28:41'),(68,'VolAction.php',26,'Activities','v','0','b','p','','admin','2012-09-03 16:37:32','2012-06-12 14:21:41'),(69,'_index.php?log=lo',0,'Log Out','a','0','z','p','','admin','2012-06-17 13:07:24','2012-06-18 12:05:10'),
(70,'_index.php?log=lo',0,'Log Out','v','0','z','p','','admin','2012-06-17 13:08:23','2012-06-18 12:05:10'),
(80,'HouseCal.php',26,'','v','','','p','','admin','2013-03-03 17:31:24','2013-03-04 19:30:30'),(86,'Portal.php',1,'Portal','r','0','a','p','','admin','2013-08-04 12:07:56','2013-08-05 11:07:56'),(87,'ws_reg.php',0,'','r','','','s','','admin','2013-08-04 13:40:29','2013-08-05 12:40:29'),(88,'AuthGroupEdit.php',2,'Edit Authorization','a','34','j','p','','admin','2013-08-07 15:13:41','2013-08-08 14:13:05'),(89,'Configure.php',2,'Site Configuration','a','34','g','p','','admin','2013-08-17 10:11:05','2013-08-18 09:10:27'),
(105,'PaymentReport.php',2,'Credit Transactions Report','a','32','v','p','','admin','2014-08-13 12:25:03','2014-08-13 23:25:03');
-- ;

--
-- Dumping data for table `page_securitygroup`
--
INSERT INTO `page_securitygroup` VALUES (1,'pub','2011-09-29 15:03:46'),(2,'pub','2011-09-24 08:14:44'),(3,'mm','2011-09-21 20:21:42'),(4,'mm','2011-09-21 20:21:42'),(5,'mm','2011-09-21 20:21:42'),(6,'db','2011-09-21 20:21:42'),(7,'db','2011-09-21 20:21:42'),(8,'mm','2011-09-21 20:21:42'),(9,'db','2011-09-21 20:21:42'),
(10,'mm','2011-09-21 20:21:42'),(11,'db','2011-09-21 20:21:42'),(12,'dm','2011-09-21 20:21:42'),(13,'mm','2011-09-21 20:21:42'),(14,'mm','2011-09-21 20:21:42'),(15,'dm','2011-09-21 20:21:42'),(16,'dm','2011-09-21 20:21:42'),(18,'mm','2011-09-21 20:21:42'),(19,'mm','2011-09-21 20:21:42'),
(20,'dm','2011-09-21 20:21:42'),(21,'mm','2011-09-21 20:21:42'),(22,'v','2011-09-21 20:24:43'),(23,'pub','2011-09-29 15:01:12'),(24,'v','2011-09-21 20:24:43'),(26,'pub','2011-09-24 08:15:17'),(27,'pub','2011-09-29 15:02:08'),(28,'pub','2011-09-29 15:02:08'),(29,'v','2011-09-21 20:24:43'),
(31,'pub','2011-09-24 08:15:48'),(32,'mm','2011-09-22 15:36:57'),(33,'mm','2011-09-22 15:48:59'),(34,'db','2011-09-22 15:36:57'),(35,'mm','2011-09-22 15:36:57'),(36,'dm','2011-09-24 10:22:47'),(37,'db','2011-09-27 12:24:53'),(39,'db','2011-09-27 14:41:54'),
(45,'v','2011-10-10 18:24:53'),(48,'p','2011-11-01 19:38:16'),(49,'mm','2011-11-03 12:20:26'),
(50,'mm','2011-12-03 19:06:32'),(51,'dna','2011-12-13 13:59:14'),(52,'dm','2012-01-12 19:48:53'),(55,'v','2012-02-08 18:36:02'),(56,'dna','2012-03-01 13:19:02'),(57,'mm','2012-03-08 23:28:42'),
(64,'mm','2012-05-30 10:28:52'),(65,'mm','2012-06-05 12:47:31'),(66,'mm','2012-06-12 13:22:04'),(67,'mm','2012-06-12 13:28:41'),(68,'v','2012-06-12 14:21:41'),(69,'pub','2012-06-18 12:10:47'),
(70,'pub','2012-06-18 12:10:47'),(71,'pub','2012-06-18 12:10:47'),
(80,'v','2013-03-04 19:30:30'),(81,'db','2013-03-26 12:19:56'),(82,'db','2013-03-26 14:01:03'),(84,'mm','2013-06-07 10:17:02'),(86,'mm','2013-08-05 11:07:56'),(86,'v','2013-08-05 11:07:56'),(87,'pub','2013-08-05 12:40:29'),(88,'db','2013-08-08 14:13:05'),(89,'db','2013-08-18 09:10:27'),
(105,'db','2014-08-13 23:25:03');


