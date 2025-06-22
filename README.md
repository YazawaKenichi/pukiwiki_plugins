# pukiwiki_plugins
自作 PukiWiki プラグイン

## regexlist
正規表現にマッチするページの名前をリスト表示

### Usage
``` md
#regexlist(REGEX)
```

|引数|説明
|:---:|:---
| `REGEX` | 正規表現

## citeurl
ウェブサイトの参考文献フォーマットを記述

注意：絶対にこの記述が正しいというものではない

### Usage
``` md
#citeurl(URL)
```

|引数|説明
|:---:|:---
|URL|引用したい参考文献の URL

### 改良したいところ
'accessed' とか特にユーザ側で指定できるようにキーワード引数にしたい（すでにそうしたつもりだけど、想定した動作をしてない）

## twitter_timeline
Twitter の埋め込みタイムラインを Wiki に表示できるようにするプラグイン

このコードは [mgmn/pukiwiki-twitter_timeline.inc.php - GitHub](https://github.com/mgmn/pukiwiki-twitter_timeline.inc.php) から取得し、本プロジェクトに組み込んだものです。

ただうまくは行ってない

### License

MIT license

### Usage
``` md
#twitter_timeline(ID, [WIDTH], [HEIGHT], [THEME], [LANG])
```

|引数|説明
|:---:|:---
| `ID` | 接続先のデータベース名
| `WIDTH`, `HEIGHT` | 横幅 と 高さ [px]
| `THEME` | テーマ ( `light` / `dark` )
| `LANG` | 言語 ( `ja` / `en` / `zh-cn` など )

## dbwrite
YouTube のタイムスタンプと感想をデータベースに保存するプラグイン

このプラグインは [ほげほげ](https://github.com/yazawakenichi/hoge) に含まれています

### Usage

``` md
#dbwrite(DATABASE, TABLE)
```

|引数|説明
|:---:|:---
| `DATABASE` | 接続先のデータベース名
| `TABLE` | 接続先のテーブル名

## dbread
データベースに保存された YouTube のタイムスタンプと感想を表示するプラグイン

このプラグインは [ほげほげ](https://github.com/yazawakenichi/hoge) に含まれています

### Usage
``` md
#dbread(DATABASE, TABLE)
```

|引数|説明
|:---:|:---
| `DATABASE` | 接続先のデータベース名
| `TABLE` | 接続先のテーブル名

# 問題
- 関係がようわからん
    - dbread.inc.php
    - dbwrite.inc.php
    - youtube.php
    - ytwrite.php
- データベースの構造が変わったときの改変が面倒
- ポート番号とかIPアドレスとかユーザ名とかパスワードとかをファイル内に記述しなきゃいけない

ゼロから搭載しようとしたらファイルを色々書き換えたり読み込んだりする必要があって不便すぎるのでどうにかしたい

