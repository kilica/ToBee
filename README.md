# ToBee
Convert Selenium .side to ShouldBee test script

Selenium IDE で記録した操作を保存した .side ファイルの JSON を、[ShouldBee](https://shouldbee.at/) のテストスクリプトにざっくり変換します。
現状では Selenium IDE で記録した以下の command にしか対応していません ^ ^;;
ソースコードも大変てきとうです。

* Open
* ClickAt
* Type
* Select

Selenium IDE の command 名（Open, ClickAt など）をクラス名にしたクラスを追加することで対応できます。

## デモサイト

[ToBee](http://jp.xoopsdev.com/toBee/convert.php)
