<?php
/**
 * Source normalisation for LessonMark.
 *
 * @package   mod_lessonmark
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_lessonmark\local;

/** Neutralises raw HTML while preserving code syntax. */
final class source_normalizer {
    /**
     * @param string $source Original Markdown source.
     * @return string Normalised renderer input.
     */
    public function neutralise_raw_html(string $source): string {
        $lines = preg_split('/\R/u', $source);
        if ($lines === false) {
            return '';
        }
        $normalised = [];
        $fencecharacter = null;
        $fencelength = 0;
        foreach ($lines as $line) {
            if ($fencecharacter !== null) {
                $normalised[] = $line;
                $quoted = preg_quote($fencecharacter, '/');
                if (preg_match('/^\s*' . $quoted . '{' . $fencelength . ',}\s*$/', $line) === 1) {
                    $fencecharacter = null;
                    $fencelength = 0;
                }
                continue;
            }
            if (preg_match('/^\s*(`{3,}|~{3,})/', $line, $matches) === 1) {
                $fencecharacter = $matches[1][0];
                $fencelength = strlen($matches[1]);
                $normalised[] = $line;
                continue;
            }
            $normalised[] = $this->escape_outside_inline_code($line);
        }
        return implode("\n", $normalised);
    }

    /**
     * @param string $line Source line outside fenced code.
     * @return string
     */
    private function escape_outside_inline_code(string $line): string {
        $result = '';
        $offset = 0;
        while ($offset < strlen($line)) {
            $opening = strpos($line, '`', $offset);
            if ($opening === false) {
                return $result . $this->escape_angles(substr($line, $offset));
            }
            $result .= $this->escape_angles(substr($line, $offset, $opening - $offset));
            $runlength = strspn($line, '`', $opening);
            $delimiter = str_repeat('`', $runlength);
            $closing = strpos($line, $delimiter, $opening + $runlength);
            if ($closing === false) {
                return $result . $this->escape_angles(substr($line, $opening));
            }
            $spanlength = ($closing + $runlength) - $opening;
            $result .= substr($line, $opening, $spanlength);
            $offset = $closing + $runlength;
        }
        return $result;
    }

    /**
     * @param string $text Text outside code syntax.
     * @return string
     */
    private function escape_angles(string $text): string {
        return str_replace(['<', '>'], ['&lt;', '&gt;'], $text);
    }
}

