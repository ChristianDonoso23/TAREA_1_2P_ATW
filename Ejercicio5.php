<?php
// === CLASE ABSTRACTA PARA EDO ===
abstract class EcuacionDiferencial {
    abstract public function resolverEuler(callable $f): array;

    public function aplicarMetodo(callable $f): array {
        return $this->resolverEuler($f);
    }
}

// === IMPLEMENTACIÓN DEL MÉTODO DE EULER===
class EulerNumerico extends EcuacionDiferencial {
    private float $x0;
    private float $y0;
    private float $xFin;
    private int $n;

    public function __construct(array $condicionesIniciales, array $parametros) {
        $this->x0 = $condicionesIniciales['x0'];
        $this->y0 = $condicionesIniciales['y0'];
        $this->xFin = $parametros['xFin'];
        $this->n = $parametros['n'];

        if ($this->n <= 0 || $this->xFin <= $this->x0) {
            throw new InvalidArgumentException("n debe ser > 0 y xFin > x0.");
        }
    }

    public function resolverEuler(callable $f): array {
        $deltaX = ($this->xFin - $this->x0) / $this->n;
        $solucion = [];

        $x = $this->x0;
        $y = $this->y0;
        $solucion[] = ['x' => round($x, 5), 'y' => $y];

        for ($i = 0; $i < $this->n; $i++) {
            $y = $y + $deltaX * $f($x, $y);
            $x = $this->x0 + ($i + 1) * $deltaX;
            $solucion[] = ['x' => round($x, 5), 'y' => $y];
        }

        return $solucion;
    }
}

// === MENÚ DE ECUACIONES DIFERENCIALES PREDEFINIDAS ===
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
            return fn($x, $y) => $x + $y;
        case 2:
            return fn($x, $y) => ($y >= 0) ? 0.1 * sqrt($y) + 0.4 * pow($x, 2) : 0;
        case 3:
            return fn($x, $y) => $x * $y;
        case 4:
            return fn($x, $y) => pow($x, 2) - $y;
        case 5:
            return fn($x, $y) => $y / (1 + pow($x, 2));
        default:
            echo "❌ Opción inválida. Se usará f(x, y) = x + y\n";
            return fn($x, $y) => $x + $y;
    }
}

// === FUNCIÓN PARA LEER ENTRADAS NUMÉRICAS ===
function leerNumero(string $mensaje): float {
    while (true) {
        echo $mensaje;
        $valor = trim(readline());
        if (is_numeric($valor)) return (float)$valor;
        echo "❌ Entrada inválida. Intenta de nuevo.\n";
    }
}

// === INICIO DEL PROGRAMA ===
echo "\n=== Método de Euler para EDO ===\n";

$f = seleccionarFuncion();

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

// Instanciar método con datos encapsulados
$condiciones = ['x0' => $x0, 'y0' => $y0];
$parametros = ['xFin' => $xFin, 'n' => $n];

try {
    $metodo = new EulerNumerico($condiciones, $parametros);
    $solucion = $metodo->aplicarMetodo($f);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

// === MOSTRAR RESULTADOS ===
echo "\n📊 Resultados con Método de Euler:\n";
echo str_pad("Paso", 6) . str_pad("x", 12) . "y\n";
echo str_repeat("-", 30) . "\n";

foreach ($solucion as $i => $punto) {
    echo str_pad($i, 6) . str_pad(number_format($punto['x'], 5), 12) . number_format($punto['y'], 10) . "\n";
}

echo "\n✅ Proceso finalizado.\n";
