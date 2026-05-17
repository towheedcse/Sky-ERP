<?php
/**
* Extracts and prepares EmployeeId
* @param String matched text by RegExp
* @return String padded with proper values
*/

function sendMail($strTo,$first_name,$job_seeker_id,$varification_no,$password)
{
   			$strMailbody="<html>
						<body>
						<table width='90%' border='0' cellspacing='0' cellpadding='0' align='center'>
						  <tr> 
							<td width='11%'>&nbsp;</td>
							<td width='39%'>&nbsp;</td>
							<td width='41%'>&nbsp;</td>
							<td width='9%'>&nbsp;</td>
						  </tr>
						  <tr> 
							<td height='56'>&nbsp;</td>
							<td colspan='2'> Dear ";
				$strMailbody.= $first_name;
				$strMailbody.="<br>
							  Welcome to MyJobsbd and thank you for signing up with Jobsbd.com. ---------------------------------------------------------------------- 
							</td>
							<td>&nbsp;</td>
						  </tr>
						  <tr> 
							<td>&nbsp;</td>
							<td colspan='2'><font color='#993300'><strong>IMMEDIATE ACTION REQUIRED</strong></font> 
							  <p>Please click the link below to let us know you have received this email. 
								We want to make sure your email is working as we, and employers, will 
								contact you through email when you apply for jobs.<br><a href='http://jobsbd.daffodil-bd.com/?app=jobseeker_signup&cmd=emailvalidate&id=".$job_seeker_id."&rn=".$varification_no."'>
								http://localhost/index.php?app=jobseeker_signup&cmd=emailvalidate&id=";
						$strMailbody.=$job_seeker_id;
						$strMailbody.="&rn=";
						$strMailbody.=$varification_no;
						$strMailbody.="</a><br>
								(If the above link does not work, please copy the full address and paste 
								it to your Internet browser)</p></td>
							<td>&nbsp;</td>
						  </tr>
						  <tr> 
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						  </tr>
						  <tr> 
							<td>&nbsp;</td>
							<td colspan='2'>We also recommend that you add Jobsbd.com to your address 
							  book so that you do not miss any email from us. 
							  <p>*If you did not sign up with Jobsbd.com, please let us know and you will 
								not receive any further email from us.<br>
								----------------------------------------------------------------------</p></td>
							<td>&nbsp;</td>
						  </tr>
						  <tr> 
							<td>&nbsp;</td>
							<td colspan='2'>Here are your account details.<br/> <p><b>Login ID : ".$strTo."<br>
								Password : ".$password."</b></p><br>
							  <p><font color='#FF0000'><b>
							  Save this email so that you always have a copy of your Login ID and Password, 
								and keep it securely. You will need it to access MyJobsbd.</b></font></p><br>
							  <p>You should be receiving job alert emails once you validate your email 
								address.</p><br>
							  <p>If you have not completed your resume, please complete it so that you 
								can apply to jobs online. You will also get more accurate job matches 
								based on extra information in your resume.</p><br>
							  <p> We're excited that you have signed up with us and trust you will find 
								our services useful for reaching more career opportunities, as it has 
								for other jobseekers.</p>
							 <p><br>
								Best Regards,<br>
								Daffodil, your Personal Career Agent<br>
								Jobsbd.com</p>
							  <p>To contact me, write to daffodil@jobsbd.com. Do not reply to this auto-generated 
								message.
							  </p></td>
							<td>&nbsp;</td>
						  </tr>
						  <tr> 
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						  </tr>
						  <tr> 
							<td>&nbsp;</td>
							<td colspan='2'>
							<table width='100%' border='1' cellpadding='0' cellspacing='0' bgcolor='#66CCCC'>
								<tr>
								  <td>
								  <strong><font color='#000099'>FEATURES OF MYJOBSBD</font></strong> <br>
							  Set up your Daffodil Job Alert Profile to receive job alerts that match your preferences:
							 <ul>
							 <li> Create your online resume</li>
							 <li> Apply to jobs online at Jobsbd.com easily</li>
							 <li> View your application status</li>
							 <li> And many other services!</li>
								</li>
							  </ul>
								  </td>
								</tr>
							  </table> </td>
							<td>&nbsp;</td>
						  </tr>
						  <tr> 
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						  </tr>
						</table>
						</body>
						</html>";
			  //$strTo ="md_farhaduddin@yahoo.com";
			  $strFrom ="ns3.daffodilnet.com";
			  $strSub ="Welcome to MyJobsbd";
			  if(@mail($strTo,$strSub,$strMailbody,"From:Jobsbd.com :$strFrom\r\nReply-to: $strFrom\r\nContent-type: text/html; charset=us-ascii"))
			  {
			  	return true;
			  }
			  else
			  {
			  	return false;
			  }
 }
?>