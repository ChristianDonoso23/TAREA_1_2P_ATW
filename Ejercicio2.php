<?php

// Clase abstracta que define la estructura para estadísticas básicas
abstract class Estadistica {
    abstract public function calcularMedia($datos); // Método para calcular la media
    abstract public function calcularMediana($datos); // Método para calcular la mediana
    abstract public function calcularModa($datos); // Método para calcular la moda
}

// Clase concreta que implementa los métodos de estadística básica
class EstadisticaBasica extends Estadistica {

    // Calcula la media aritmética de un conjunto de datos
    public function calcularMedia($datos) {
        if (count($datos) === 0) return null; // Si el arreglo está vacío retorna null
        return array_sum($datos) / count($datos); // Suma todos los valores y divide para la cantidad
    }

    // Calcula la mediana de un conjunto de datos
    public function calcularMediana($datos) {
        if (count($datos) === 0) return null; // Retorna null si no hay datos
        sort($datos); // Ordena los datos de menor a mayor
        $n = count($datos); // Cantidad de datos
        $medio = (int)($n / 2); // Calcula la posición del medio
        if ($n % 2 === 0) {
            return ($datos[$medio - 1] + $datos[$medio]) / 2; // Si es par, promedio de los dos valores centrales
        } else {
            return $datos[$medio]; // Si es impar, toma el valor central
        }
    }

    // Calcula la moda de un conjunto de datos
    public function calcularModa($datos) {
        if (count($datos) === 0) return null; // Retorna null si no hay datos
        $frecuencias = array_count_values($datos); // Cuenta la frecuencia de cada valor
        $maxFrecuencia = max($frecuencias); // Obtiene la frecuencia máxima
        $moda = []; // Arreglo para guardar los valores que son moda
        foreach ($frecuencias as $numero => $freq) {
            if ($freq === $maxFrecuencia) {
                $moda[] = $numero; // Guarda el valor si tiene la frecuencia máxima
            }
        }
        if (count($moda) === count($frecuencias)) {
            return null; // Si todos los valores tienen la misma frecuencia, no hay moda
        }
        return $moda; // Retorna un arreglo con los valores de la moda
    }
}

// Función que genera un informe de media, mediana y moda para varios datasets
function generarInforme($datasets) {
    $estadistica = new EstadisticaBasica(); // Crea una instancia de EstadisticaBasica
    $informe = []; // Arreglo para guardar el informe final
    foreach ($datasets as $nombre => $datos) {
        $informe[$nombre] = [
            'media' => $estadistica->calcularMedia($datos),
            'mediana' => $estadistica->calcularMediana($datos),
            'moda' => $estadistica->calcularModa($datos)
        ]; // Calcula media, mediana y moda para cada dataset
    }
    return $informe; // Retorna el informe completo
}

// Conjuntos de datos de prueba para calcular las estadísticas
$datasets = [
    'dataset1' => [1, 2, 3, 4, 5, 1],
    'dataset2' => [5, 5, 6, 7, 5],
];

// Genera el informe de estadísticas y lo imprime
$informe = generarInforme($datasets);
print_r($informe);
