@mod @mod_lessonmark
Feature: Author and publish a LessonMark teaching resource
  In order to publish reusable Markdown teaching material
  As a teacher
  I need to preview and save the same accessible document students will read

  Background:
    Given the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "activities" exist:
      | activity   | name         | intro              | course | idnumber | markdownsource   |
      | lessonmark | Lesson one   | Teaching resource  | C1     | LM1      | # Original lesson |

  @javascript @accessibility
  Scenario: Preview and publish an accessible teaching document
    Given I am on the "Lesson one" "lessonmark activity editing" page logged in as admin
    Then the "wrap" attribute of "Markdown source" "field" should contain "soft"
    When I set the LessonMark Markdown source to:
      """
      # Updated lesson

      ## Example

      > [!NOTE]
      > Check this explanation.

      ```python
      print("Hello")
      ```

      | Item | Value |
      | --- | --- |
      | Answer | 42 |

      Inline formula: `math:\frac{a}{b}`

      ```asciimath
      sum_(i=1)^n i = (n(n+1))/2
      ```

      ```mermaid
      flowchart LR
          Draft --> Review --> Publish
      ```
      """
    And I press "Refresh preview"
    And I wait until "Preview updated." "text" exists
    Then I should see "Updated lesson" in the "[data-region=\"preview-content\"]" "css_element"
    And ".ozmd-math .katex" "css_element" should exist in the "[data-region=\"preview-content\"]" "css_element"
    And ".ozmd-mermaid svg" "css_element" should exist in the "[data-region=\"preview-content\"]" "css_element"
    And the "Copy LaTeX" "button" should exist
    And the LessonMark source editor should stay aligned with its preview
    And the LessonMark source editor should remain visible while the preview scrolls
    And the page should meet accessibility standards
    When I press "Save and display"
    Then I should see "Updated lesson"
    And I should see "Contents"
    And I should see "Note"
    And "[role=\"main\"] > h2" "css_element" should not exist
    And ".ozmd-math .katex" "css_element" should exist
    And ".ozmd-mermaid svg" "css_element" should exist
    And the "Copy LaTeX" "button" should exist
    And the page should meet accessibility standards

  @javascript
  Scenario: Invalid formulas and diagrams retain their source
    Given I am on the "Lesson one" "lessonmark activity editing" page logged in as admin
    When I set the LessonMark Markdown source to:
      """
      # Invalid browser source

      ```math
      \\frac{
      ```

      ```mermaid
      not a diagram
      ```
      """
    And I press "Refresh preview"
    And I wait until "Preview updated." "text" exists
    And I wait until ".ozmd-math-error" "css_element" exists
    And I wait until ".ozmd-mermaid-error" "css_element" exists
    Then I should see "\\frac{" in the "[data-region=\"preview-content\"]" "css_element"
    And I should see "not a diagram" in the "[data-region=\"preview-content\"]" "css_element"
    When I press "Save and display"
    Then I should see "\\frac{"
    And I should see "not a diagram"
