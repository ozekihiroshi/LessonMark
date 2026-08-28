# LessonMark 製品要件書

## 1. 文書情報

- 製品名（仮称）: LessonMark
- 対象リリース: v0.1
- 製品種別: Moodle Resource plugin
- 文書の目的: 最初の実用版で実現する範囲と、実現しない範囲を固定する

## 2. 製品概要

LessonMarkは、Moodle上でMarkdown教材を新規作成・編集・プレビュー・公開するためのResourceプラグインである。

保存する正本はHTMLではなくMarkdown sourceとする。教師はMoodleの画面だけで教材を最初から作成でき、既存の `.md` ファイルを取り込んで編集することもできる。学生には、同じsourceから生成した読みやすい教材ページを表示する。

## 3. 解決する問題

既存のMoodle Markdown機能は、テキスト入力形式、エディタ、表示用filterなどの個別機能が中心であり、Markdownを教材の正本として作成・管理・公開する一連の体験を提供していない。

`.md` のアップロードと再アップロードだけでは、軽微な修正にも外部エディタが必要となる。一方、HTMLのWYSIWYG編集ではMarkdown sourceを維持できず、Git、diff、AI、VS Codeなどを利用する教材制作との相性が悪い。

LessonMarkは、Moodle内で完結するMarkdown専用編集環境と教材向け表示を一体で提供する。

## 4. 対象ユーザー

### 主なユーザー

- Moodle上で技術教材や文章教材を作成する教師・教材制作者
- Markdownには慣れているが、MoodleのHTML編集を負担に感じる利用者
- AIが生成したMarkdownを確認・修正して教材に利用したい利用者

### 副次的なユーザー

- VS CodeやGitで作成した教材をMoodleへ持ち込みたい開発者・教材チーム
- Moodle外でも再利用できる原稿形式を必要とする教育プロジェクト

## 5. 製品原則

1. **Moodleだけで完結できる**  
   外部エディタやコマンドを必須にせず、新規作成から公開、再編集までブラウザ上で行える。

2. **Markdown sourceを正本として保持する**  
   保存・再編集によってHTMLへ置換したり、原稿のMarkdown記法を失ったりしない。

3. **編集結果を公開前に確認できる**  
   Previewは学生向け表示と同じレンダリング経路を使用する。

4. **教材として読みやすく表示する**  
   単なるMarkdown-to-HTML変換ではなく、目次、コード、callout、レスポンシブ表示を統合する。

5. **Moodleの仕組みを活用する**  
   File API、Forms、context、capability、backup/restoreなど、利用可能なMoodle標準APIに従う。

6. **レンダラーを交換可能にする**  
   特定のCommonMark系実装を製品の中核仕様にせず、Markdown source、教材拡張、表示を分離する。Markdownパーサの完全自作は目的としない。

## 6. v0.1の標準利用フロー

### 新規作成

1. 教師が「活動またはリソースを追加する」からLessonMarkを選ぶ。
2. タイトルとMarkdown本文を入力する。
3. 左側のMarkdown editorで編集し、右側のPreviewで学生向け表示を確認する。
4. 保存してコース上に公開する。
5. 後日、保存済みMarkdown sourceを再び開いて編集する。

### `.md` のimport

1. 教師がUTF-8の `.md` ファイルを選択する。
2. 内容をeditorへ取り込む。
3. 教師がPreviewを確認し、必要に応じて修正する。
4. Moodle管理のMarkdown sourceとして保存する。

v0.1のimportは一度限りの取り込みであり、元ファイルとの継続同期ではない。

### export

教師は保存中のMarkdown sourceをUTF-8の `.md` ファイルとして取得できる。

## 7. 機能要件

### FR-1 Resourceとしての作成・公開

- LessonMarkをMoodleコースへResourceとして追加できる。
- タイトル、説明、表示設定、Markdown sourceを保存できる。
- Moodle標準の表示・非表示、利用制限、完了条件と整合する。

### FR-2 Markdown専用editor

- 空のMarkdown教材を新規作成できる。
- 保存済みMarkdown sourceをそのまま再編集できる。
- HTML WYSIWYGへの自動変換を行わない。
- タブ挿入や主要なMarkdown記法の入力を妨げない。
- 未保存の変更がある状態で画面を離れる場合は警告する。

### FR-3 Preview

- デスクトップではMarkdown sourceとPreviewを左右に表示できる。
- 狭い画面ではEditとPreviewをタブで切り替えられる。
- Previewは手動更新または短い遅延を置いた更新とし、入力操作を妨げない。
- Previewと学生向け表示は同じレンダリング処理を使用する。
- Preview処理は下書きを保存しなくても利用できる。

### FR-4 import/export

- UTF-8の `.md` ファイルをeditorへimportできる。
- import前に未保存の本文がある場合は、意図しない上書きを防止する。
- 保存中のsourceを `.md` としてexportできる。
- 改行コードの違いによって内容を破壊しない。

### FR-5 v0.1対応記法

