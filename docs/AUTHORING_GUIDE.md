# LessonMark authoring guide

This guide defines the Markdown dialect supported by LessonMark 0.3. The
Markdown source remains the editable source of truth; rendered HTML is derived
for preview and student display.

## Basic structure

Use ATX headings, paragraphs, emphasis, links, lists, blockquotes, inline code,
fenced code blocks, and tables. Keep one H1 for the document title where
practical and do not skip heading levels merely to obtain a visual size.

```markdown
# Python conditions

## Learning objective

Explain how `if` selects a branch.
```

LessonMark assigns stable IDs to headings. Duplicate headings receive `-2`,
`-3`, and so on. A table of contents is displayed when the document has two or
more headings. Changing a heading changes its generated link.

## Callouts

Use the portable blockquote form below. Names are case-insensitive.

```markdown
> [!NOTE]
> Background information.

> [!TIP]
> A practical suggestion.

> [!WARNING]
> A condition that can cause a problem.
```

Custom callout titles, nested callouts, and collapsible callouts are not part of
the current dialect.

## Code

Add a language after the opening fence.

````markdown
```python
if score >= 60:
    print("pass")
```
````

The supported language identifiers are:

| Language | Identifiers |
| --- | --- |
| Plain text | `text`, `plain`, `plaintext` |
| Bash | `bash`, `sh`, `shell` |
| CSS | `css` |
| HTML/XML | `html`, `xml` |
| JavaScript | `javascript`, `js` |
| JSON | `json` |
| PHP | `php` |
| Python | `python`, `py` |
| SQL | `sql` |

An unknown identifier does not load code or a library dynamically. LessonMark
keeps the code readable as plain text and reports a preview diagnostic.

## Tables

```markdown
| Concept | Example |
| --- | --- |
| Equality | `x == 1` |
| Ordering | `x < 10` |
```

Wide tables receive a keyboard-focusable horizontal scroll region. Avoid using
a table for page layout and include a meaningful header row.

## Safety and current boundaries

Raw HTML is not an authoring feature. HTML-like input is displayed as text;
scripts, iframes, inline styles, and event attributes are not accepted. Use a
fenced code block when HTML is the subject of the lesson.

Images managed through Moodle's File API are planned for M4. The current M3
renderer makes Markdown images responsive, but importing a relative image such
as `images/example.png` does not upload that file.

Preview does not save the draft. Save the activity to publish the current
source. Preview and student display call the same server-side rendering
pipeline; syntax colour is then applied in the browser without changing the
stored source.
