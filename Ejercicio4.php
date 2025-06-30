<?php
abstract class MatrizAbstracta
{
    abstract public function multiplicar(array $matriz): array;
    abstract public function inversa(): array;
}

class Matriz extends MatrizAbstracta
{
    private $matriz;

    public function __construct(array $matriz)
    {
        $this->matriz = $matriz;
    }

    // Método para imprimir matrices
    public static function imprimir(array $matriz, string $titulo = ""): void
    {
        if ($titulo !== "") {
            echo "$titulo:\n";
        }
        foreach ($matriz as $fila) {
            echo "| ";
            foreach ($fila as $valor) {
                echo number_format($valor, 2) . " ";
            }
            echo "|\n";
        }
    }

    public function multiplicar(array $matrizB): array
    {
        $resultado = [];
        $filasA = array_keys($this->matriz);
        $columnasB = array_keys($matrizB[array_key_first($matrizB)]);

        foreach ($filasA as $i) {
            foreach ($columnasB as $j) {
                $resultado[$i][$j] = 0;
                foreach ($this->matriz[$i] as $k => $valA) {
                    $resultado[$i][$j] += $valA * ($matrizB[$k][$j] ?? 0);
                }
            }
        }
        return $resultado;
    }

    public function inversa(): array
    {
        if (count($this->matriz) != 2 || count($this->matriz[array_key_first($this->matriz)]) != 2) {
            throw new Exception("Solo matrices 2x2 soportadas");
        }

        $a = $this->matriz[0][0] ?? 0;
        $b = $this->matriz[0][1] ?? 0;
        $c = $this->matriz[1][0] ?? 0;
        $d = $this->matriz[1][1] ?? 0;

        $det = ($a * $d) - ($b * $c);
        if ($det == 0) throw new Exception("Matriz no invertible");

        return [
            0 => [0 => $d / $det, 1 => -$b / $det],
            1 => [0 => -$c / $det, 1 => $a / $det]
        ];
    }

    public static function determinante(array $matriz): float
    {
        if (count($matriz) == 2 && count($matriz[0]) == 2) {
            return ($matriz[0][0] * $matriz[1][1]) - ($matriz[0][1] * $matriz[1][0]);
        }
        throw new Exception("Solo matrices 2x2 soportadas");
    }
}

// Interacción por consola
echo "Operaciones con Matrices 2x2\n";
echo "Formato: [fila][columna] = valor\n\n";

$matrizA = [];
for ($i = 0; $i < 2; $i++) {
    $matrizA[$i] = [];
    for ($j = 0; $j < 2; $j++) {
        $matrizA[$i][$j] = (float)readline("Valor para fila $i, columna $j: ");
    }
}

$matriz = new Matriz($matrizA);

// Determinante
echo "Determinante: " . Matriz::determinante($matrizA) . "\n";

// Inversa
try {
    $inversa = $matriz->inversa();
    Matriz::imprimir($inversa, "Inversa");
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
