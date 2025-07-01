<?php

// Clase abstracta que define la estructura para sistemas de ecuaciones
abstract class SistemaEcuaciones {
    abstract public function calcularResultado();
    abstract public function validarConsistencia();
}

// Clase que implementa el método de sustitución para sistemas de 2x2
class SistemaLineal extends SistemaEcuaciones {
    private $ecuacion1;
    private $ecuacion2;

    // Constructor que recibe las dos ecuaciones
    public function __construct($ecuacion1, $ecuacion2) {
        $this->ecuacion1 = $ecuacion1;
        $this->ecuacion2 = $ecuacion2;
    }

    // Verifica que se pueda despejar x de la segunda ecuación
    public function validarConsistencia() {
        return $this->ecuacion2['x'] != 0;
    }

    public function calcularResultado() {
        if (!$this->validarConsistencia()) {
            return "No se puede despejar x de la segunda ecuación (a2 = 0).";
        }

        // Extraemos coeficientes de las ecuaciones
        $a1 = $this->ecuacion1['x'];
        $b1 = $this->ecuacion1['y'];
        $c1 = $this->ecuacion1['c'];
        $a2 = $this->ecuacion2['x'];
        $b2 = $this->ecuacion2['y'];
        $c2 = $this->ecuacion2['c'];

        // Realizamos el paso de sustitución en forma algebraica compacta
        $izquierda = $a1 * $c2 - $a1 * $b2 * 1 + $b1 * 1 * $a2;
        $derecha = $c1 * $a2;

        // Calculamos el coeficiente de y y la constante para despejar y
        $coefY = -$a1 * $b2 + $b1 * $a2;
        $constante = $a1 * $c2;

        // Calculamos y con el método de sustitución
        $y = ($derecha - $constante) / $coefY;

        // Calculamos x usando el valor encontrado de y
        $x = ($c2 - $b2 * $y) / $a2;

        // Retornamos el resultado en un arreglo asociativo
        return ['x' => $x, 'y' => $y];
    }
}

// Función para resolver el sistema de ecuaciones llamando la clase
function resolverSistema($ecuacion1, $ecuacion2) {
    $sistema = new SistemaLineal($ecuacion1, $ecuacion2);
    return $sistema->calcularResultado();
}

// Ejemplo de ecuaciones a resolver
$ecuacion1 = ['x' => 2, 'y' => 3, 'c' => 5];
$ecuacion2 = ['x' => 1, 'y' => -1, 'c' => 1];

// Se resuelve el sistema y se imprime el resultado
$resultado = resolverSistema($ecuacion1, $ecuacion2);
print_r($resultado);
