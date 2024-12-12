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

## dbwrite
YouTube のタイムスタンプと感想をデータベースに保存するプラグイン

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

### Usage
``` md
#dbread(DATABASE, TABLE)
```

|引数|説明
|:---:|:---
| `DATABASE` | 接続先のデータベース名
| `TABLE` | 接続先のテーブル名



