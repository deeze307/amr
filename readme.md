## Automatic Material Request con Laravel 5.2 PHP Framework

### Visión General

Mediante la utilización de Low Level Warnings de Cogiscan, se realizan los pedidos de materiales directamente a una interfaz con EBS que interactua con en Abastecimiento de materiales de Inserción Automática.

### Intervalo AMR

El sistema recorre todas las líneas habilitadas por el sistema por defecto cada 3 minutos

### Utilización de Delta para pedidos

Cada vez que se detecta consumo de un LPN, se guarda un registro. Al contabilizar 10 registros, se calcula un delta para sacar un promedio de pedido.

### Pedidos parciales

> En desarrollo...

### Tablas de interfáz EBS que utiliza:

DB: _Traza_material_

* XXE_WMS_COGISCAN_LPN
* XXE_WMS_COGISCAN_LPN_DETAILS
* XXE_WMS_COGISCAN_OP_OPERATION
* XXE_WMS_COGISCAN_PEDIDO_LPNS
* XXE_WMS_COGISCAN_PEDIDOS
* XXE_WMS_COGISCAN_WIP

---

## Inicialización de Materiales

### Visión General

Mediante una interfáz que se nos ha generado en 

