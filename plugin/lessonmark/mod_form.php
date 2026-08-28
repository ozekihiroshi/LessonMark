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
        global $PAGE;

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

        $previewid = 'lessonmark-preview-' . random_int(1000, 999999);
        $preview = html_writer::start_div('mod_lessonmark-editor', [
            'data-region' => 'lessonmark-editor',
            'data-preview-id' => $previewid,
        ]);
        $preview .= html_writer::start_div('mod_lessonmark-editor__toolbar', [
            'role' => 'tablist',
            'aria-label' => get_string('editormodes', 'mod_lessonmark'),
        ]);
        $preview .= html_writer::tag('button', get_string('editmode', 'mod_lessonmark'), [
            'type' => 'button',
            'class' => 'btn btn-secondary mod_lessonmark-editor__tab is-active',
            'data-action' => 'show-editor',
            'role' => 'tab',
            'aria-selected' => 'true',
        ]);
        $preview .= html_writer::tag('button', get_string('previewmode', 'mod_lessonmark'), [
            'type' => 'button',
            'class' => 'btn btn-secondary mod_lessonmark-editor__tab',
            'data-action' => 'show-preview',
            'role' => 'tab',
            'aria-selected' => 'false',
        ]);
        $preview .= html_writer::tag('button', get_string('refreshpreview', 'mod_lessonmark'), [
            'type' => 'button',
            'class' => 'btn btn-link ml-auto',
            'data-action' => 'refresh-preview',
        ]);
        $preview .= html_writer::end_div();
        $preview .= html_writer::div(
            get_string('previewempty', 'mod_lessonmark'),
            'mod_lessonmark-editor__preview mod_lessonmark-content text-muted',
            [
                'id' => $previewid,
                'data-region' => 'preview-content',
                'role' => 'tabpanel',
            ]
        );
        $preview .= html_writer::div('', 'mod_lessonmark-editor__status', [
            'data-region' => 'preview-status',
            'role' => 'status',
            'aria-live' => 'polite',
        ]);
        $preview .= html_writer::end_div();
        $mform->addElement('html', $preview);

        $cmid = $this->_cm ? (int) $this->_cm->id : 0;
        $courseid = (int) ($this->current->course ?? 0);
        $PAGE->requires->js_call_amd('mod_lessonmark/editor', 'init', [[
            'endpoint' => (new moodle_url('/mod/lessonmark/preview.php'))->out(false),
            'sourceSelector' => '#id_markdownsource',
            'containerSelector' => '[data-preview-id="' . $previewid . '"]',
            'cmid' => $cmid,
            'courseid' => $courseid,
            'sesskey' => sesskey(),
            'strings' => [
                'loading' => get_string('previewloading', 'mod_lessonmark'),
                'ready' => get_string('previewready', 'mod_lessonmark'),
                'error' => get_string('previewerror', 'mod_lessonmark'),
                'empty' => get_string('previewempty', 'mod_lessonmark'),
            ],
        ]]);
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
