<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guarantee extends Model
{
    protected $fillable = [
        "id", 
        "idPrestamo", 
        "idTipo", 
        "descripcion", 
        "comentario", 
        "tasacion", 
        "matricula", 
        "marca", 
        "modelo", 
        "chasis", 
        "estado", 
        "placa", 
        "anoFabricacion", 
        "motorOSerie", 
        "idTipoEmision", 
        "cilindros", 
        "color", 
        "numeroPasajeros", 
        "idTipoCondicion", 
        "numeroPuertas", 
        "fuerzaMotriz", 
        "capacidadCarga", 
        "placaAnterior", 
        "fechaExpedicion", 
        "idEmpresa", 
    ];

    public static function customAll($idEmpresa){
        return \DB::select("
            SELECT
                g.id,
                g.descripcion,
                g.comentario,
                g.foto,
                g.tasacion,
                g.estado,
                (SELECT JSON_OBJECT('id', t.id, 'descripcion', t.descripcion)) AS tipo,
                (
                    SELECT
                        JSON_OBJECT(
                            'id', c.id,
                            'nombres', c.nombres,
                            'apellidos', c.apellidos,
                            'documento', (
                                SELECT 
                                JSON_OBJECT(
                                    'id', d.id, 
                                    'descripcion', d.descripcion, 
                                    'idTipo', d.idTipo,
                                    'tipo', (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = d.idTipo)
                                )
                                FROM documents AS d
                                WHERE d.id = c.idDocumento
                            )
                        )
                ) AS cliente

            FROM guarantees AS g
            INNER JOIN loans AS l ON l.id = g.idPrestamo
            INNER JOIN customers AS c ON c.id = l.idCliente
            INNER JOIN types AS t ON t.id = g.idTipo
            WHERE g.idEmpresa = $idEmpresa AND l.status != 0
        ");
    }

    public static function customFirst($id){
        $data = \DB::select("
            SELECT
                g.id,
                g.descripcion,
                g.comentario,
                g.foto,
                g.tasacion,
                g.estado,
                (SELECT JSON_OBJECT('id', t.id, 'descripcion', t.descripcion)) AS tipo,
                (
                    SELECT
                        JSON_OBJECT(
                            'id', c.id,
                            'nombres', c.nombres,
                            'apellidos', c.apellidos,
                            'documento', (
                                SELECT 
                                JSON_OBJECT(
                                    'id', d.id, 
                                    'descripcion', d.descripcion, 
                                    'idTipo', d.idTipo,
                                    'tipo', (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = d.idTipo)
                                )
                                FROM documents AS d
                                WHERE d.id = c.idDocumento
                            )
                        )
                ) AS cliente

            FROM guarantees AS g
            INNER JOIN loans AS l ON l.id = g.idPrestamo
            INNER JOIN customers AS c ON c.id = l.idCliente
            INNER JOIN types AS t ON t.id = g.idTipo
            WHERE g.id = $id AND l.status != 0
        ");

        return count($data) > 0 ? $data[0] : null;
    }
}
