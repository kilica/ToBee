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

