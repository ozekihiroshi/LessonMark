# Presentation implementation and local evidence

Date: 2026-09-08. Working tree on codex/math-mermaid, not a released RC2.

## Implementation

- `view.php?present=1` inherits the same course login and view capability as
  ordinary display, including Moodle availability checks.
- Embedded layout with activity header disabled removes Moodle navigation and
  introduction. Return link is always rendered server-side.
- `presentation_source` recognises standalone column-zero markers outside
  backtick/tilde fences. Normal rendering removes structural markers; saved
  Markdown is unchanged. Each slide uses the existing safe renderer and File API.
- Slide-local IDs are prefixed to avoid duplicate heading IDs. Cross-slide
  anchor navigation is not implemented; use previous/next controls.
- Progressive enhancement displays one section at a time with page count,
  keyboard navigation (excluding controls), fullscreen request and scrollable
  long content. Without JavaScript all sections remain visible. Print CSS
  exposes all sections and hides controls.
- No added database fields, remote assets, build dependency or new capability.

## Local checks

- PHP syntax, JavaScript syntax and git diff whitespace checks passed.
- Ten integration assertions passed: boundaries, empty segments, ordinary rules,
  literal markers in fenced/indented/quoted code, normal marker suppression,
  code example retention and inert raw HTML.
- Added PHPUnit boundary/renderer tests and a Behat navigation scenario. Full
  PHPUnit/Behat/Plugin CI still needs to run; do not equate syntax checks with CI.
- Existing test-only activity 37 is now `Presentation acceptance`, a four-page
  fixture. It replaces the previous malformed-input/duplication fixture, not
  user teaching material. The earlier claim that its valid source had already
  been saved was incorrect: reopening showed the malformed source. This run
  explicitly verified the presentation fixture's saved title and content.
- Normal view showed all four headings with no structural markers.
- Presentation showed 1/4, one visible region, no Moodle navigation/description.
- Next rendered math on page 2; ArrowRight rendered Japanese Mermaid on page 3.
- End selected 4/4, disabled Next, and long content had scrollHeight 1468 versus
  clientHeight 672 at 1280x800. Home returned to 1/4.
- Narrow default viewport displayed wrapping controls and a readable diagram.
- Return link returned to normal Moodle navigation. Viewport override reset.

## Pending before RC2

- Complete CI, unauthenticated/student/restricted-activity tests, no-JS fallback,
  browser print, and longer diagram/table/image fixtures.
- Fullscreen success/exit/denial: request attempted but success was not observable
  in the in-app browser. Do not record this as a passed fullscreen test.
- Source transfer, offline cold-load, and prior release-gate checks remain open.
- Working-tree files were copied into UI test environment; version metadata and
  existing ZIPs remain RC1. Build a new uniquely versioned RC2 only after gates.

Try http://localhost:8085/mod/lessonmark/view.php?id=37&present=1 locally.

## Continued CI attempt

- Added `scripts/test-presentation.cjs` and wired it into local/hosted CI. Its
  controller assertions passed: navigation, bounds, input arrow-key preservation,
  and continued navigation after fullscreen denial (mocked DOM, not fullscreen UI).
- Unauthenticated HTTP request to presentation returned 303 to login, without
  lesson content.
- Moodle CodeSniffer initially found documentation/format issues; corrected
  those and ran the whole plugin with standard `moodle`: exit 0, no diagnostics.
- npm audit of LessonMark reported zero vulnerabilities; generated vendor assets
  remained unchanged. The CI tool's own helper npm dependency reported one
  moderate advisory; no forced upgrade was performed.
- Found cross-slide self-check label/radio collisions. Prefixed `for`/`name`
  alongside IDs. Presentation no longer restores/persists browser-local answers;
  hides clear/persistence UI. This final refinement is not yet browser-verified.
- PHP 8.3 local CI reached step 7/9, Initialize test suite. Full PHPUnit, PHPDoc,
  Grunt and Behat results are NOT available.
- WSL then stopped accepting new commands: Wsl/Service/0x8007274c. A read-only
  `/bin/true` retry also timed out. No WSL shutdown/restart was attempted because
  unrelated Docker environments are running.
- Last known CI container: `4348ec653dcd` / `admiring_bohr`; temporary DB:
  `lessonmark-ci-db-682763`; temporary network follows the same numeric suffix.
  Runner's normal cleanup is scoped to its temporary DB/network. Its completion
  and cleanup could not be verified. Do not start another run before checking
  these on recovery. Never delete Moodle UI-test volumes to recover this run.
