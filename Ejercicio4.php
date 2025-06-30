<?php
abstract class MatrizAbstracta {
    abstract public function multiplicar(array $matriz): array;
    abstract public function inversa(): array;
}

class Matriz extends MatrizAbstracta {
    private $matriz;

    public function __construct(array $matriz) {
        $this->matriz = $matriz;
    }

    // Metodo para imprimir matrices
    public static function imprimir(array $matriz, string $titulo = ""): void {
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

    // Metodo para obtener la submatriz eliminando fila $i y columna $j
    private static function submatriz(array $matriz, int $i, int $j): array {
        $sub = [];
        $filas = array_keys($matriz);
        unset($filas[$i]);
        $filas = array_values($filas);

        foreach ($filas as $x) {
            $columnas = array_keys($matriz[$x]);
            unset($columnas[$j]);
            $columnas = array_values($columnas);

            foreach ($columnas as $y) {
                $sub[$x][$y] = $matriz[$x][$y];
            }
        }
        return array_values(array_map('array_values', $sub));
    }

    // Metodo para calcular el determinante (recursivo)
    public static function determinante(array $matriz): float {
        $n = count($matriz);
        if ($n == 1) {
            return $matriz[0][0];
        }
        if ($n == 2) {
            return $matriz[0][0] * $matriz[1][1] - $matriz[0][1] * $matriz[1][0];
        }

        $det = 0.0;
        for ($j = 0; $j < $n; $j++) {
            $sub = self::submatriz($matriz, 0, $j);
            $det += $matriz[0][$j] * pow(-1, $j) * self::determinante($sub);
        }
        return $det;
    }

    // Metodo para multiplicar matrices
    public function multiplicar(array $matrizB): array {
        $filasA = array_keys($this->matriz);
        $columnasB = array_keys($matrizB[0]);

        $resultado = [];
        foreach ($filasA as $i) {
            foreach ($columnasB as $j) {
                $resultado[$i][$j] = 0;
                foreach ($this->matriz[$i] as $k => $valA) {
                    $resultado[$i][$j] += $valA * $matrizB[$k][$j];
                }
            }
        }
        return $resultado;
    }

    // Metodo para calcular la inversa (solo matrices cuadradas invertibles)
    public function inversa(): array {
        $n = count($this->matriz);
        $det = self::determinante($this->matriz);

        if ($det == 0) {
            throw new Exception("La matriz no es invertible (determinante = 0)");
        }

        $cofactores = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $sub = self::submatriz($this->matriz, $i, $j);
                $cofactores[$i][$j] = pow(-1, $i + $j) * self::determinante($sub);
            }
        }

        $adjunta = array_map(null, ...$cofactores); // Transponer
        $inversa = [];
        foreach ($adjunta as $i => $fila) {
            foreach ($fila as $j => $valor) {
                $inversa[$i][$j] = $valor / $det;
            }
        }
        return $inversa;
    }
}

// Interaccion por consola
echo "Operaciones con Matrices n x m\n";
echo "Ingrese las dimensiones de la matriz A (n x m):\n";
$n = (int)readline("Filas (n): ");
$m = (int)readline("Columnas (m): ");

$matrizA = [];
for ($i = 0; $i < $n; $i++) {
    $matrizA[$i] = [];
    for ($j = 0; $j < $m; $j++) {
        $matrizA[$i][$j] = (float)readline("Valor para fila $i, columna $j: ");
    }
}

$matriz = new Matriz($matrizA);

// Determinante (solo para matrices cuadradas)
if ($n == $m) {
    try {
        $det = Matriz::determinante($matrizA);
        echo "Determinante: " . number_format($det, 2) . "\n";
    } catch (Exception $e) {
        echo "Error al calcular determinante: " . $e->getMessage() . "\n";
    }
} else {
    echo "El determinante solo se calcula para matrices cuadradas.\n";
}

// Inversa (solo para matrices cuadradas invertibles)
if ($n == $m) {
    try {
        $inversa = $matriz->inversa();
        Matriz::imprimir($inversa, "Inversa");
    } catch (Exception $e) {
        echo "Error al calcular inversa: " . $e->getMessage() . "\n";
    }
} else {
    echo "La inversa solo existe para matrices cuadradas.\n";
}

// Multiplicacion por otra matriz
echo "\nIngrese las dimensiones de la matriz B (m x p):\n";
$p = (int)readline("Columnas (p): ");

$matrizB = [];
for ($i = 0; $i < $m; $i++) {
    $matrizB[$i] = [];
    for ($j = 0; $j < $p; $j++) {
        $matrizB[$i][$j] = (float)readline("Valor para fila $i, columna $j: ");
    }
}

try {
    $producto = $matriz->multiplicar($matrizB);
    Matriz::imprimir($producto, "Producto A x B");
} catch (Exception $e) {
    echo "Error al multiplicar: " . $e->getMessage() . "\n";
}
?>