- 見出し
- 段落と改行
- 太字、斜体、取り消し線
- 箇条書き、番号付きリスト、ネストしたリスト
- リンク
- 画像
- blockquote
- inline code
- fenced code blockと言語指定
- 表
- 水平線
- NOTE、TIP、WARNINGのcallout

対応する記法と解釈は利用者向け仕様として文書化する。特定パーサとの完全互換は保証しない。

### FR-6 教材向け表示

- 見出しから自動目次を生成する。
- 見出しに安定したページ内リンクを付与する。
- fenced code blockをsyntax highlightingして表示する。
- 長いコード、表、画像がページ幅を破壊しない。
- NOTE、TIP、WARNINGを視覚的に区別する。
- Moodle themeの文字サイズ・色・幅と大きく衝突しない。
- 印刷時にも本文の順序と可読性を維持する。

### FR-7 画像とファイル

- Moodle File APIを利用して教材用画像を保存する。
- editorから画像を追加できる。
- Markdown内の画像参照を保存後も解決できる。
- 画像の代替テキストを保持し、未指定時には教師へ注意を示す。
- v0.1で正式対応する相対パス規則を実装前に別途定義する。

### FR-8 Moodle統合

- 作成、閲覧、更新、削除にcapabilityを設定する。
- course module contextに従ってファイルアクセスを制御する。
- Moodleのbackup/restoreにMarkdown source、設定、関連ファイルを含める。
- コース複製後も画像と内部参照が機能する。

## 8. 非機能要件

### セキュリティ

- 表示HTMLをMoodleのセキュリティモデルに従って無害化する。
- Markdown内の任意のscript実行を許可しない。
- 危険なURL scheme、イベント属性、埋め込みHTMLを許可しない。
- Previewにも公開表示と同じ安全化を適用する。
- 閲覧権限のない利用者が教材ファイルへ直接アクセスできないようにする。

### アクセシビリティ

- editorとPreviewをキーボードで操作できる。
- 見出し階層、表、calloutに意味のあるHTML構造を使用する。
- 色だけで情報の種類を区別しない。
- Moodleが対象とするアクセシビリティ水準を損なわない。

### 性能

- 通常規模の単一教材で、Preview更新が継続的な入力を妨げない。
- syntax highlightingや目次生成は必要なページでのみ読み込む。
- Preview要求にはサイズ制限と適切な頻度制御を設ける。

### 保守性

- source保存、Markdown解析、教材拡張、HTML安全化、画面表示を分離する。
- Moodle core APIで代替できる機能を重複実装しない。
- 対応MoodleバージョンとPHPバージョンをリリース前に明記する。

## 9. v0.1の非対象

- GitHub、GitLabなどとの同期
- 複数Markdownファイルによる章構成
- 前へ／次へナビゲーション
- ZIP教材bundleのimport
- AIによる教材生成・書き換え
- 共同リアルタイム編集
- バージョン履歴とdiff画面
- Mermaid
- 数式
- footnote
- front matterによる教材メタデータ
- 任意のHTML、JavaScript、iframeの埋め込み
- CommonMark、GitHub Flavored Markdownなどとの完全互換保証

これらはv0.1のデータ構造を壊さず追加できるよう考慮するが、初回リリースの完成条件には含めない。

## 10. 受け入れ条件

v0.1は、少なくとも次をすべて満たした時点で完成とする。

1. 教師が空のLessonMark Resourceを作成し、Moodle内だけでMarkdown教材を完成・公開できる。
2. 編集画面でMarkdown sourceと学生向けPreviewを確認できる。
3. 保存、再読込、再編集を行ってもMarkdown sourceがHTMLへ置換されない。
4. `.md` をimportして編集でき、保存内容を `.md` としてexportできる。
5. 対応記法、コードブロック、callout、表、目次、画像が学生画面で正しく表示される。
6. Previewと学生画面で、安全化後の表示結果が一致する。
7. 権限のない利用者は教材と関連ファイルを閲覧・編集できない。
8. backup/restoreおよびコース複製後も本文と画像が維持される。
9. デスクトップと狭い画面の両方で編集・閲覧できる。
10. Moodleのコーディング規約、プライバシー要件、Plugin directory公開要件についてリリース前検査を通過する。

## 11. v0.1で実装前に決定する事項

- 対応するMoodle/PHPの最小・最大バージョン
- 採用するMoodle plugin component名
- 使用する解析実装とRenderer interface
- calloutの正式な記法
- 画像挿入UIと相対パスの規則
- HTML混在を全面禁止するか、安全なsubsetのみ許可するか
- Previewを自動更新にするか、手動更新も併用するか
- 自動目次の表示条件と見出しID生成規則
- syntax highlighterの実装と対応言語
- Markdown sourceの最大サイズ

## 12. 将来の発展

- v0.2: 画像・添付ファイルを含む教材bundle
- v0.3: 複数Markdown、章構成、前後ナビゲーション
- v0.4: course packageとfront matter
- v0.5: Git repository同期、差分確認、更新方針
- v0.6: AI-assisted authori