- CI scratch root: `/tmp/lessonmark-plugin-ci.3lee6C`. The attempt to sync latest
  PHP fixes to its copied plugin failed with the WSL timeout; refresh that copy
  before rerunning checks, or start a fresh run after safe cleanup.
- No RC2 version bump, commit, push or ZIP rebuild yet. All edits remain local.

### First CI result and retry

The user supplied the completed log: initialization finished at about 20 minutes,
PHP lint passed 42 files, then CodeSniffer failed on the stale CI plugin copy.
PHPDoc/Grunt/PHPUnit did not run because the runner stops on failure. In addition
to already-fixed PHP formatting, the configured CI detected language-key ordering
warnings. Both language files have now been ordered. The earlier direct generic
CodeSniffer pass was not equivalent to this configured CI check.

WSL new-command access subsequently recovered. The old runner and temporary
containers were no longer present. A fresh PHP 8.3 local CI run was launched from
`--cd /tmp` using current source; result pending. The controller unit test and
git diff whitespace checks passed again before retry.

### Second run: intermediate checks

The second runner is `nifty_allen` (`387e602e67ef`), with scratch directory
`/tmp/lessonmark-plugin-ci.u4rjfp`. While its test-suite initialization continued,
the CI tool's CodeSniffer command was run against the current workspace plugin:
42 files passed with `--max-warnings 0`, exit 0. PHPDoc also passed with the
test Moodle path explicitly supplied, exit 0. These are individual checks, not
a completed pipeline or PHPUnit result.

Grunt completed AMD lint/build and Gherkin lint, then rejected `clamp()` in the
presentation font size and a missing blank line before the print media rule.
Replaced the fluid size with equivalent viewport-width media breakpoints and
fixed the blank line. Copied this CSS into the running CI plugin copy. The
targeted CI `stylelint` retry passed (one file, no errors, exit 0). Controller
tests and `git diff --check` passed again. The main runner is still initializing;
PHPUnit and a complete end-to-end pipeline result remain unconfirmed.

### Second run: test results

The main PHP 8.3 runner subsequently passed PHP lint (42 files), CodeSniffer,
PHPDoc, plugin validation, savepoint check and all configured Grunt tasks,
including the corrected CSS. PHPUnit completed successfully:
`OK (36 tests, 142 assertions)`, reported test time `01:10.345`, memory 95 MB.
Environment: Moodle 5.2.2+ (20260903), PHP 8.3.33, MariaDB 11.8.8.
The tool response had a much longer delay than PHPUnit's reported duration;
do not treat the response delay as the test runtime. Final runner cleanup/exit
is being checked. PHP 8.4, browser Behat and the remaining manual release gates
are not covered by this PHP 8.3 result. No RC2 artifact has been generated.

### PHP 8.4 and browser follow-up

PHP 8.3 runner finished with exit 0. Started the existing local runner with
`LESSONMARK_CI_PHP_VERSION=8.4`; container `sad_keller`, scratch
`/tmp/lessonmark-plugin-ci.IpnywN`. Controller tests, asset rebuild/reproducibility
and the plugin npm audit passed; Moodle initialization is still in progress.

Copied current plugin files to the existing UI-test environment and completed
cache purge (exit 0). Browser navigation did not complete; a separate test tab
reported `ERR_CONNECTION_RESET`. Resetting the browser tool connection did not
restore the page. One WSL command failed with `Wsl/Service/0x8007274c`; a later
HTTP check inside the Moodle container returned 303. This does not establish
the precise connection failure cause. No WSL restart or unrelated-container
shutdown was performed. Current browser checks are blocked, not passed.

Hosted Actions already defines Chrome Behat. Commit/push approval has been
requested to run the uncommitted candidate there without publishing a release.
Local PHP 8.4 continues; its final result and Behat results remain pending.

### PHP 8.4 completion

The local PHP 8.4 runner finished with exit 0: PHP lint, CodeSniffer, PHPDoc,
validation, savepoints and Grunt passed. PHPUnit on PHP 8.4.24 / Moodle 5.2.2+
(20260903) / MariaDB 11.8.8 reported `OK (36 tests, 142 assertions)` in
`01:06.091`, memory 95 MB. This does not include Chrome Behat.
The user authorized committing and pushing the candidate to `codex/math-mermaid`
for hosted PHP 8.4 and Chrome Behat verification, without publishing a release.
