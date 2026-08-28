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
 * Module form for LessonMark.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Defines the LessonMark activity form.
 */
class mod_lessonmark_mod_form extends moodleform_mod {
    /**
     * Defines form elements.
     */
    public function definition(): void {
        $mform = $this->_form;
        $mform->addElement('text', 'name', get_string('lessonmarkname', 'mod_lessonmark'), ['size' => 64]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->standard_intro_elements();
        $mform->addElement('textarea', 'markdownsource', get_string('markdownsource', 'mod_lessonmark'), [
            'rows' => 24,
            'cols' => 100,
            'wrap' => 'off',
        ]);
        $mform->setType('markdownsource', PARAM_RAW);
        $mform->addRule('markdownsource', null, 'required', null, 'client');
        $mform->addHelpButton('markdownsource', 'markdownsource', 'mod_lessonmark');
        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Validates Markdown source constraints.
     *
     * @param array $data Submitted values.
     * @param array $files Submitted files.
     * @return array Validation errors.
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        $source = (string) ($data['markdownsource'] ?? '');
        if (strlen($source) > \mod_lessonmark\local\moodle_markdown_renderer::MAX_SOURCE_BYTES) {
            $errors['markdownsource'] = get_string('errorsourcetoolarge', 'mod_lessonmark');
        } else if (preg_match('//u', $source) !== 1) {
            $errors['markdownsource'] = get_string('errorinvalidutf8', 'mod_lessonmark');
        }
        return $errors;
    }
}

