# LessonMark 技術判断と実装マイルストーン

## 1. 文書の位置づけ

この文書は`PRODUCT_REQUIREMENTS.md`を実装へ移すため、v0.1の主要な技術判断、境界、実装順序を固定する。

本書と製品要件書が矛盾する場合、以下の確定事項については本書を優先する。

- plugin typeとcomponent名
- 対象Moodle/PHP
- rendererとPreviewの構造
- raw HTML、callout、画像importの境界
- 実装マイルストーン

## 2. 確定した技術判断

### TD-001: Activity moduleとして実装する

- 製品上の位置づけはMoodle course resourceとする。
- Moodle内部のplugin typeは`mod`とする。
- Frankenstyle component名は`mod_lessonmark`とする。
- 配置先は`mod/lessonmark`とする。
- 学習者による提出や評定を中心機能としないが、コースへ追加される教材であるためActivity moduleの標準構造を利用する。

### TD-002: Moodle 5.2を初期対象にする

- v0.1の初期対象はMoodle 5.2とする。
- PHPはMoodle 5.2がサポートするPHP 8.3および8.4を対象とする。
- PHP 8.2はMoodle 5.2の最低要件を満たさないため対象外とする。
- Moodle 5.3は次期LTSだが、v0.1開始時点の対象には含めない。M7で互換性を再評価する。
- `version.php`の`requires`はMoodle 5.2のrelease番号に合わせる。

### TD-003: rendering pipelineを一本化する

Previewと学生表示は、同じapplication serviceを呼び出す。

```text
Markdown source
    ↓
source validation / raw HTML neutralization
    ↓
Moodle core Markdown adapter
    ↓
LessonMark post-processing
  - heading ID
  - TOC
  - callout
  - file URL resolution
    ↓
final sanitization
    ↓
rendered document
  - content HTML
  - TOC data
  - diagnostics
```

最小の境界は次の責務を持つ。

```php
interface markdown_renderer_interface {
    public function render(string $source, \context $context): rendered_document;
}
```

- `rendered_document`は本文HTML、目次データ、警告・エラーを保持する値オブジェクトとする。
- v0.1のadapterはMoodle coreの`FORMAT_MARKDOWN`処理を利用する。
- LessonMarkのdomain/service層からMoodle coreの変換関数を直接呼ばず、adapter内へ閉じ込める。
- 特定のCommonMark系ライブラリを直接の製品APIまたは保存形式にしない。
- renderer frameworkの構築は目的とせず、v0.1で実際に必要な1実装だけを作る。

### TD-004: raw HTMLを禁止する

- v0.1のMarkdown内ではraw HTMLを教材要素として解釈しない。
- `<div>`、`<script>`、`<iframe>`、`<style>`、イベント属性などを許可しない。
- HTMLらしい入力は実行・描画せず、通常の文字として表示する。
- fenced code blockおよびinline code内のHTML文字列はコードとして表示できる。
- source validationだけを安全性の境界にせず、生成HTMLへMoodleの安全化処理を必ず適用する。
- Previewも学生表示と同じ禁止・安全化処理を通す。

### TD-005: callout記法を固定する

正式対応する記法は次の3種類とする。

```markdown
> [!NOTE]
> 補足説明です。

> [!TIP]
> 実践上のヒントです。

> [!WARNING]
> 注意事項です。
```

- 未対応環境でもblockquoteとして読める構造を維持する。
- callout名は大文字・小文字を区別せず解釈する。
- v0.1ではタイトルの上書き、入れ子、折りたたみを扱わない。
- 種類は色だけでなく、ラベル、アイコン、意味のあるHTML属性で区別する。

### TD-006: Previewを保存から分離する

- Previewは下書き保存を行わない。
- editorから専用のAJAX対応endpointへMarkdown sourceを送信する。
- endpointはcourse module contextと編集capabilityを確認する。
- sesskeyを検証し、GETではなくPOSTで処理する。
- client側は入力後400 msを目安にdebounceする。
- 自動更新に加えて「Previewを更新」ボタンを設ける。
- 古いrequestの応答で新しいPreviewを上書きしない。
- source上限は512 KiBとし、server側でも検証する。
- endpointが返すのは共通rendererが生成した安全化済みHTML、目次データ、diagnosticsに限定する。

### TD-007: v0.1の画像はMoodle管理ファイルに限定する

- 画像はMoodle File APIを使用し、自前ディレクトリへ保存しない。
- 編集中はdraft file area、保存後はcourse module contextの`mod_lessonmark`教材用file areaを使用する。
- Markdown source内の正規参照は`@@PLUGINFILE@@/path/to/file.png`形式とする。
- 表示時にMoodleのFile APIを使って`pluginfile.php` URLへ解決する。
- `mod_lessonmark_pluginfile()`でcontext、login、course module visibility、capabilityを確認する。
- `.md`単体importに含まれる`images/example.png`などの相対参照は自動取得しない。
- 未解決の相対画像はPreview diagnosticsとして教師へ通知する。
- 相対画像とフォルダを含むZIP bundleはv0.2で扱う。

### TD-008: importは取り込み、exportは複製とする

- `.md` importはUTF-8本文を現在のeditorへ読み込む一度限りの操作とする。
- import元との同期関係は保存しない。
- import時に既存の未保存sourceがある場合は確認を求める。
- UTF-8 BOMは受け入れて除去する。不正なUTF-8は保存せずエラーを表示する。
- exportは現在保存されているsourceをUTF-8、拡張子`.md`で返す。
- source modeはv0.1では`Moodle managed`だけとする。

