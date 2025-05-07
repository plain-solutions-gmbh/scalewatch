<?php

require_once(__DIR__ . '/../autoload.php');

class Routine extends Item
{
    private static array $icons = [];
    private static ?array $scopes = null;
    private static ?array $params = null;
    private User $user;

    public function __construct(...$args)
    {
        $this->user = User::factory();
        parent::__construct(...$args);
    }

    public function icon()
    {

        $name = lcfirst($this->routine());

        if (\array_key_exists($name, static::$icons)) {
            return static::$icons[$name];
        }

        $icon_folder = Config::location('icons');
        $file = "$icon_folder/$name.svg";

        if (file_exists($file) === false) {
            $file = "$icon_folder/_placeholder.svg";
        }
        $svg = file_get_contents($file);

        // Entferne Zeilenumbrüche & ersetze `"` durch `'` für saubere URL
        $svg = trim(preg_replace('/\s+/', ' ', $svg));
        $svg = str_replace('"', "'", $svg);

        // Optional: Farbe ersetzen, z. B. `currentColor` durch `%23cfd3dc`
        $svg = str_replace('currentColor', '#cfd3dc', $svg);

        // URL-encode, aber ohne `data:image/svg+xml,` selbst zu codieren
        $encoded = rawurlencode($svg);

        return "data:image/svg+xml," . $encoded;
    }

    public function isScheduled(): ?bool
    {
        if ($scheduled = $this->scheduled()) {
            return new DateTime($this->scheduled()) < new DateTime();
        }
        return null;
    }

    public function inProcessing(): bool
    {
        return $this->status() === 'processing';
    }

    public function currentUser()
    {
        return $this->user->name() === $this->user();
    }

    public function scopes()
    {
        if (($scopes = $this->get('scopes')) === null) {
            return [];
        }

        static::$scopes ??= Config::plant('scopes');

        $scopes = explode(',', $scopes);
        return array_map(function ($scope) {
            return ['id' => $scope] + (static::$scopes[$scope] ?? [
                'name' => "Scope $scope not available!"
            ]);
        }, $scopes);
    }

    public function parameters()
    {
        static::$params ??= Config::plant('params');
        $params = explode(',', $this->get('parameters') ?? '');
        return array_map(function ($param) {
            return ['id' => $param] + (static::$params[$param] ?? [
                'name' => "Parameter $param not available!"
            ]);
        }, $params);
    }

    public function cta()
    {

        switch ($this->status()) {
            case 'done':
                return [
                    'name' => 'done',
                    'label' => "Done",
                    'type' => 'success'
                ];
            case 'skipped':
                return [
                    'name' => 'skipped',
                    'label' => "Skipped",
                    'type' => 'danger'
                ];
            case 'processing':
                return $this->currentUser() ? [
                    'name' => 'processing',
                    'label' => "Continue",
                    'type' => 'warning',
                    'active' => true
                ] : [
                    'name' => 'processing',
                    'label' => "Processing...",
                    'type' => 'default',
                    'active' => false
                ];
        }

        return match ($this->isScheduled()) {
            null    => ['label' => 'Start', 'type' => 'primary', 'active' => true],
            true    => ['label' => 'Start now!', 'type' => 'warning', 'active' => true, 'priority' => true],
            false   => ['label' => "Wait until " . $this->scheduled(), 'type' => 'info', 'active' => false]
        };
    }

    public function priority()
    {

        //Is done or skipped
        if (in_array($this->status(), ['done', 'skipped'])) {
            return 10;
        }

        //You are working on it
        if ($this->user->name() === $this->user()) {
            return 2;
        }

        //Routine belongs to someone else
        if ($this->user->routine() !== $this->routine() && $this->routine() !== 'General') {
            return 9;
        }

        //Someone else is working on it
        if ($this->status() === 'processing') {
            return 4;
        }

        //Do it now!
        if ($this->isScheduled() === null) {
            return 5;
        }


        return $this->isScheduled() ? 4 : 6;
    }

    public function status(): string
    {
        return $this->get('status') ?? 'pendent';
    }

    public function isStatus(string $check): bool
    {
        return $this->status() === $check;
    }

    public function checklist()
    {
        $checklist = $this->get('checklist') ?? $this->name();
        return explode(',', $checklist);
    }

    public function statusText()
    {

        if ($this->isScheduled() && $this->isStatus('pendent')) {
            return "Scheduled on " . $this->scheduled();
        }

        $status = ucfirst($this->status());
        $user = $this->user();

        return $user ? "$status by $user" : $status;
    }

    public function toArray(): array
    {

        return [
            'status_text'   => $this->statusText(),
            'priority'      => $this->priority(),
            'checklist'     => $this->checklist(),
            'status'        => $this->status(),
            'scopes'        => $this->scopes(),
            'parameters'    => $this->parameters(),
            'cta'           => $this->cta(),
            'icon'          => $this->icon(),
        ] + parent::toArray();
    }
}
