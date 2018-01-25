<?php

$output = "";
$input = isset($_POST['selenium']) ? $_POST['selenium'] : null;
if (isset($input)) {
	$json = json_decode($input);

	foreach ($json->tests[0]->commands as $row) {
		$id = $row->id;
		$command  = ucfirst($row->command);
		$target = $row->target;
		$value = $row->value;
		if (class_exists($command)) {
			$converter = new $command($command, $target, $value);
			$output .= $converter->convert();	
		}
	}
}

echo <<<END
<!DOCTYPE html>
<html lang="ja">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<title>Selenium to ShouldBee</title>
<meta name="description" content="Selenium の操作記録を ShouldBee のテストスクリプトに変換します">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<body>
  <h1>Selenium to ShouldBee</h1>
  <p>Selenium のコマンドの内容を <a href="https://shouldbee.at/">ShouldBee</a> のテストスクリプトにざっくり変換します。</p>
	<h2>Selenium</h2>
	<form action="convert.php" method="post">
		<fieldset>
			<textarea name="selenium" cols="60" rows="20">$input</textarea>
			<p>このテキストエリア内に Selenium の .side ファイル内の中身をペーストします。</p>
			<h3>貼り付けサンプル</h3>
			<textarea cols="60" rows="2">{"id":"d933e7ce-ee4b-46a1-94e2-c4bff336bf5f","name":"Untitled Project","url":"http://maebashi.demo.xacro.org","tests":[{"id":"d15e0b38-ecc1-4032-a6d7-c81b2b6f95e6","name":"Untitled","commands":[{"id":"370da03e-8a3b-4c9d-ae67-c1cb8e2b755f","command":"open","target":"/modules/info/index.php","value":""},{"id":"994b1ad2-f3d1-4cc4-8b3a-b6332ce81455","command":"clickAt","target":"id=legacy_xoopsform_title","value":"145,18"},{"id":"e0aede9b-8549-4aa6-9ee2-d07a3b511037","command":"clickAt","target":"//a[contains(@href, '/steps/FillField/')]","value":"352,6"},{"id":"bba5d841-b87a-4d96-8ef1-2e0732245a43","command":"type","target":"id=legacy_xoopsform_title","value":"使い方"},{"id":"d5400f31-14ba-42ba-8d28-c2d27647f902","command":"clickAt","target":"name=category_id","value":"135,12"},{"id":"372410a4-58f0-4428-8e76-b4409468f452","command":"runScript","target":["window.scrollTo(0,1133)"],"value":""},{"id":"d8f11def-14b6-4712-a816-f7d0a6f83343","command":"clickAt","target":"//a[contains(@href, '/steps/SelectOption/')]","value":"426,7"},{"id":"105b6b8e-fb39-4a49-bd15-7cd3d31e9ebe","command":"clickAt","target":"css=h2","value":"15,7"},{"id":"f8b6f004-428a-4deb-a62a-2026c0799e10","command":"runScript","target":["window.scrollTo(0,1133)"],"value":""},{"id":"6a21b355-abdb-45bf-8b4c-e8fef2c4f101","command":"clickAt","target":"//li[2]/label","value":"25,13"},{"id":"37cd8f82-1924-48e8-84b5-be356ef28853","command":"select","target":"name=category_id","value":"label=-ShouldBee"},{"id":"44998524-e6d6-4b90-81df-c24856a4be92","command":"clickAt","target":"name=category_id","value":"-343,-362"},{"id":"18bb1f5d-641c-4550-9df3-7ce53a2631c7","command":"type","target":"id=legacy_xoopsform_p_id","value":"2"},{"id":"5c6b10af-3d32-4081-9146-3a8ce9316751","command":"clickAt","target":"id=legacy_xoopsform_is_published_0","value":"7,4"},{"id":"f1a87f1d-356a-4eec-b544-100fdcef21a3","command":"clickAt","target":"css=li > label","value":"46,4"},{"id":"ce7d2f94-b706-4063-bf35-b0a525f34d44","command":"clickAt","target":"name=_form_control_confirm","value":"68,12"}]}],"suites":[],"urls":["http://maebashi.demo.xacro.org"]}</textarea>
			<hr />
			<input type="submit" value="変換" />
		</fieldset>
	</form>
	<h2>Shouldbee</h2>
	<form action="convert.php" method="post">
		<fieldset>
			<textarea name="selenium" cols="60" rows="20">$output</textarea>
			<p>このテキストエリア内の内容を ShouldBee のテストスクリプトに貼り付けます。</p>
		</fieldset>
	</form>
</body>
</html>
END;

abstract class AbstractCommandConverter
{
	protected $command;
	protected $target;
	protected $value;

	public function __construct($command, $target, $value)
	{
		$this->command = $command;
		$this->target = $target;
		$this->value = $value;
	}

	abstract public function convert();

	protected function convertTarget($target)
	{
		list($attr, $val) = explode('=', $target);
		if ($attr == 'id') {
			return '#' . $val;
		}
		if ($attr == 'class') {
			return '.' . $val;
		}
		if ($attr == 'name') {
			return $val;
		}
		if ($attr == 'label') {
			return $val;
		}
		if ($attr == 'css') {
			return $val;
		}
		return $target;
	}
	
	protected function convertValue($value)
	{
		return $value;
	}
}

class Open extends AbstractCommandConverter
{
	public function convert()
	{
		return sprintf("「%s」に移動する", $this->target) . "\n";
	}
}

class ClickAt extends AbstractCommandConverter
{
	public function convert()
	{
		$matches = array();
		if (preg_match("#//a\[contains\(text\(\),'「(.*)」'\)\]#", $this->target, $matches)) {
			return sprintf("「%s」のリンク先へ移動する", $matches[1]) . "\n";
		}
		$matches = array();
		if (preg_match("#css=(.*)#", $this->target, $matches)) {
			return sprintf("「%s」エレメントをクリックする", $matches[1]) . "\n";
		}
	}
}


class Type extends AbstractCommandConverter
{
	public function convert()
	{
		return sprintf("「%s」フィールドに「%s」と入力する", $this->convertTarget($this->target), $this->convertValue($this->value)) . "\n";
	}
}

class Select extends AbstractCommandConverter
{
	public function convert()
	{
		return sprintf("「%s」という値を「%s」から選択する", $this->convertValue($this->value), $this->convertTarget($this->target)) . "\n";
	}
}



class VerifyText extends AbstractCommandConverter
{
	public function convert()
	{
	}
}

class VerifyTitle extends AbstractCommandConverter
{
	public function convert()
	{
	}
}

class AssertText extends AbstractCommandConverter
{
	public function convert()
	{
	}
}

class AssertTitle extends AbstractCommandConverter
{
	public function convert()
	{
	}
}
class StoreText extends AbstractCommandConverter
{
	public function convert()
	{
	}
}

class StoreTitle extends AbstractCommandConverter
{
	public function convert()
	{
	}
}

