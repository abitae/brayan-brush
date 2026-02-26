<?php

namespace App\Traits;

trait ToastTrait
{
    /**
     * Show a success toast notification
     */
    public function success(string $message, string $title = 'Éxito'): void
    {
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => $title,
            'message' => $message,
        ]);
    }

    /**
     * Show an error toast notification
     */
    public function error(string $message, string $title = 'Error'): void
    {
        $this->dispatch('toast', [
            'type' => 'error',
            'title' => $title,
            'message' => $message,
        ]);
    }

    /**
     * Show an info toast notification
     */
    public function info(string $message, string $title = 'Información'): void
    {
        $this->dispatch('toast', [
            'type' => 'info',
            'title' => $title,
            'message' => $message,
        ]);
    }

    /**
     * Show a warning toast notification
     */
    public function warning(string $message, string $title = 'Advertencia'): void
    {
        $this->dispatch('toast', [
            'type' => 'warning',
            'title' => $title,
            'message' => $message,
        ]);
    }
}

