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

## ShouldBee

[ShouldBee](https://shouldbee.at/) はサイトの受け入れテスト（フォームを操作しての入力チェックや表示内容のチェックなど）がとっても楽になるサービスですが、テストケースを書くのはやっぱり大変です。
テストケースを書くのに、頭を使う部分と単純作業の部分とがありますが、単純作業の部分がちょっとでも楽になると良いなと思って作ってみました（というほどのものでもないけど）。
なので完璧に動作するものを作ろうと思って無くて、動いて実用的なら良いか、と作りました。たぶん、そのうち ShouldBee 自体がこうした機能を実装してくれるのではないかと思ってます！！

本当はクラスごとにファイルを分けたり色々した方がいいんでしょうが、お手軽にってことで1ファイルになってます。
