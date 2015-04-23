<html>
<body>
	<table cellspacing="0" cellpadding="0" width="100%"  align="center">
		<tr>
			<td>
				<table width="650" cellpadding="12" bgcolor="#f8f8f8" style="border-color: #e7e7e7;">
					<tr>
						<td>
							<img id="logo" src="https://droptobox.herokuapp.com/dist/img/logo@2x.png" width="300" height="68">
						</td>
					</tr>
				</table>
				<table cellpadding="12" width="650">
					<tr>
						<td style="font-family:Arial, sans-serif; font-size:16px; color:#333333;">
							<h1 style="font-size:18px;"><?= $sender_name ?> <?= isset($sender_email) ? "($sender_email)" : ""; ?> sent you an invitation to share files</h1>
						</td>
					</tr>
				</table>
				<table cellpadding="12" width="650">
					<tr>
						<td style="font-family:Arial, sans-serif; font-size:14px; color:#333333;">
							DroptoBox is a file sharing platform wich let you share your files with other people. You can upload the files you want to share with this group, or you can take a look at files already shared.
						</td>
					</tr>
				</table>
				<table cellpadding="12" width="650">
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
        <?php if($password): ?>
  				<table cellpadding="12" width="650">
  					<tr>
  						<td style="font-family:Arial, sans-serif; font-size:18px;">Password to access this folder is: <code><?=$password ?></code></td>
  					</tr>
  				</table>
        <?php endif; ?>
				<table cellpadding="12" width="650"	>
					<tr>
						<td style="font-family:Arial, sans-serif; font-size:18px; color:#FFF;" bgcolor="#157DB8" align="center">
							<a href="<?= $upload_url ?>" style="color:#fff;text-decoration:none;">Upload your files</a>
						</td>
						<td>&nbsp;</td>
						<td style="font-family:Arial, sans-serif; font-size:18px; color:#FFF;" bgcolor="#157DB8" align="center">
							<a href="<?= $dropbox_url ?>" style="color:#fff;text-decoration:none;">View other files</a>
						</td>
					</tr>
				</table>
				<table cellpadding="12" width="650">
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
				<table width="650" cellpadding="12" bgcolor="#f8f8f8" style="border-color: #e7e7e7;">
					<tr>
						<td style="font-family: Arial, Helvetica, sans-serif; font-size:10px; color:#333333;">
							<i>Want to create your own public drag and drop Dropbox page? <a href="http://www.droptobox.com">Click here</a></i>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html
