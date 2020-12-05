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
        \App\Type::updateOrCreate(["description" => "Cedula identidad"], ["category" => "documento"]);
        \App\Type::updateOrCreate(["description" => "RNC"], ["category" => "documento"]);
        \App\Type::updateOrCreate(["description" => "Pasaporte"], ["category" => "documento"]);
        \App\Type::updateOrCreate(["description" => "Ninguna"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Combustible"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Gastos Diversos"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Nómina"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Comisión Agente"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Aportaciones"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Automóvil"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Renta"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Sistema"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Pagina Web"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Imprestos"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Seguro Social"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Comisiones Bancarias"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Misceláneo"], ["category" => "gasto"]);
        \App\Type::updateOrCreate(["description" => "Almuerzo Administrativo"], ["category" => "gasto"]);

        \App\Type::updateOrCreate(["description" => "Cuota fija"], ["category" => "amortizacion"]);
        \App\Type::updateOrCreate(["description" => "Disminuir cuota"], ["category" => "amortizacion"]);
        \App\Type::updateOrCreate(["description" => "Interes fijo"], ["category" => "amortizacion"]);
        \App\Type::updateOrCreate(["description" => "Capital al final"], ["category" => "amortizacion"]);

        \App\Type::updateOrCreate(["description" => "Diario"], ["category" => "plazo"]);
        \App\Type::updateOrCreate(["description" => "Semanal"], ["category" => "plazo"]);
        \App\Type::updateOrCreate(["description" => "Bisemanal"], ["category" => "plazo"]);
        \App\Type::updateOrCreate(["description" => "Quincenal"], ["category" => "plazo"]);
        \App\Type::updateOrCreate(["description" => "15 y fin de mes"], ["category" => "plazo"]);
        \App\Type::updateOrCreate(["description" => "Mensual"], ["category" => "plazo"]);
        \App\Type::updateOrCreate(["description" => "Anual"], ["category" => "plazo"]);
        \App\Type::updateOrCreate(["description" => "Semestral"], ["category" => "plazo"]);
        \App\Type::updateOrCreate(["description" => "Trimestral"], ["category" => "plazo"]);
        \App\Type::updateOrCreate(["description" => "Ult. dia del mes"], ["category" => "plazo"]);

        \App\Type::updateOrCreate(["description" => "Gastos de cierre"], ["category" => "gastoPrestamo"]);
        \App\Type::updateOrCreate(["description" => "Tasacion"], ["category" => "gastoPrestamo"]);
        \App\Type::updateOrCreate(["description" => "Cargos por seguro"], ["category" => "gastoPrestamo"]);
        \App\Type::updateOrCreate(["description" => "Otros gastos de cierre"], ["category" => "gastoPrestamo"]);
        \App\Type::updateOrCreate(["description" => "Gastos del gps"], ["category" => "gastoPrestamo"]);

        \App\Type::updateOrCreate(["description" => "Efectivo"], ["category" => "desembolso"]);
        \App\Type::updateOrCreate(["description" => "Cheque"], ["category" => "desembolso"]);
        \App\Type::updateOrCreate(["description" => "Transferencia"], ["category" => "desembolso"]);
        \App\Type::updateOrCreate(["description" => "Efectivo en ruta"], ["category" => "desembolso"]);

        \App\Type::updateOrCreate(["description" => "Vehiculo"], ["category" => "garantia"]);
        \App\Type::updateOrCreate(["description" => "Infraestructura"], ["category" => "garantia"]);
        \App\Type::updateOrCreate(["description" => "Joyeria"], ["category" => "garantia"]);
        \App\Type::updateOrCreate(["description" => "Electrodomestico"], ["category" => "garantia"]);
        \App\Type::updateOrCreate(["description" => "Inmueble"], ["category" => "garantia"]);
        \App\Type::updateOrCreate(["description" => "Telefono"], ["category" => "garantia"]);
        \App\Type::updateOrCreate(["description" => "Otros"], ["category" => "garantia"]);

        \App\Type::updateOrCreate(["description" => "Nuevo"], ["category" => "condicionGarantia"]);
        \App\Type::updateOrCreate(["description" => "Usado"], ["category" => "condicionGarantia"]);
        
        \App\Type::updateOrCreate(["description" => "Sedan"], ["category" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["description" => "Compacto"], ["category" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["description" => "Jeepeta"], ["category" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["description" => "Camioneta"], ["category" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["description" => "Coupe/Sport"], ["category" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["description" => "Camion"], ["category" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["description" => "Motor"], ["category" => "tipoVehiculo"]);
        \App\Type::updateOrCreate(["description" => "Otros"], ["category" => "tipoVehiculo"]);

    }
}
