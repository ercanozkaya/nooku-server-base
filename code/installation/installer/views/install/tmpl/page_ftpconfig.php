<?php
/**
 * @version	$Id: ftpconfig.html 14401 2010-01-26 14:10:00Z louis $
 * @package	Joomla
 * @subpackage	Installation
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license	GNU/GPL
 */
?>

<script language="JavaScript" type="text/javascript">
<!--

	Window.onDomReady(function(){
	document.formvalidator.handlers['isftp'] = { enabled : true,
									exec : function (value) {
										if (document.getElementById('ftpenable').checked == true) {
											if (value == '') {
												return false;
											} else {
												return true;
											}
										} else {
											return true;
										}
									}
									}
	});

	function validateForm( frm, task ) {
		var valid = document.formvalidator.isValid(frm);
		if (valid == false) {
			return false;
		}

		var ftpEnable = document.getElementById("ftpenable");
		var ftpRoot = document.getElementById("ftproot");

		if (ftpEnable.checked == false) {
			submitForm( frm, task );
		} else if (ftpRoot.value == '') {
			alert( '<?php echo JText::_('warnFtpRoot') ?>' );
			return;
		} else {
			submitForm( frm, task );
		}
	}

	function doFTPVerify() {
		xajax_FTPVerify(xajax.getFormValues('adminForm'));
	}

	function JProcess() {

		if ( document.getElementById("ftphost").value == '' ) {
			alert( '<?php echo JText::_('validFtpHost') ?>' );
			return;
		} else if (document.getElementById("ftpuser").value == '') {
			alert( '<?php echo JText::_('validFtpUser') ?>' );
			return;
		} else if (document.getElementById("ftppass").value == '') {
			alert( '<?php echo JText::_('validFtpPass') ?>' );
			return;
		} else {
			xajax_getFtpRoot(xajax.getFormValues('adminForm'));
		}
	}
//-->
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate" autocomplete="off">
	<div id="toolbar-box">
		<div class="container_16 clearfix">
			<div class="grid_16">
				<h1><?php echo JText::_('FTP Configuration') ?></h1>
				<div class="buttons">
					<div class="button1-right"><div class="prev"><a onclick="submitForm( adminForm, 'dbconfig' );" alt="<?php echo JText::_('Previous') ?>"><?php echo JText::_('Previous') ?></a></div></div>
					<div class="button1-left"><div class="next"><a onclick="validateForm( adminForm, 'mainconfig' );" alt="<?php echo JText::_('Next') ?>"><?php echo JText::_('Next') ?></a></div></div>
				</div>
			</div>
		</div>
	</div>

	<div id="content-box">
		<div class="container_16 clearfix">
			<div class="grid_16">
				<h2><?php echo JText::_('FTP Configuration') ?>:</h2>
				<div class="grid_6 install-text">
					<?php echo JText::_('tipFtpConfSteps') ?>
				</div>
				<div class="grid_10 install-body">
					<fieldset>
						<h3 class="title-smenu" title="<?php echo JText::_('Basic') ?>"><?php echo JText::_('Basic Settings') ?></h3>
						<div class="section-smenu">
							<table class="content2">
								<tr>
									<td width="100">
										<input id="ftpenable" type="radio" name="vars[ftpEnable]" value="1" />
										<label for="vars_ftpenable">
											<?php echo JText::_('YES') ?>
										</label>
										<br />
										<input id="ftpdisable" type="radio" name="vars[ftpEnable]" value="0" checked="checked" />
										<label for="vars_ftpdisable">
											<?php echo JText::_('NO') ?>
										</label>
									</td>
									<td align="justify">
										<?php echo JText::_('ENABLEFTPDESC') ?>
									</td>
								</tr>
								<tr>
									<td width="100">
										<label for="ftpuser">
											<span id="ftpusermsg"><?php echo JText::_('FTP user') ?></span>
										</label>
									</td>
									<td align="center">
										<input class="inputbox validate notrequired isftp ftpusermsg" type="text" id="ftpuser" name="vars[ftpUser]" value="<?php echo isset($this->ftpUser) ? $this->ftpUser : '' ?>" size="30"/>
									</td>
								</tr>
								<tr>
									<td>
										<label for="ftppass">
											<span id="ftppassmsg"><?php echo JText::_('FTP password') ?></span>
										</label>
									</td>
									<td align="center">
										<input class="inputbox validate notrequired isftp ftppassmsg" type="password" id="ftppass" name="vars[ftpPassword]" value="<?php echo isset($this->ftpPassword) ? $this->ftpPassword : '' ?>" size="30"/>
									</td>
								</tr>
								<tr id="rootPath">
									<td>
										<label for="ftproot">
											<span id="ftprootmsg"><?php echo JText::_('FTP Root Path') ?></span>
										</label>
									</td>
									<td align="center">
										<input class="inputbox validate notrequired isftp ftprootmsg" id="ftproot" type="text" name="vars[ftpRoot]" value="<?php echo isset($this->ftpRoot) ? $this->ftpRoot : '' ?>" size="30"/>
									</td>
								</tr>
							</table>
							<input type="button" id="findbutton" class="button" value="<?php echo JText::_('Autofind FTP Path') ?>" onclick="JProcess();" />
							<input type="button" id="verifybutton" class="button" value="<?php echo JText::_('Verify FTP Settings') ?>" onclick="doFTPVerify();" />
							<br /><br />
						</div>
			
						<h3 class="title-smenu" title="<?php echo JText::_('Advanced') ?>"><?php echo JText::_('Advanced settings') ?></h3>
						<div class="section-smenu">
							<table class="content2">
								<tr id="host">
									<td width="100">
										<label for="ftphost">
											<?php echo JText::_('FTP host') ?>
										</label>
									</td>
									<td align="center">
										<input class="inputbox" type="text" id="ftphost" name="vars[ftpHost]" value="<?php echo isset($this->ftpHost) ? $this->ftpHost : '' ?>" size="30"/>
									</td>
								</tr>
								<tr id="port">
									<td width="100">
										<label for="ftpport">
											<?php echo JText::_('FTP port') ?>
										</label>
									</td>
									<td align="center">
										<input class="inputbox" type="text" id="ftpport" name="vars[ftpPort]" value="<?php echo isset($this->ftpPort) ? $this->ftpPort : '' ?>" size="30"/>
									</td>
								</tr>
								<tr>
									<td width="100">
										<label for="ftpsavepass">
											<?php echo JText::_('Save FTP Password') ?>
										</label>
									</td>
									<td align="justify">
										<input id="ftpsavepass" type="radio" name="vars[ftpSavePass]" value="1" />
										<label for="ftpsavepass">
											<?php echo JText::_('YES') ?>
										</label>
										<br />
										<input id="ftpnosavepass" type="radio" name="vars[ftpSavePass]" value="0" checked="checked" />
										<label for="ftpnosavepass">
											<?php echo JText::_('NO') ?>
										</label>
									</td>
								</tr>
							</table>
						</div>
						<div class="clr"></div>
					</div>
				</fieldset>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="lang" value="<?php echo $this->lang ?>" />
</form>