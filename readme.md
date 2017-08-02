## Automatic Material Request (AMR) con Laravel 5.2 PHP Framework

### Visión General

Mediante la utilización de Low Level Warnings de Cogiscan, se realizan los pedidos de materiales directamente a una interfaz con EBS que interactua con en Abastecimiento de materiales de Inserción Automática.

### Intervalo AMR

El sistema recorre todas las líneas habilitadas por el sistema por defecto cada 3 minutos

### Utilización de Delta para pedidos

Cada vez que se detecta consumo de un LPN, se guarda un registro. Al contabilizar 10 registros, se calcula un delta para sacar un promedio de pedido.

### Pedidos parciales para cada ubicación (`En Desarrollo`)
Ejemplo:
```php
pedido:{
  cantAsolic: 5000
  transito: 2000
  almacenIA: 2000
  interfaz: 1000
}
```

### Tablas de interfáz EBS que utiliza:

DB: _Traza_material_

* XXE_WMS_COGISCAN_LPN
* XXE_WMS_COGISCAN_LPN_DETAILS
* XXE_WMS_COGISCAN_OP_OPERATION
* XXE_WMS_COGISCAN_PEDIDO_LPNS
* XXE_WMS_COGISCAN_PEDIDOS
* XXE_WMS_COGISCAN_WIP

---

## Automatic Material Initialization (AMI) con Laravel 5.2 PHP Framework

### Visión General

Mediante una interfáz de EBS que se nos ha generado en SQL Server, cada 1 hora, esos datos son copiados a una tabla llamada `XXE_WMS_COGISCAN_PEDIDO_LPNS`. Esta tabla se recorre cada 1 minuto por un concurrente que inicializa cada LPN en Cogiscan y los inserta en una tabla de MySql para tener un registro de los materiales inicializados por jornada.

### Datos adicionales

* Al no contar con información necesaria aun para saber si un LPN asignado corresponde a un rollo, bandeja, varilla, etc, cada LPN es inicializado como tipo REEL (Rollo).

* Al no contar con información necesaria aun para saber si un LPN es sensible a la Humedad, cada LPN se inicializa en Nivel 1. El único producto que tiene configurado materiales sensibles a la humedad es LG ya que esa información fué suministrada en la lista BOM.

