<?php
// === CLASE ABSTRACTA PARA EDO ===
abstract class EcuacionDiferencial {
    // Método abstracto que debe implementar el método de resolución específico
    abstract public function resolverEuler(callable $f, array $condicionesIniciales, array $parametros): array;

    // Método genérico para aplicar el método elegido
    public function aplicarMetodo(callable $f, array $condicionesIniciales, array $parametros): array {
        return $this->resolverEuler($f, $condicionesIniciales, $parametros);
    }
}

// === IMPLEMENTACIÓN DEL MÉTODO DE EULER ===
class EulerNumerico extends EcuacionDiferencial {
    public function resolverEuler(callable $f, array $condicionesIniciales, array $parametros): array {
        // Extraer condiciones iniciales y parámetros
        $a = $condicionesIniciales['x0'];
        $y0 = $condicionesIniciales['y0'];
        $b = $parametros['xFin'];
        $n = $parametros['n'];

        // Validar que los valores sean válidos
        if ($n <= 0 || $b <= $a) {
            throw new InvalidArgumentException("Verifica que n > 0 y que xFin > x0.");
        }

        // Calcular tamaño de paso
        $deltaX = ($b - $a) / $n;
        $solucion = [];

        // Inicializar con condiciones iniciales
        $x = $a;
        $y = $y0;
        $solucion[] = ['x' => round($x, 5), 'y' => $y];

        // Aplicar el método de Euler n veces
        for ($i = 0; $i < $n; $i++) {
            // Calcular el nuevo y utilizando la fórmula de Euler
            $y = $y + $deltaX * $f($x, $y);
            // Avanzar en x
            $x = $a + ($i + 1) * $deltaX;
            // Guardar el punto
            $solucion[] = ['x' => round($x, 5), 'y' => $y];
        }

        return $solucion;
    }
}

// === MENÚ DE ECUACIONES DIFERENCIALES PREDEFINIDAS ===
// Permite al usuario elegir una función diferencial dy/dx = f(x, y) ya definida.
function seleccionarFuncion(): callable {
    echo "\n=== Seleccione la ecuación diferencial dy/dx = f(x, y) ===\n";
    echo "1. f(x, y) = x + y\n";
    echo "2. f(x, y) = 0.1 * sqrt(y) + 0.4 * x^2\n";
    echo "3. f(x, y) = x * y\n";
    echo "4. f(x, y) = x^2 - y\n";
    echo "5. f(x, y) = y / (1 + x^2)\n";
    echo "Opción (1-5): ";

    // Leer la opción seleccionada
    $opcion = (int)readline();

    // Devolver la función correspondiente
    switch ($opcion) {
        case 1:
            return function($x, $y) { return $x + $y; };
        case 2:
            return function($x, $y) {
                // Validación para evitar sqrt de número negativo
                return ($y >= 0) ? 0.1 * sqrt($y) + 0.4 * pow($x, 2) : 0;
            };
        case 3:
            return function($x, $y) { return $x * $y; };
        case 4:
            return function($x, $y) { return pow($x, 2) - $y; };
        case 5:
            return function($x, $y) { return $y / (1 + pow($x, 2)); };
        default:
            // Opción inválida: usar una función por defecto
            echo "❌ Opción inválida. Se usará f(x, y) = x + y\n";
            return function($x, $y) { return $x + $y; };
    }
}

// === FUNCIÓN AUXILIAR PARA LEER NÚMEROS CON VALIDACIÓN ===
function leerNumero(string $mensaje): float {
    while (true) {
        echo $mensaje;
        $valor = trim(readline());
        // Validar que se haya ingresado un número
        if (is_numeric($valor)) return (float)$valor;
        echo "❌ Entrada inválida. Intenta de nuevo.\n";
    }
}

// === INICIO DEL PROGRAMA PRINCIPAL ===

echo "\n=== Método de Euler para EDO ===\n";

// Seleccionar ecuación diferencial
$f = seleccionarFuncion();

// Leer condiciones iniciales
$x0 = leerNumero("Ingrese el valor inicial de x (x0): ");
$y0 = leerNumero("Ingrese el valor inicial de y (y0): ");
$xFin = leerNumero("Ingrese el valor final de x (xFin > x0): ");

// Validar que xFin > x0
while ($xFin <= $x0) {
    echo "❌ xFin debe ser mayor que x0.\n";
    $xFin = leerNumero("Ingrese el valor final de x (xFin > x0): ");
}

// Leer número de pasos n
$n = (int)leerNumero("Ingrese el número de pasos (n entero > 0): ");
while ($n <= 0) {
    echo "❌ El número de pasos debe ser positivo.\n";
    $n = (int)leerNumero("Ingrese el número de pasos (n entero > 0): ");
}

// Crear instancia del método numérico
$metodo = new EulerNumerico();

// Arreglos con las condiciones y parámetros
$condiciones = ['x0' => $x0, 'y0' => $y0];
$parametros = ['xFin' => $xFin, 'n' => $n];

try {
    // Ejecutar el método de Euler
    $solucion = $metodo->aplicarMetodo($f, $condiciones, $parametros);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

// === MOSTRAR RESULTADOS ===
// Encabezado de la tabla
echo "\n📊 Resultados con Método de Euler:\n";
echo str_pad("Paso", 6) . str_pad("x", 12) . "y\n";
echo str_repeat("-", 30) . "\n";

// Imprimir cada punto calculado
foreach ($solucion as $i => $punto) {
    echo str_pad($i, 6) . str_pad(number_format($punto['x'], 5), 12) . number_format($punto['y'], 10) . "\n";
}

echo "\n✅ Proceso finalizado.\n";
