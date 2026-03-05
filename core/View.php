<?php
/**
 * View Class - Template Renderer
 */
class View {
    private $viewPath = __DIR__ . '/../app/views/';

    public function render($viewName, $data = []) {
        $file = $this->viewPath . $viewName . '.php';
        
        if (!file_exists($file)) {
            die("View file not found: {$file}");
        }

        // Extract data array to variables
        extract($data);

        include $file;
    }

    /**
     * Partial/component rendering
     */
    public function component($componentName, $data = []) {
        $file = $this->viewPath . 'components/' . $componentName . '.php';
        
        if (!file_exists($file)) {
            die("Component file not found: {$file}");
        }

        extract($data);
        include $file;
    }
}
?>
