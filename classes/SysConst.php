<?php
/**
 * SysConst.php
 *
 * @category  Code Support
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

class ActivityTypes {
    const Donation = 'don';
    const Volunteer = 'vol';
}

class Address_Purpose {
    const Home = '1';
    const Work = '2';
    const Alt = '3';
    const Office = '4';
}

class Attribute_Types {
    const Room = '1';
    const Hospital = '2';
    const House = '3';
    const Resource = '4';
}

class CampaignType {
    const Normal = 'as';
    const Percent = 'pct';
    const InKind = 'ink';
    const Scholarship = 'sch';
}

class Constraint_Type {
    const Reservation = 'rv';
    const Hospital = 'hos';
    const Visit = 'v';
}

class DbTable {
    const Donations = "donations";
    const GenLookups = "gen_lookups";
    const Name = "name";
    const NameVolunteer = "name_volunteer2";
    const NameAddress = "name_address";
    const NameEmail = "name_email";
    const NamePhone = "name_phone";
    const NameLog = "name_log";
    const NameCrypto = "name_crypto";
    const Relationship = "relationship";
    const WebUsers = "w_users";
}

class Default_Settings {
    const Rate_Category = 'e';
    const Fixed_Rate_Category = 'x';
}

class Email_Purpose {
    const Home = '1';
    const Work = '2';
    const Alt = '3';
    const Office = '4';
}

class FeesType {
    const RoomFee = "r";
    const KeyDeposit = 'k';
}

class FeesPaymentStatus {
    const Cleared = 'c';
    const Pending = 'p';
    const Denied = 'd';
    const Void = 'v';
    const Returned = 'r';
    const Error = 'er';
}

class FinAppStatus {
    const Granted = 'a';
    const Denied = 'n';
}

class GL_TableNames {
    const AddrPurpose = 'Address_Purpose';
    const EmailPurpose = 'Email_Purpose';
    const PhonePurpose = 'Phone_Type';
    const Gender = 'Gender';
    const MemberBasis = 'Member_Basis';
    const MemberStatus = 'mem_status';
    const NamePrefix = 'Name_Prefix';
    const NameSuffix = 'Name_Suffix';
    const RoleCode = 'Role_Codes';
    const PatientRel = 'Patient_Rel_Type';
    const RelTypes = 'rel_type';
    const Hospital = 'Hospitals';
    const RescType = 'Resource_Type';
    const RescStatus = 'Resource_Status';
    const PayType = 'Pay_Type';
    const WL_Status = 'WL_Status';
    const WL_Final_Status = 'WL_Final_Status';
    const FeesPayType = 'FeesPayType';
    const KeyDispositions = 'Key_Disposition';
    const SalutationCodes = 'Salutation';
    const AgeBracket = 'Age_Bracket';
    const Ethnicity = 'Ethnicity';
    const SpecialNeeds = 'Special_Needs';
    const RateCode = 'Rate_Code';
    const RoomType = 'Room_Type';
    const RoomStatus = 'Room_Status';
    const Patient = 'Patient';
    const RoomCategory = 'Room_Category';
    const KeyDepositCode = 'Key_Deposit_Code';
}

class InvoiceStatus {
    const Paid = 'p';
    const Unpaid = 'up';
    const Carried = 'c';
}

class KeyDisposition {
    const Refunded = '2';
    const Retained = '1';
    const Donated = '3';
    const PayFees = '4';
}

class MemBasis {
    const Indivual = "ai";
    const Student = 'bs';
    const Company = "c";
    const NonProfit = "np";
    const Government = 'og';
}

class MemDesignation {
    const Individual = "i";
    const Organization = "o";
    const Student = 's';
    const Not_Set = "n";
}

class MemGender {
    const Male = 'm';
    const Female = 'f';
    const Other = 't';
}

class MemStatus {
    const Active = "a";
    const Inactive = "in";
    const Deceased = "d";
    const Pending = "p";
    const ToBeDeleted = "TBD";
    const Duplicate = "u";
}


// operating mode of site, live, demo or training
// in site.cfg file.
class Mode {
    const Live = "live";
    const Demo = "demo";
    const Training = "train";
}

class OrderStatusCode {
    const Active = 'a';
    const Finished = 'f';
    const Suspended = 's';
    const SuspendAging = 'sa';
}

class PayType {
    const Cash = 'ca';
    const Charge = 'cc';
    const Check = 'ck';
    const Other = 'ot';
}

class PaymentStatusCode {
    const Paid = 's';
    const VoidSale = 'v';
    const Retrn = 'r';
    const VoidReturn = "vr";
}

class Phone_Purpose {
    const Home = 'dh';
    const Work = 'gw';
    const Cell = 'mc';
    const Fax = 'xf';
    const Office = 'hw';
}

class RelLinkType {
    const Spouse = "sp";
    const Child = "chd";
    const Parnt = "par";
    const Sibling = "sib";
    const Employee = "emp";
    const Relative = "rltv";
    const Friend = "frd";
    const Company = "co";
    const Self = 'slf';
}

class ReservationStatus {
    const Committed = 'a';
    const Waitlist = 'w';
    const NoShow = 'ns';
    const TurnDown = 'td';
    const Canceled = 'c';
    const Pending = 'p';
    const Staying = 's';
    const Checkedout = 'co';
}

class ResourceStatus {
    const Unavailable = 'un';
    const Available = 'a';
    const OutOfService = 'oos';
}

class RoomRateCategorys {
    const FlatRateCategory = 'e';
    const Fixed_Rate_Category = 'x';
}

class RoomState {
    const Dirty = 'dty';
    const Clean = 'a';
    const TurnOver = 'to';
}
class RoomAvailable {
    const Unavailable = 'un';
    const Available = 'a';
}
class RoomService {
    const OutOfService = 'oos';
    const InService = 'a';
}

class RoomType {
    const Room =  'r';
    const Suite = 's';
    const Host  = 'hr';
    const Hotel = 'mr';
}

class SalutationCodes {
    const Formal = 'for';
    const FirstOnly = 'fno';
    const FirstLast = 'fln';
    const Retro = 'mm';
}

class SalutationPurpose {
    const Envelope = 'e';
    const Letter = "l";
}

class TransMethod {
    const Token = 'tkn';
    const HostedPayment = 'hp';
    const Cash = 'cash';
    const Check = 'check';
}

class TransType {
    const Sale = 's';
    const Void = 'vs';
    const Retrn = 'r';
    const VoidReturn = 'vr';
}

class VisitStatus {
    const Active = "a";
    const CheckedIn = "a";
    const CheckedOut = "co";
    const Pending = "p";
    const NewSpan = "n";
    const ChangeRate = "cp";
}

// Calendar status for Table mcalendar
class Vol_Calendar_Status {
    const Active = 'a';
    const Logged = 't'; // Time is logged in the volunteer time table.
    const Deleted = 'd';
}

class VolStatus {
    const Active = 'a';
    const Retired = 'i';
}

class VolRank {
    const Chair = "c";
    const CoChair = "cc";
    const Member = "m";
    const Guest = "rg";
}

class VolMemberType {
    const VolCategoryCode = 'Vol_Type';
    const Guest = 'g';
    const Patient = 'p';
    const Donor = 'd';
    const ReferralAgent = 'ra';
}

class WebRole {
    const DefaultRole = 100;
    const WebUser = 100;
    const Admin = 10;
    const Guest = 700;
}

class WebPageCode {
    const Page = 'p';
    const Component = 'c';
    const Service = 's';
}

class WebSiteCode {
    const  House = 'h';
    const Volunteer = 'v';
    const Admin = 'a';
    const Root = 'r';
}

class WL_Status {
    const Active = 'a';
    const Inactive = 'in';
    const Stayed = 'st';
    const NoShow = 'ns';
    const TurnedAway = 'ta';
}
