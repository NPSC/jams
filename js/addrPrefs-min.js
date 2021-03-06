/* 
 * addrPrefs-min.js
 * 
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 *  
 */
function addrPrefs(e){"use strict";$("input.prefPhone").each(function(){if(this.checked){e.phonePref=this.value}});$("input.prefEmail").each(function(){if(this.checked){e.emailPref=this.value}});$("input.addrPrefs").each(function(){if(this.checked){e.addrPref=this.value}});$("input.addrPrefs").click(function(){var t=this.value,n,r,i;n=document.getElementById("adraddress1"+t);r=document.getElementById("adrcity"+t);if(n!=null&&n.value==""||r!=null&&r.value==""){alert("This address is blank.  It cannot be the 'preferred' address.");this.checked=false;i=false;if(e.addrPref!=""&&$("#adraddress1"+e.addrPref).val()!=""){$("#rbPrefMail"+e.addrPref).prop("checked",true);i=true}if(!i){$("input.addrPrefs").each(function(){if($("#adraddress1"+this.value).val()!=""){$(this).prop("checked",true);e.addrPref=this.value}})}}});$("input.prefPhone").change(function(){var t,n=$("#txtPhone"+this.value);if(n!==null&&n.val()==""){alert("This Phone Number is blank.  It cannot be the 'preferred' phone number.");this.checked=false;t=false;if(e.phonePref!=""&&$("#txtPhone"+e.phonePref).val()!=""){$("#ph"+e.phonePref).prop("checked",true);t=true}if(!t){$("input.prefPhone").each(function(){if($("#txtPhone"+this.value).val()!=""){$(this).prop("checked",true);e.phonePref=this.value;return}})}}});$("input.prefEmail").change(function(){var t=$("#txtEmail"+this.value),n;if(t!=null&&t.val()==""){alert("This Email Address is blank.  It cannot be the 'preferred' Email address.");n=false;this.checked=false;if(e.emailPref!=""&&$("#txtEmail"+e.emailPref).val()!=""){$("#em"+e.emailPref).prop("checked",true);n=true}if(!n){$("input.prefEmail").each(function(){if($("#txtEmail"+this.value).val()!=""){$(this).prop("checked",true);e.emailPref=this.value;return}})}}})}

