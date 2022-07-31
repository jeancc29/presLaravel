<?php

use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Type::updateOrCreate(["descripcion" => "Hombre"], ["renglon" => "sexo"]);
        \App\Type::updateOrCreate(["descripcion" => "Mujer"], ["renglon" => "sexo"]);
        \App\Type::updateOrCreate(["descripcion" => "Mujer"], ["renglon" => "sexo"]);
        \App\Type::updateOrCreate(["descripcion" => "Cedula identidad"], ["renglon" => "documento"]);
        \App\Type::updateOrCreate(["descripcion" => "RNC"], ["renglon" => "documento"]);
        \App\Type::updateOrCreate(["descripcion" => "Pasaporte"], ["renglon" => "documento"]);

        \App\Type::updateOrCreate(["descripcion" => "Ninguna"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Combustible"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Gastos Diversos"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Nómina"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Comisión Agente"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Aportaciones"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Automóvil"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Renta"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Sistema"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Pagina Web"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Imprestos"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Seguro Social"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Comisiones Bancarias"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Misceláneo"], ["renglon" => "gasto"]);
        \App\Type::updateOrCreate(["descripcion" => "Almuerzo Administrativo"], ["renglon" => "gasto"]);

        \App\Type::updateOrCreate(["descripcion" => "Cuota fija"], ["renglon" => "amortizacion"]);
        \App\Type::updateOrCreate(["descripcion" => "Disminuir cuota"], ["renglon" => "amortizacion"]);
        \App\Type::updateOrCreate(["descripcion" => "Interes fijo"], ["renglon" => "amortizacion"]);
        \App\Type::updateOrCreate(["descripcion" => "Capital al final"], ["renglon" => "amortizacion"]);

        \App\Type::updateOrCreate(["descripcion" => "Diario"], ["renglon" => "plazo"]);
        \App\Type::updateOrCreate(["descripcion" => "Semanal"], ["renglon" => "plazo"]);
        \App\Type::updateOrCreate(["descripcion" => "Bisemanal"], ["renglon" => "plazo"]);
        \App\Type::updateOrCreate(["descripcion" => "Quincenal"], ["renglon" => "plazo"]);
        \App\Type::updateOrCreate(["descripcion" => "15 y fin de mes"], ["renglon" => "plazo"]);
        \App\Type::updateOrCreate(["descripcion" => "Mensual"], ["renglon" => "plazo"]);
        \App\Type::updateOrCreate(["descripcion" => "Anual"], ["renglon" => "plazo"]);
        \App\Type::updateOrCreate(["descripcion" => "Semestral"], ["renglon" => "plazo"]);
        \App\Type::updateOrCreate(["descripcion" => "Trimestral"], ["renglon" => "plazo"]);
        \App\Type::updateOrCreate(["descripcion" => "Ult. dia del mes"], ["renglon" => "plazo"]);

        \App\Type::updateOrCreate(["descripcion" => "Gastos de cierre"], ["renglon" => "gastoPrestamo"]);
        \App\Type::updateOrCreate(["descripcion" => "Tasacion"], ["renglon" => "gastoPrestamo"]);
        \App\Type::updateOrCreate(["descripcion" => "Cargos por seguro"], ["renglon" => "gastoPrestamo"]);
        \App\Type::updateOrCreate(["descripcion" => "Otros gastos de cierre"], ["renglon" => "gastoPrestamo"]);
        \App\Type::updateOrCreate(["descripcion" => "Gastos del gps"], ["renglon" => "gastoPrestamo"]);

        \App\Type::updateOrCreate(["descripcion" => "Efectivo"], ["renglon" => "desembolso"]);
        \App\Type::updateOrCreate(["descripcion" => "Cheque"], ["renglon" => "desembolso"]);
        \App\Type::updateOrCreate(["descripcion" => "Transferencia"], ["renglon" => "desembolso"]);
        \App\Type::updateOrCreate(["descripcion" => "Efectivo en ruta"], ["renglon" => "desembolso"]);

        \App\Type::updateOrCreate(["descripcion" => "Vehiculo"], ["renglon" => "garantia"]);
        \App\Type::updateOrCreate(["descripcion" => "Infraestructura"], ["renglon" => "garantia"]);
        \App\Type::updateOrCreate(["descripcion" => "Joyeria"], ["renglon" => "garantia"]);
        \App\Type::updateOrCreate(["descripcion" => "Electrodomestico"], ["renglon" => "garantia"]);
        \App\Type::updateOrCreate(["descripcion" => "Inmueble"], ["renglon" => "garantia"]);
        \App\Type::updateOrCreate(["descripcion" => "Telefono"], ["renglon" => "garantia"]);
        \App\Type::updateOrCreate(["descripcion" => "Otros"], ["renglon" => "garantia"]);

        \App\Type::updateOrCreate(["descripcion" => "Nuevo"], ["renglon" => "condicionGarantia"]);
        \App\Type::updateOrCreate(["descripcion" => "Usado"], ["renglon" => "condicionGarantia"]);

        \App\Type::updateOrCreate(["descripcion" => "Sedan"], ["renglon" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["descripcion" => "Compacto"], ["renglon" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["descripcion" => "Jeepeta"], ["renglon" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["descripcion" => "Camioneta"], ["renglon" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["descripcion" => "Coupe/Sport"], ["renglon" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["descripcion" => "Camion"], ["renglon" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["descripcion" => "Motor"], ["renglon" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["descripcion" => "Otros"], ["renglon" => "tipoVehiculo"]);

        \App\Type::updateOrCreate(["descripcion" => "Capital pendiente"], ["renglon" => "mora"]);
        \App\Type::updateOrCreate(["descripcion" => "Cuota vencida"], ["renglon" => "mora"]);
        \App\Type::updateOrCreate(["descripcion" => "Capital vencido"], ["renglon" => "mora"]);

        \App\Type::updateOrCreate(["descripcion" => "Balance inicial"], ["renglon" => "transaccion"]);
        \App\Type::updateOrCreate(["descripcion" => "Pago"], ["renglon" => "transaccion"]);
        \App\Type::updateOrCreate(["descripcion" => "Anulación pago"], ["renglon" => "transaccion"]);
        \App\Type::updateOrCreate(["descripcion" => "Préstamo"], ["renglon" => "transaccion"]);
        \App\Type::updateOrCreate(["descripcion" => "Cancelación préstamo"], ["renglon" => "transaccion"]);
        \App\Type::updateOrCreate(["descripcion" => "Ajuste capital"], ["renglon" => "transaccion"]);
        \App\Type::updateOrCreate(["descripcion" => "Anulación ajuste capital"], ["renglon" => "transaccion"]);
        \App\Type::updateOrCreate(["descripcion" => "Ajuste caja"], ["renglon" => "transaccion"]);
        \App\Type::updateOrCreate(["descripcion" => "Gasto"], ["renglon" => "transaccion"]);
        \App\Type::updateOrCreate(["descripcion" => "Anulación caja"], ["renglon" => "transaccion"]);
        \App\Type::updateOrCreate(["descripcion" => "Transferencia entre cajas"], ["renglon" => "transaccion"]);

        \App\Type::updateOrCreate(["descripcion" => "Empleado"], ["renglon" => "situacionLaboral"]);
        \App\Type::updateOrCreate(["descripcion" => "Desempleado"], ["renglon" => "situacionLaboral"]);
        \App\Type::updateOrCreate(["descripcion" => "Estudiante"], ["renglon" => "situacionLaboral"]);
        \App\Type::updateOrCreate(["descripcion" => "Independiente"], ["renglon" => "situacionLaboral"]);
        \App\Type::updateOrCreate(["descripcion" => "Negocio propio"], ["renglon" => "situacionLaboral"]);
        \App\Type::updateOrCreate(["descripcion" => "Pensionado"], ["renglon" => "situacionLaboral"]);
        \App\Type::updateOrCreate(["descripcion" => "Otros"], ["renglon" => "situacionLaboral"]);

        \App\Type::updateOrCreate(["descripcion" => "Disminuir valor cuota"], ["renglon" => "abonoCapital"]);
        \App\Type::updateOrCreate(["descripcion" => "Disminuir plazo"], ["renglon" => "abonoCapital"]);

        \App\Type::updateOrCreate(["descripcion" => "Ingresos"], ["renglon" => "contabilidad"]);
        \App\Type::updateOrCreate(["descripcion" => "Egresos"], ["renglon" => "contabilidad"]);
    }
}
