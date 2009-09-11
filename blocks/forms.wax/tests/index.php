<?php
	require_once "../../../wax_init.php";
	
	$theme = Wax::GetBlock("default");
	$block = Wax::LoadBlock("forms");
	$form = new Form();
?>
<link rel='stylesheet' type='text/css' href='<?=$theme->css("wax")?>' />
<table class='waxtable'>
	<tr>	
		<td><b>Textfield Test</b></td>
		<td><?=$form->TextField('nametest')?></td>
	</tr>
	<tr>
		<td><b>Password Test</b></td>
		<td><?=$form->PasswordField("passwordtest")?></td>
	</tr>
	<tr>
		<td><b>Reset Test</b></td>
		<td><?=$form->ResetButton("reset_test","Reset Test")?></td>
	</tr>
	<tr>
		<td><b>Submit Test</b></td>
		<td><?=$form->SubmitButton("submit_test","Submit Test")?></td>
	</tr>
	<tr>
		<td><b>File Test</b></td>
		<td><?=$form->FileField("filetest")?></td>
	</tr>
	<tr>
		<td><b>Textarea Test</b></td>
		<td><?=$form->Textarea("textarea_test")?></td>
	</tr>
	<tr>
		<td><b>Select Test</b></td>
		<td><?=$form->SelectMenu("select_test",array("option1" => "Option 1","option2" => "Option 2"));?></td>
	</tr>
</table>