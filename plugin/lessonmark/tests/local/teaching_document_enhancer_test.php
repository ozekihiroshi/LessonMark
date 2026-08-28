<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Tests for teaching-document HTML enhancement.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Tests safe teaching-document structure.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(teaching_document_enhancer::class)]
final class teaching_document_enhancer_test extends \advanced_testcase {
    /**
     * Tests heading IDs, duplicate handling, Unicode slugs, and TOC output.
     */
    public function test_enhances_headings_and_toc(): void {
        $enhancer = new teaching_document_enhancer();
        $document = $enhancer->enhance('<h1>Overview</h1><h2>日本語 見出し</h2><h2>日本語 見出し</h2>');

        $this->assertSame([
            ['id' => 'lessonmark-overview', 'level' => 1, 'text' => 'Overview'],
            ['id' => 'lessonmark-日本語-見出し', 'level' => 2, 'text' => '日本語 見出し'],
            ['id' => 'lessonmark-日本語-見出し-2', 'level' => 2, 'text' => '日本語 見出し'],
        ], $document->get_toc());
        $html = $document->get_content_html();
        $this->assertStringContainsString('class="mod_lessonmark-toc"', $html);
        $this->assertStringContainsString(
            'href="#lessonmark-' . rawurlencode('日本語-見出し-2') . '"',
            $html
        );
        $this->assertStringContainsString('id="lessonmark-overview"', $html);
    }

    /**
     * Tests callouts, code languages, diagnostics, and responsive tables.
     */
    public function test_enhances_teaching_elements(): void {
        $enhancer = new teaching_document_enhancer();
        $document = $enhancer->enhance(
            '<blockquote><p>[!tip] Try <strong>this</strong>.</p></blockquote>'
            . '<pre><code class="py">print(&quot;Hi&quot;)</code></pre>'
            . '<pre><code class="brainfuck">+++</code></pre>'
            . '<table><tbody><tr><td>Cell</td></tr></tbody></table>'
        );
        $html = $document->get_content_html();

        $this->assertStringContainsString('mod_lessonmark-callout--tip', $html);
        $this->assertStringContainsString('role="note"', $html);
        $this->assertStringNotContainsString('[!tip]', $html);
        $this->assertStringContainsString('<strong>this</strong>', $html);
        $this->assertStringContainsString('class="mod_lessonmark-code language-python"', $html);
        $this->assertStringContainsString('class="language-python"', $html);
        $this->assertStringContainsString('class="mod_lessonmark-table-scroll"', $html);
        $this->assertStringContainsString('tabindex="0"', $html);
        $this->assertSame([
            ['type' => 'unsupportedlanguage', 'language' => 'brainfuck'],
        ], $document->get_diagnostics());
    }

    /**
     * Tests that Moodle-merged callout paragraphs become separate callouts.
     */
    public function test_splits_consecutive_callouts(): void {
        $enhancer = new teaching_document_enhancer();
        $document = $enhancer->enhance(
            '<blockquote><p>[!NOTE] First</p><p>[!TIP] Second</p><p>[!WARNING] Third</p></blockquote>'
        );
        $html = $document->get_content_html();
        $this->assertSame(3, substr_count($html, 'class="mod_lessonmark-callout '));
        $this->assertStringContainsString('mod_lessonmark-callout--note', $html);
        $this->assertStringContainsString('mod_lessonmark-callout--tip', $html);
        $this->assertStringContainsString('mod_lessonmark-callout--warning', $html);
    }
}
