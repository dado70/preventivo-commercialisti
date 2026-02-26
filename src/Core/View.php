<?php
/**
 * Preventivo Commercialisti - View Helper
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Core;

class View
{
    /**
     * Renderizza un template con layout.
     */
    public static function render(string $template, array $data = [], bool $withLayout = true): void
    {
        extract($data);

        if ($withLayout) {
            $content = self::capture($template, $data);
            include SRC_PATH . '/Views/layout/main.php';
        } else {
            include SRC_PATH . '/Views/' . $template . '.php';
        }
    }

    /**
     * Cattura l'output di un template in una stringa.
     */
    public static function capture(string $template, array $data = []): string
    {
        extract($data);
        ob_start();
        include SRC_PATH . '/Views/' . $template . '.php';
        return ob_get_clean();
    }

    /**
     * Escape HTML per output sicuro.
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Formatta un importo in euro.
     */
    public static function euro(mixed $value): string
    {
        return '€ ' . number_format((float)$value, 2, ',', '.');
    }

    /**
     * Formatta una data italiana.
     */
    public static function data(mixed $value, string $format = 'd/m/Y'): string
    {
        if (!$value) return '';
        return date($format, strtotime($value));
    }

    /**
     * URL relativo all'asset.
     */
    public static function asset(string $path): string
    {
        return BASE_URL . '/assets/' . ltrim($path, '/');
    }

    /**
     * URL relativo a una route.
     */
    public static function url(string $path): string
    {
        return BASE_URL . '/' . ltrim($path, '/');
    }
}
