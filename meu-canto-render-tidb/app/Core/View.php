<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    /** Renderiza app/Views/{$view}.php dentro de layouts/{$layout}.php */
    public static function render(string $view, array $data = [], string $layout = 'main'): string
    {
        $content = self::partial($view, $data);
        if ($layout === '') {
            return $content;
        }
        return self::partial('layouts/' . $layout, $data + ['content' => $content]);
    }

    public static function partial(string $view, array $data = []): string
    {
        $file = BASE_PATH . '/app/Views/' . $view . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("View não encontrada: {$view}");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }
}
