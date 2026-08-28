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
 * Moodle File API integration for LessonMark teaching images.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/**
 * Owns the fixed file area and draft/permanent URL lifecycle.
 */
final class content_files {
    /** Permanent file area name. */
    public const FILEAREA = 'content';

    /** Permanent item ID. */
    public const ITEMID = 0;

    /** Module form field name. */
    public const FORM_FIELD = 'lessonmarkfiles';

    /** Maximum number of teaching images in v0.1. */
    public const MAX_FILES = 50;

    /**
     * Returns the common filemanager and draft-save options.
     *
     * @return array File API options.
     */
    public static function options(): array {
        global $CFG;
        require_once($CFG->dirroot . '/repository/lib.php');

        return [
            'subdirs' => true,
            'maxfiles' => self::MAX_FILES,
            'maxbytes' => 0,
            'accepted_types' => ['web_image'],
            'return_types' => FILE_INTERNAL,
        ];
    }

    /**
     * Copies permanent files into an editing draft area.
     *
     * @param int $draftitemid User draft item ID.
     * @param \context_module $context LessonMark module context.
     */
    public static function prepare_draft_area(int &$draftitemid, \context_module $context): void {
        file_prepare_draft_area(
            $draftitemid,
            $context->id,
            'mod_lessonmark',
            self::FILEAREA,
            self::ITEMID,
            self::options()
        );
    }

    /**
     * Replaces permanent files with the submitted draft area.
     *
     * @param int $draftitemid User draft item ID.
     * @param \context_module $context LessonMark module context.
     */
    public static function save_draft_area(int $draftitemid, \context_module $context): void {
        file_save_draft_area_files(
            $draftitemid,
            $context->id,
            'mod_lessonmark',
            self::FILEAREA,
            self::ITEMID,
            self::options()
        );
    }

    /**
     * Rewrites canonical placeholders for a permanent or draft file area.
     *
     * @param string $html Safe rendered HTML.
     * @param \context $context Current rendering context.
     * @param int|null $draftitemid Draft item ID for unsaved Preview.
     * @return string HTML containing requestable file URLs.
     */
    public static function rewrite_urls(string $html, \context $context, ?int $draftitemid = null): string {
        global $USER;

        if ($draftitemid !== null && $draftitemid > 0) {
            $usercontext = \context_user::instance($USER->id);
            return file_rewrite_pluginfile_urls(
                $html,
                'draftfile.php',
                $usercontext->id,
                'user',
                'draft',
                $draftitemid
            );
        }
        if ($context instanceof \context_module) {
            return file_rewrite_pluginfile_urls(
                $html,
                'pluginfile.php',
                $context->id,
                'mod_lessonmark',
                self::FILEAREA,
                self::ITEMID
            );
        }
        return $html;
    }

    /**
     * Finds one permanent content file from pluginfile path arguments.
     *
     * @param \context_module $context LessonMark module context.
     * @param array $args Relative path arguments after item ID.
     * @return \stored_file|null Stored file, or null for an invalid path.
     */
    public static function get_file(\context_module $context, array $args): ?\stored_file {
        if ($args === [] || (int) array_shift($args) !== self::ITEMID || $args === []) {
            return null;
        }
        $relativepath = implode('/', $args);
        $fullpath = "/{$context->id}/mod_lessonmark/" . self::FILEAREA . '/' . self::ITEMID . "/{$relativepath}";
        $file = get_file_storage()->get_file_by_hash(sha1($fullpath));
        if (!$file || $file->is_directory()) {
            return null;
        }
        return $file;
    }
}
