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
 * Tests for LessonMark content files.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Tests the Moodle File API draft and permanent lifecycle.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(content_files::class)]
final class content_files_test extends \advanced_testcase {
    /**
     * Saves, prepares, resolves, and locates one teaching image.
     */
    public function test_content_file_lifecycle(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $lessonmark = $this->getDataGenerator()->create_module('lessonmark', [
            'course' => $course->id,
            'name' => 'Image lifecycle',
            'markdownsource' => '![Diagram](@@PLUGINFILE@@/images/diagram.png)',
        ]);
        $cm = get_coursemodule_from_instance('lessonmark', $lessonmark->id, $course->id, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        $usercontext = \context_user::instance($USER->id);
        $draftitemid = file_get_unused_draft_itemid();
        get_file_storage()->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => $draftitemid,
            'filepath' => '/images/',
            'filename' => 'diagram.png',
            'mimetype' => 'image/png',
            'userid' => $USER->id,
        ], "\x89PNG\r\n\x1a\n");

        content_files::save_draft_area($draftitemid, $context);
        $stored = get_file_storage()->get_file(
            $context->id,
            'mod_lessonmark',
            content_files::FILEAREA,
            content_files::ITEMID,
            '/images/',
            'diagram.png'
        );
        $this->assertInstanceOf(\stored_file::class, $stored);
        $this->assertSame($stored->get_id(), content_files::get_file(
            $context,
            [content_files::ITEMID, 'images', 'diagram.png']
        )?->get_id());
        $this->assertNull(content_files::get_file($context, [99, 'images', 'diagram.png']));

        $permanent = content_files::rewrite_urls(
            '<img src="@@PLUGINFILE@@/images/diagram.png" alt="Diagram">',
            $context
        );
        $this->assertStringContainsString(
            "/pluginfile.php/{$context->id}/mod_lessonmark/content/0/images/diagram.png",
            $permanent
        );

        $rendered = (new moodle_markdown_renderer())->render(
            '![Diagram](@@PLUGINFILE@@/images/diagram.png)',
            $context
        );
        $this->assertStringContainsString('/pluginfile.php/', $rendered->get_content_html());
        $editdraftitemid = 0;
        content_files::prepare_draft_area($editdraftitemid, $context);
        $this->assertTrue(get_file_storage()->file_exists(
            $usercontext->id,
            'user',
            'draft',
            $editdraftitemid,
            '/images/',
            'diagram.png'
        ));
        $draft = content_files::rewrite_urls(
            '<img src="@@PLUGINFILE@@/images/diagram.png" alt="Diagram">',
            $context,
            $editdraftitemid
        );
        $this->assertStringContainsString(
            "/draftfile.php/{$usercontext->id}/user/draft/{$editdraftitemid}/images/diagram.png",
            $draft
        );
        $draftdocument = (new moodle_markdown_renderer(draftitemid: $editdraftitemid))->render(
            '![Diagram](@@PLUGINFILE@@/images/diagram.png)',
            $context
        );
        $this->assertStringContainsString('/draftfile.php/', $draftdocument->get_content_html());
    }
}
