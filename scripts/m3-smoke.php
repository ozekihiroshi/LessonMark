<?php
/**
 * Creates and verifies the LessonMark M3 teaching-document fixture.
 *
 * This script is for disposable development environments only.
 *
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

$configpath = getenv('MOODLE_CONFIG') ?: '/var/www/html/config.php';
if (!is_file($configpath)) {
    fwrite(STDERR, "Moodle config.php was not found.\n");
    exit(1);
}

require($configpath);
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');

\core\session\manager::set_user(get_admin());

$shortname = 'LESSONMARK-M3';
$course = $DB->get_record('course', ['shortname' => $shortname]);
if (!$course) {
    $course = create_course((object) [
        'fullname' => 'LessonMark M3 smoke test',
        'shortname' => $shortname,
        'category' => 1,
        'format' => 'topics',
        'numsections' => 1,
        'visible' => 1,
    ]);
}

$source = <<<'MARKDOWN'
# LessonMark M3

## Repeated heading

## Repeated heading

> [!NOTE]
> Background information.

> [!TIP]
> A practical suggestion.

> [!WARNING]
> A condition that can cause a problem.

```bash
echo "hello"
```

```json
{"ready": true}
```

```sql
SELECT 1;
```

```brainfuck
+++.
```

| Concept | Example |
| --- | --- |
| Equality | `x == 1` |

<script>window.lessonmarkUnsafe = true;</script>
MARKDOWN;

$name = 'M3 teaching document fixture';
$lessonmark = $DB->get_record('lessonmark', ['course' => $course->id, 'name' => $name]);
if (!$lessonmark) {
    $module = $DB->get_record('modules', ['name' => 'lessonmark'], '*', MUST_EXIST);
    course_create_sections_if_missing($course, 1);
    $moduleinfo = (object) [
        'course' => $course->id,
        'module' => $module->id,
        'modulename' => 'lessonmark',
        'section' => 1,
        'name' => $name,
        'intro' => 'M3 integration fixture',
        'introformat' => FORMAT_HTML,
        'markdownsource' => $source,
        'visible' => 1,
        'visibleoncoursepage' => 1,
        'cmidnumber' => '',
        'groupmode' => 0,
        'groupingid' => 0,
        'completion' => 0,
        'completionview' => 0,
        'completionexpected' => 0,
        'availabilityconditionsjson' => null,
    ];
    $moduleinfo = add_moduleinfo($moduleinfo, $course);
    $lessonmark = $DB->get_record('lessonmark', ['id' => $moduleinfo->instance], '*', MUST_EXIST);
} else {
    $lessonmark->markdownsource = $source;
    $lessonmark->timemodified = time();
    $DB->update_record('lessonmark', $lessonmark);
}

$cm = get_coursemodule_from_instance('lessonmark', $lessonmark->id, $course->id, false, MUST_EXIST);
$context = context_module::instance($cm->id);
$renderer = new \mod_lessonmark\local\moodle_markdown_renderer();
$document = $renderer->render($lessonmark->markdownsource, $context);
$html = $document->get_content_html();
$diagnostics = $document->get_diagnostics();

$checks = [
    'source_preserved' => $lessonmark->markdownsource === $source,
    'toc_rendered' => str_contains($html, 'mod_lessonmark-toc'),
    'duplicate_heading_id' => str_contains($html, 'lessonmark-repeated-heading-2'),
    'note_rendered' => str_contains($html, 'mod_lessonmark-callout--note'),
    'tip_rendered' => str_contains($html, 'mod_lessonmark-callout--tip'),
    'warning_rendered' => str_contains($html, 'mod_lessonmark-callout--warning'),
    'bash_language' => str_contains($html, 'language-bash'),
    'json_language' => str_contains($html, 'language-json'),
    'sql_language' => str_contains($html, 'language-sql'),
    'table_scroll_region' => str_contains($html, 'mod_lessonmark-table-scroll'),
    'script_not_executable' => !str_contains($html, '<script>'),
    'raw_html_visible_as_text' => str_contains($html, '&lt;script&gt;'),
    'unsupported_language_diagnostic' => in_array(
        ['type' => 'unsupportedlanguage', 'language' => 'brainfuck'],
        $diagnostics,
        true
    ),
];

if (in_array(false, $checks, true)) {
    fwrite(STDERR, json_encode([
        'checks' => $checks,
        'diagnostics' => $diagnostics,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    exit(1);
}

echo json_encode([
    'courseid' => (int) $course->id,
    'cmid' => (int) $cm->id,
    'instanceid' => (int) $lessonmark->id,
    'checks' => $checks,
    'toc' => $document->get_toc(),
    'diagnostics' => $diagnostics,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
