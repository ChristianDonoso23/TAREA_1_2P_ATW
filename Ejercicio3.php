<?php
abstract class PolinomioAbstracto
{
    abstract public function evaluar(float $x): float;
    abstract public function derivada(): array;
}

class Polinomio extends PolinomioAbstracto
{
    private $terminos;

    public function __construct(array $terminos)
    {
        $this->terminos = $terminos;
    }

    // Método para imprimir cualquier array asociativo
    public static function imprimir(array $datos, string $titulo = ""): void
    {
        if ($titulo !== "") {
            echo "$titulo:\n";
        }
        print_r($datos);
    }

    public function evaluar(float $x): float
    {
        $resultado = 0.0;
        foreach ($this->terminos as $grado => $coeficiente) {
            $resultado += $coeficiente * pow($x, $grado);
        }
        return $resultado;
    }

    public function derivada(): array
    {
        $derivada = [];
        foreach ($this->terminos as $grado => $coeficiente) {
            if ($grado > 0) {
                $derivada[$grado - 1] = $coeficiente * $grado;
            }
        }
        return $derivada;
    }

    public static function sumarPolinomios(array $p1, array $p2): array
    {
        $suma = $p1;
        foreach ($p2 as $grado => $coeficiente) {
            $suma[$grado] = ($suma[$grado] ?? 0) + $coeficiente;
        }
        krsort($suma);
        return $suma;
    }
}

// Interacción por consola
echo "Manejo de Polinomios\n";
echo "Formato: [grado => coeficiente, ...] (ej: 2x^3 + 1 = [3 => 2, 0 => 1])\n\n";

$terminos1 = [];
$terminos2 = [];

$n = (int)readline("Número de términos del Polinomio 1: ");
for ($i = 0; $i < $n; $i++) {
    $grado = (int)readline("Grado del término " . ($i + 1) . ": ");
    $coef = (float)readline("Coeficiente para x^$grado: ");
    $terminos1[$grado] = $coef;
}

$n = (int)readline("\nNúmero de términos del Polinomio 2: ");
for ($i = 0; $i < $n; $i++) {
    $grado = (int)readline("Grado del término " . ($i + 1) . ": ");
    $coef = (float)readline("Coeficiente para x^$grado: ");
    $terminos2[$grado] = $coef;
}

$polinomio = new Polinomio($terminos1);

// Evaluar
$x = (float)readline("\nValor de x para evaluar Polinomio 1: ");
echo "P($x) = " . $polinomio->evaluar($x) . "\n";

// Derivada
$derivada = $polinomio->derivada();
Polinomio::imprimir($derivada, "Derivada P'(x)");

// Suma
$suma = Polinomio::sumarPolinomios($terminos1, $terminos2);
Polinomio::imprimir($suma, "Suma P1(x) + P2(x)");
