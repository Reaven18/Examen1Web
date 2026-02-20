<?php

class password
{

    private $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private $lower = 'abcdefghijklmnopqrstuvwxyz';
    private $digits = '0123456789';

    // símbolos comunes; puedes editar según tus políticas
    private $symbols = '!@#$%^&*()-_=+[]{}|;:,.<>?';

    // caracteres ambiguos que a veces se evitan
    private $ambiguous = 'Il1O0o';

    public $length;
    public $count = 1;
    public $upper_enabled;
    public $lower_enabled;
    public $digits_enabled;
    public $symbols_enabled;
    public $avoid_ambiguous;
    public $exclude;
    public $require_each;

    public function __construct()
    {
        $this->length = 16;
        $this->upper_enabled = true;
        $this->lower_enabled = true;
        $this->digits_enabled = true;
        $this->symbols_enabled = true;
        $this->avoid_ambiguous = true;
        $this->exclude = '';
        $this->require_each = true;
    }
    public function generateMultiple(): array
    {
        $passwords = [];
        for ($i = 0; $i < $this->count; $i++) {
            $passwords[] = $this->generate_password();
        }
        return $passwords;
    }

    function secure_random_int_between(int $min, int $max): int
    {
        return random_int($min, $max);
    }

    function shuffle_secure(string $str): string
    {
        // Fisher-Yates shuffle usando random_int
        $arr = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        $n = count($arr);
        for ($i = $n - 1; $i > 0; $i--) {
            $j = $this->secure_random_int_between(0, $i);
            $tmp = $arr[$i];
            $arr[$i] = $arr[$j];
            $arr[$j] = $tmp;
        }
        return implode('', $arr);
    }

    function generate_password(): string
    {
        if ($this->length < 1) {
            throw new InvalidArgumentException("La longitud debe ser mayor o igual a 1");
        }

        // Conjuntos de caracteres
        $sets = [];

        if ($this->upper_enabled) $sets['upper'] = $this->upper;
        if ($this->lower_enabled) $sets['lower'] = $this->lower;
        if ($this->digits_enabled) $sets['digits'] = $this->digits;
        if ($this->symbols_enabled) $sets['symbols'] = $this->symbols;

        if (empty($sets)) {
            throw new InvalidArgumentException("Debe activarse al menos una categoría (upper/lower/digits/symbols).");
        }

        // construir pool total y aplicar exclusiones
        $exclude_chars = $this->exclude;
        if ($this->avoid_ambiguous) {
            $exclude_chars .= $this->ambiguous;
        }

        // normalizar exclusions a conjunto único
        $exclude_arr = array_unique(preg_split('//u', $exclude_chars, -1, PREG_SPLIT_NO_EMPTY));
        $exclude_map = array_flip($exclude_arr);

        // filtrar sets
        foreach ($sets as $k => $chars) {
            $arr = preg_split('//u', $chars, -1, PREG_SPLIT_NO_EMPTY);
            $filtered = array_values(array_filter($arr, function ($c) use ($exclude_map) {
                return !isset($exclude_map[$c]);
            }));
            if (empty($filtered)) {
                // Si una categoría queda vacía tras exclusiones -> error
                throw new InvalidArgumentException("Después de aplicar exclusiones, la categoría '{$k}' no tiene caracteres disponibles.");
            }
            $sets[$k] = implode('', $filtered);
        }

        // crear pool total concatenado
        $pool = implode('', array_values($sets));
        if ($pool === '') {
            throw new InvalidArgumentException("No hay caracteres disponibles para generar la contraseña (pool vacío).");
        }

        $password_chars = [];

        // Si require_each: garantizar al menos un carácter de cada categoría seleccionada
        if ($this->require_each) {
            foreach ($sets as $chars) {
                $idx = $this->secure_random_int_between(0, strlen($chars) - 1);
                $password_chars[] = $chars[$idx];
            }
        }

        // Rellenar el resto de la longitud con caracteres del pool
        $needed = $this->length - count($password_chars);
        for ($i = 0; $i < $needed; $i++) {
            $idx = $this->secure_random_int_between(0, strlen($pool) - 1);
            $password_chars[] = $pool[$idx];
        }

        // Mezclar de forma segura y devolver
        $password = implode('', $password_chars);
        $password = $this->shuffle_secure($password);
        return $password;
    }

    public function validatePassword($requiments, $password): bool
    {
        if (!empty($requiments['minLength']) && strlen($password) < $requiments['minLength']) return false;
        if (!empty($requiments['maxLength']) && strlen($password) > $requiments['maxLength']) return false;
        if (!empty($requiments['requireUppercase']) && !preg_match('/[A-Z]/', $password)) return false;
        if (!empty($requiments['requireLowercase']) && !preg_match('/[a-z]/', $password)) return false;
        if (!empty($requiments['requireDigits']) && !preg_match('/[0-9]/', $password)) return false;
        if (!empty($requiments['requireSymbols']) && !preg_match('/[' . preg_quote($this->symbols, '/') . ']/', $password)) return false;
        if (!empty($requiments['avoidAmbiguous']) && preg_match('/[' . preg_quote($this->ambiguous, '/') . ']/', $password)) return false;
        if (!empty($requiments['exclude']) && preg_match('/[' . preg_quote($requiments['exclude'], '/') . ']/', $password)) return false;
        return true;
    }
}


?>