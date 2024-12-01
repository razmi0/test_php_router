<?php

namespace App\Lib\Injector;

require_once BASE_DIR . '/vendor/autoload.php';

use App\Lib\HTTP\ErrorPage;
use Attribute;
use DOMDocument;
use DOMDocumentFragment;
use DOMNode;

#[Attribute(Attribute::TARGET_METHOD)]
class Inject
{
    private ?string $view = null;
    private ?string $content = null;

    public function __construct(
        public string $target, // target element innerHTML
    ) {}

    /**
     * @param string $view - view file path
     * @param string $content - content to inject
     * @return string|void
     */
    public function inject(string $view, string $content)
    {
        $this->view = $view;                                                                // Set the view file path
        $this->content = $content;                                                        // Set the content to inject

        $view_dom = new DOMDocument();                                                   // Create a new DOMDocument
        $hasHTML = $view_dom->loadHTMLFile($this->view);                               // Load the view file
        if (!$hasHTML) ErrorPage::HTTP500(
            file: "Inject.php",
            message: "Failed to load view file",
            data: ["view" => $this->view, "content" => $this->content]
        );
        $element = $view_dom->getElementById($this->target);                          // Get the target element
        if (!$element) {
            ErrorPage::HTTP500(
                file: "Inject.php",
                message: "Failed to find target element",
                data: ["target" => $this->target]
            );
            return;
        }
        while ($element->hasChildNodes()) {
            $firstChild = $element->firstChild;
            if (is_null($firstChild)) break;
            $element->removeChild($firstChild);
        }


        // Create a document fragment and append the content
        /**
         * @var DOMDocumentFragment | false $fragment
         */
        $fragment = $view_dom->createDocumentFragment();
        if (!$fragment) {
            ErrorPage::HTTP500(
                file: "Inject.php",
                message: "Failed to create document fragment",
                data: ["content" => $this->content]
            );
            return;
        }
        $fragment->appendXML($this->content);

        foreach (iterator_to_array($fragment->childNodes) as $child) {      // cast iterator to array. Iterate over the fragment's child nodes
            $element->appendChild($child);                                  // Inject the fragment's child nodes into the target element
        }
        $html = $view_dom->saveHTML();                                      // Save the modified view content
        if (!$html) {
            ErrorPage::HTTP500(
                file: "Inject.php",
                message: "Failed to save HTML",
                data: ["content" => $this->content]
            );
            return;
        }
        return $html;                                            // Return the modified view content
    }
}