### TD-009: syntax highlightingは表示層の拡張とする

- Markdown rendererは言語指定を`language-*` classとして安全に保持する。
- highlightingは保存HTMLへ直接色付けする処理ではなく、表示層で適用する。
- highlighterはプラグインへ同梱し、外部CDNへ依存しない。
- ライブラリ、固定version、license、対象言語はM3開始時に監査し、`thirdpartylibs.xml`へ記録する。
- 初期対象言語はplain text、Bash、CSS、HTML/XML、JavaScript、JSON、PHP、Python、SQLとする。
- 未対応言語でもコード本文は失わず、plain code blockとして表示する。

## 3. データ境界

v0.1で最低限保持するinstance dataは次のとおりとする。

- `id`
- `course`
- `name`
- `intro`
- `introformat`
- `markdownsource`
- `displayoptions`
- `timecreated`
- `timemodified`

派生HTMLは正本としてDBへ保存しない。cacheが必要になった場合も、source、renderer version、設定から再生成可能な派生物として扱う。

関連画像はDB本文へ埋め込まず、File APIの教材用file areaに保存する。

## 4. capability境界

少なくとも次を定義する。

- `mod/lessonmark:addinstance`
- `mod/lessonmark:view`
- `mod/lessonmark:edit`

更新、Preview、import、export、ファイル操作ではcourse module contextのcapabilityをserver側で確認する。画面上でボタンを隠すだけでは認可としない。

## 5. 実装マイルストーン

各マイルストーンは動作する状態で完了し、対応する自動テストとともにcommitできる単位にする。

### M1: `mod_lessonmark` skeletonと学生表示

- Activity moduleの必須ファイル
- install/upgrade可能なDB schema
- capabilityと基本CRUD
- Markdown sourceの保存
- 共通renderer serviceの最小実装
- 学生向け`view.php`
- raw HTML禁止と最終安全化の基礎テスト

完了条件: 教師が教材を作成し、保存したMarkdownを学生画面で安全に表示できる。

### M2: Markdown editorとPreview

- Markdown専用textarea/editor UI
- デスクトップの左右分割表示
- 狭い画面のEdit/Previewタブ
- Preview endpoint
- debounce、手動更新、古い応答の破棄
- 未保存変更の離脱警告
- Previewと学生表示の一致テスト

完了条件: Moodle内だけで新規作成、Preview、保存、再編集ができる。

### M3: 教材記法と表示

- 正式対応記法
- heading IDと自動目次
- NOTE/TIP/WARNING
- fenced code block
- 同梱syntax highlighterとlicense記録
- responsive table、code、image
- 教材用typographyと印刷CSS

完了条件: v0.1対応記法のfixtureがPreviewと学生画面で期待どおり表示される。

### M4: 画像とFile API

- draft file areaを利用した画像追加
- 教材用file areaへの保存
- `@@PLUGINFILE@@`参照の解決
- `pluginfile` callbackとアクセス制御
- 代替テキストの確認
- 未解決相対画像のdiagnostics

完了条件: 画像を追加、保存、再編集でき、権限のある利用者だけが閲覧できる。

### M5: `.md` import/export

- UTF-8 validationとBOM処理
- import時の上書き防止
- `.md` export
- ファイル名とHTTP responseの安全化
- 改行コード差のテスト

完了条件: 外部Markdownを取り込んで編集でき、保存sourceを再利用可能な`.md`として取得できる。

### M6: backup/restoreとcourse duplicate

- `backup/moodle2`のactivity taskとsteps
- restore taskとsteps
- Markdown source、設定、関連ファイルの移行
- content linkとFile API参照の復元
- backup/restore自動テスト

完了条件: course backup/restoreおよびduplicate後も本文、目次、画像が機能する。

### M7: リリース品質

- PHPUnit、Behat、PHPDoc、JavaScript lint
- Moodle coding style検査
- security review
- keyboard操作とaccessibility確認
- Moodle Plugin CI
- privacy APIの該当性確認と実装
- Moodle 5.2 / PHP 8.3、8.4 matrix
- Moodle 5.3互換性の再評価
- README、対応記法、管理者向けinstall文書

完了条件: `PRODUCT_REQUIREMENTS.md`の受け入れ条件を満たし、公開候補packageを生成できる。

## 6. マイルストーン共通の完了基準

- 新規・変更機能に自動テストがある。
- Previewと学生表示の共通pipelineを迂回していない。
- 認可と入力制限をclient側だけに依存していない。
- sourceをHTMLへ不可逆変換して保存していない。
- 新しい外部依存を追加した場合、license、version、更新方法を記録している。
- Moodle標準APIで実現できる処理を独自保存・独自認証で置き換えていない。

## 7. 実装開始前に残る小さな確認事項

以下は製品境界を変えないため、該当マイルストーン内で決める。

- instance table名と`displayoptions`の具体的なschema
- 見出しIDの重複解消規則
- 目次を表示する最低見出し数
- 同梱syntax highlighterの製品と固定version
- 教材用file area名
- editor pane幅を利用者設定として保持するか

これらの決定によってv0.1の機能範囲を増やしてはならない。

## 8. 参照した公式仕様

- Moodle 5.2 release notes: https://moodledev.io/general/releases/5.2
- Activity modules: https://moodledev.io/docs/5.2/apis/plugintypes/mod
- File API internals: https://moodledev.io/docs/5.2/apis/subsystems/files/internals
- Backup API: https://moodledev.io/docs/5.2/apis/subsystems/backup
- Moodle core formatting implementation: https://github.com/moodle/moodle/blob/main/public/lib/classes/formatting.php
