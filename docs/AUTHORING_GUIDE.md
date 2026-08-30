# LessonMark authoring guide

This guide defines the Markdown dialect supported by LessonMark 0.5. The
Markdown source remains the editable source of truth; rendered HTML is derived
for preview and student display.

## Basic structure

Use ATX headings, paragraphs, emphasis, links, lists, blockquotes, inline code,
fenced code blocks, and tables. Keep one H1 for the document title where
practical and do not skip heading levels merely to obtain a visual size.

The source editor visually wraps long prose lines without adding line breaks to
the saved Markdown. Code blocks continue to preserve their source whitespace.

```markdown
# Python conditions

## Learning objective

Explain how `if` selects a branch.
```

LessonMark assigns stable IDs to headings. Duplicate headings receive `-2`,
`-3`, and so on. A table of contents is displayed when the document has two or
more headings. Changing a heading changes its generated link.

## Images

Upload teaching images in the **Teaching images** file manager, then reference
the exact uploaded path with LessonMark's canonical placeholder:

```markdown
![Network topology](@@PLUGINFILE@@/network.png)
![Request flow](@@PLUGINFILE@@/diagrams/request-flow.png)
```

The text inside `[]` is the alternative text. Describe the instructional
meaning of the image; Preview reports an empty or missing alternative text.
Decorative-image semantics are not inferred in the current dialect.

Moodle stores the images. LessonMark keeps `@@PLUGINFILE@@` in the Markdown
source and resolves it to a temporary draft URL in Preview or an
access-controlled module URL after save. Up to 50 Moodle `web_image` files are
accepted per activity, subfolders are supported, and site upload limits apply.

A relative reference such as `![Chart](images/chart.png)` does not upload or
search for a local file. Preview reports it as unresolved. Upload the image and
change the source to `@@PLUGINFILE@@/images/chart.png`. Markdown-and-images ZIP
bundle import is not part of LessonMark 0.5.

## Import and export

Select **Import .md** to place an external Markdown file in the editor. The
file must:

- have a name ending in `.md`;
- contain valid UTF-8 text;
- remain within 512 KiB after BOM and line-ending normalisation.

A leading UTF-8 BOM is removed and Windows or classic Mac line endings become
LF. If the editor is not empty, LessonMark asks before replacing its content.
The imported text remains unsaved until the activity form is saved. Import
does not upload adjacent images or remain synchronized with the original file.

For an existing activity, **Export saved .md** downloads the Markdown source
currently stored in Moodle. Unsaved editor changes and image files are not
included. Save first when the download must contain the latest edit.

**Download saved PDF** produces a portable reading copy from the same saved
source. Moodle-managed teaching images are embedded, ANSWER disclosures are
expanded, and RESPONSE or CHOICE controls become blank printable working
areas. Browser-local answers and unsaved editor changes are not included.
Remote images are not fetched into the PDF.

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

## Self-check blocks

Self-check blocks keep a short practice cycle inside one LessonMark page. They
do not create Moodle quiz attempts, grades, submissions, or completion data.

Use `RESPONSE` for a free-text working answer:

```markdown
> [!RESPONSE]
> Explain your answer before opening the official answer.
```

Use `CHOICE` with a Markdown list for a single-choice working answer:

```markdown
> [!CHOICE]
> Select one answer.
>
> - A. First option
> - B. Second option
```

Place the official answer and explanation immediately after the response. The
content is rendered as a closed disclosure:

```markdown
> [!ANSWER]
> **Official answer: B**
>
> Explain why B follows from the source material.
```

Working answers are stored only in the current browser, scoped by Moodle user
and LessonMark activity. They are not available to teachers and do not move to
another browser or device. Learners can clear each saved answer. Use Moodle
Quiz or Assignment when grading, submission, attempt history, or teacher review
is required.

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

Preview and import do not save the Markdown source or publish uploaded draft
files. Save the activity to publish the current source and images. Preview and
student display call the same server-side rendering pipeline; syntax colour is
then applied in the browser without changing the stored source.
