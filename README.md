Aplicacion de Residenciales
========================

1) Modelos

Residenciales
    nombre: nombre la residencial
    porcentaje: porcentaje en numero del cargo por adeudo.

Edificio: (Torre)
    nombre: nombre del edificio, ejemplo: Edificio A1
    cuota: valor que se cobra por mantenimiento mensual.
    relaciones: 
        residencial: (1-1) residencial a la que pertenece el edificio.
        recursos: (n-n) un recurso puede existir para uno o mas edificios.
        usuarios: (n-1) un usuario registrado en el edificio.

Recursos: (amenidades)
    nombre: nombre del recurso
    tipoAcceso: Es para indicar quien puede hacer uso del recurso. Este se va a descontinuar, solo va a ser nivel Residencial. 
    precio: se cambia a "cuota". Es el precio de mantenimiento del recurso. 
    
        