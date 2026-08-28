<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library callbacks for LessonMark.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Reports the Moodle features supported by LessonMark.
 *
 * @param string $feature Feature constant.
 * @return bool|null
 */
function lessonmark_supports($feature) {
    return match ($feature) {
        FEATURE_MOD_ARCHETYPE => MOD_ARCHETYPE_RESOURCE,
        FEATURE_MOD_INTRO => true,
        FEATURE_SHOW_DESCRIPTION => true,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        FEATURE_BACKUP_MOODLE2 => false,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_CONTENT,
        default => null,
    };
}

/**
 * Creates a LessonMark instance.
 *
 * @param stdClass $lessonmark Submitted module data.
 * @param mod_lessonmark_mod_form|null $mform Module form.
 * @return int New instance id.
 */
function lessonmark_add_instance(stdClass $lessonmark, $mform = null): int {
    global $DB;

    $now = time();
    $lessonmark->timecreated = $now;
    $lessonmark->timemodified = $now;
    $lessonmark->displayoptions = $lessonmark->displayoptions ?? null;
    return $DB->insert_record('lessonmark', $lessonmark);
}

/**
 * Updates a LessonMark instance.
 *
 * @param stdClass $lessonmark Submitted module data.
 * @param mod_lessonmark_mod_form|null $mform Module form.
 * @return bool
 */
function lessonmark_update_instance(stdClass $lessonmark, $mform = null): bool {
    global $DB;

    $lessonmark->id = $lessonmark->instance;
    $lessonmark->timemodified = time();
    $lessonmark->displayoptions = $lessonmark->displayoptions ?? null;
    return $DB->update_record('lessonmark', $lessonmark);
}

/**
 * Deletes a LessonMark instance.
 *
 * @param int $id Instance id.
 * @return bool
 */
function lessonmark_delete_instance($id): bool {
    global $DB;

    if (!$DB->record_exists('lessonmark', ['id' => $id])) {
        return false;
    }
    $DB->delete_records('lessonmark', ['id' => $id]);
    return true;
}

/**
 * Supplies cached course-module information.
 *
 * @param stdClass $coursemodule Course-module record.
 * @return cached_cm_info|null
 */
function lessonmark_get_coursemodule_info($coursemodule): ?cached_cm_info {
    global $DB;

    $lessonmark = $DB->get_record('lessonmark', ['id' => $coursemodule->instance], 'id, name, intro, introformat');
    if (!$lessonmark) {
        return null;
    }
    $info = new cached_cm_info();
    $info->name = $lessonmark->name;
    if (trim((string) $lessonmark->intro) !== '') {
        $info->content = format_module_intro('lessonmark', $lessonmark, $coursemodule->id, false);
    }
    return $info;
}
