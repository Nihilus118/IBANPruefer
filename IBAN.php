<?php

class IBAN
{
    private string $iban;

    function __construct(string $iban)
    {
        $this->iban = strtoupper(str_replace(' ', '', $iban));
    }

    public static function pruefen(string $iban): bool
    {
        $iban = strtoupper(str_replace(' ', '', $iban));
        if (preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $iban)) {
            return (new IBAN($iban))->pruefsumCheck();
        }
        return false;
    }

    private function pruefsumCheck(): bool
    {
        $tempiban = preg_replace('/[^a-zA-Z0-9]/', '', $this->iban);
        $tempiban = substr($tempiban, 4) . substr($tempiban, 0, 4);
        $tempiban = $this->pruefsumFormat($tempiban);
        return !($this->mod97_10($tempiban) != 1);
    }

    private function pruefsumFormat(string $iban)
    {
        $iban_replace_chars = range('A', 'Z');
        foreach (range(10, 35) as $tempval) {
            $iban_replace_values[] = strval($tempval);
        }
        return str_replace($iban_replace_chars, $iban_replace_values ?? array(), $iban);
    }

    private function mod97_10(string $pruefsumFormat): bool
    {
        $len = strlen($pruefsumFormat);
        $rest = "";
        $pos = 0;
        while ($pos < $len) {
            $value = 9 - strlen($rest);
            $n = $rest . substr($pruefsumFormat, $pos, $value);
            $rest = $n % 97;
            $pos += $value;
        }
        return ($rest === 1);
    }
}