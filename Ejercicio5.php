<?php
// === CLASES ===
abstract class EcuacionDiferencial {
    abstract public function resolverEuler(callable $f, array $condicionesIniciales, array $parametros): array;

    public function aplicarMetodo(callable $f, array $condicionesIniciales, array $parametros): array {
        return $this->resolverEuler($f, $condicionesIniciales, $parametros);
    }
}

class EulerNumerico extends EcuacionDiferencial {
    public function resolverEuler(callable $f, array $condicionesIniciales, array $parametros): array {
        $a = $condicionesIniciales['x0'];
        $y0 = $condicionesIniciales['y0'];
        $b = $parametros['xFin'];
        $n = $parametros['n'];

        if ($n <= 0 || $b <= $a) {
            throw new InvalidArgumentException("Verifica que n > 0 y que xFin > x0.");
        }

        $deltaX = ($b - $a) / $n;
        $solucion = [];

        $x = $a;
        $y = $y0;
        $solucion[] = ['x' => round($x, 5), 'y' => $y];

        for ($i = 0; $i < $n; $i++) {
            $y = $y + $deltaX * $f($x, $y);
            $x = $a + ($i + 1) * $deltaX;
            $solucion[] = ['x' => round($x, 5), 'y' => $y];
        }

        return $solucion;
    }
}

// === FUNCIÓN DIFERENCIAL PREDEFINIDA ===

function seleccionarFuncion(): callable {
    echo "\n=== Seleccione la ecuación diferencial dy/dx = f(x, y) ===\n";
    echo "1. f(x, y) = x + y\n";
    echo "2. f(x, y) = 0.1 * sqrt(y) + 0.4 * x^2\n";
    echo "3. f(x, y) = x * y\n";
    echo "4. f(x, y) = x^2 - y\n";
    echo "5. f(x, y) = y / (1 + x^2)\n";
    echo "Opción (1-5): ";

    $opcion = (int)readline();

    switch ($opcion) {
        case 1:
            return function($x, $y) { return $x + $y; };
        case 2:
            return function($x, $y) {
                return ($y >= 0) ? 0.1 * sqrt($y) + 0.4 * pow($x, 2) : 0;
            };
        case 3:
            return function($x, $y) { return $x * $y; };
        case 4:
            return function($x, $y) { return pow($x, 2) - $y; };
        case 5:
            return function($x, $y) { return $y / (1 + pow($x, 2)); };
        default:
            echo "❌ Opción inválida. Se usará f(x, y) = x + y\n";
            return function($x, $y) { return $x + $y; };
    }
}

// === LECTURA DE PARÁMETROS ===
function leerNumero(string $mensaje): float {
    while (true) {
        echo $mensaje;
        $valor = trim(readline());
        if (is_numeric($valor)) return (float)$valor;
        echo "❌ Entrada inválida. Intenta de nuevo.\n";
    }
}

// === EJECUCIÓN PRINCIPAL ===

echo "\n=== Método de Euler para EDO ===\n";

// Seleccionar la función diferencial
$f = seleccionarFuncion();

// Leer valores
$x0 = leerNumero("Ingrese el valor inicial de x (x0): ");
$y0 = leerNumero("Ingrese el valor inicial de y (y0): ");
$xFin = leerNumero("Ingrese el valor final de x (xFin > x0): ");
while ($xFin <= $x0) {
    echo "❌ xFin debe ser mayor que x0.\n";
    $xFin = leerNumero("Ingrese el valor final de x (xFin > x0): ");
}
$n = (int)leerNumero("Ingrese el número de pasos (n entero > 0): ");
while ($n <= 0) {
    echo "❌ El número de pasos debe ser positivo.\n";
    $n = (int)leerNumero("Ingrese el número de pasos (n entero > 0): ");
}

// Procesar
$metodo = new EulerNumerico();
$condiciones = ['x0' => $x0, 'y0' => $y0];
$parametros = ['xFin' => $xFin, 'n' => $n];

try {
    $solucion = $metodo->aplicarMetodo($f, $condiciones, $parametros);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Mostrar resultados
echo "\n📊 Resultados con Método de Euler:\n";
echo str_pad("Paso", 6) . str_pad("x", 12) . "y\n";
echo str_repeat("-", 30) . "\n";
foreach ($solucion as $i => $punto) {
    echo str_pad($i, 6) . str_pad(number_format($punto['x'], 5), 12) . number_format($punto['y'], 10) . "\n";
}
echo "\n✅ Proceso finalizado.\n";
