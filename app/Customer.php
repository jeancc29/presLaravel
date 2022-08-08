<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        "id", "foto", "nombres", "apellidos", "apodo", "idNacionalidad",
        "fechaNacimiento", "numeroDependientes",
        "sexo", "estadoCivil", "estado", "idContacto",
        "idDireccion", "idDocumento", "tipoVivienda", "tiempoEnVivienda", "referidoPor",
        "idTrabajo", "idNegocio", "idEmpresa", "idTipoSituacionLaboral", "idRuta",
        "idTipoSexo", "idTipoEstadoCivil", "idTipoVivienda"
    ];

    public function documento()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Document', 'id', 'idDocumento');
    }

    public function contacto()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Contact', 'id', 'idContacto');
    }

    public function trabajo()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Job', 'id', 'idTrabajo');
    }

    public static function customAll($idEmpresa, $idCliente = null, $arrayOfLimit = array(0, 50)){
        $limit = implode(", ", $arrayOfLimit);

        $data = \DB::select("
            SELECT
                c.id,
                c.foto AS foto,
                c.nombres,
                c.apellidos,
                c.apodo,
                c.numeroDependientes,
                c.fechaNacimiento,
                c.sexo,
                c.estadoCivil,
                c.estado,
                (SELECT SUM(loans.capitalPendiente) FROM loans WHERE loans.status = 1 AND loans.idCliente = c.id) AS capitalPendiente,
                (SELECT JSON_OBJECT('id', co.id, 'telefono', co.telefono, 'extension', co.extension, 'celular', co.celular, 'correo', co.correo, 'fax', co.fax, 'facebook', co.facebook, 'instagram', co.instagram)) AS contacto,
                (SELECT
                    JSON_OBJECT(
                        'id', d.id,
                        'descripcion', d.descripcion,
                        'idTipo', d.idTipo,
                        'tipo', (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = d.idTipo)
                    )
                ) AS documento,
                c.idEmpresa
            FROM customers c
            INNER JOIN types ts ON c.idTipoSituacionLaboral = ts.id
            INNER JOIN nationalities n ON n.id = c.idNacionalidad
            LEFT JOIN addresses a ON a.id = c.idDireccion
            LEFT JOIN contacts co ON co.id = c.idContacto
            LEFT JOIN documents d ON d.id = c.idDocumento
            LEFT JOIN jobs j ON j.id = c.idTrabajo
            LEFT JOIN businesses b ON b.id = c.idNegocio
            WHERE c.estado = 1 AND c.idEmpresa = $idEmpresa
            LIMIT $limit
        ");

        return $data;
    }
    public static function customFirst($idCliente){
        if($idCliente == null)
            return null;

        $data = \DB::select("
            SELECT
                c.id,
                c.foto AS foto,
                c.nombres,
                c.apellidos,
                c.apodo,
                c.numeroDependientes,
                c.fechaNacimiento,
                c.sexo,
                c.estadoCivil,
                c.estado,
                (SELECT JSON_OBJECT('id', n.id, 'descripcion', n.descripcion)) AS nacionalidad,
                (SELECT JSON_OBJECT(
                    'id', a.id,
                    'direccion', a.direccion,
                    'sector', a.sector,
                    'estado', (SELECT JSON_OBJECT('id', states.id, 'nombre', states.nombre) FROM states WHERE states.id = a.idEstado),
                    'ciudad', (SELECT JSON_OBJECT('id', cities.id, 'nombre', cities.nombre) FROM cities WHERE cities.id = a.idCiudad)
                )) AS direccion,
                (SELECT JSON_OBJECT('id', co.id, 'telefono', co.telefono, 'extension', co.extension, 'celular', co.celular, 'correo', co.correo, 'fax', co.fax, 'facebook', co.facebook, 'instagram', co.instagram)) AS contacto,
                (SELECT
                    JSON_OBJECT(
                        'id', d.id,
                        'descripcion', d.descripcion,
                        'idTipo', d.idTipo,
                        'tipo', (SELECT JSON_OBJECT('id', types.id, 'descripcion', types.descripcion) FROM types WHERE types.id = d.idTipo)
                    )
                ) AS documento,
                (SELECT
                    IF(
                        j.id IS NULL,
                        NULL,
                        JSON_OBJECT(
                            'id', j.id,
                            'nombre', j.nombre,
                            'ocupacion', j.ocupacion,
                            'ingresos', j.ingresos,
                            'otrosIngresos', j.otrosIngresos,
                            'fechaIngreso', j.fechaIngreso,
                            'contacto', (SELECT JSON_OBJECT('id', contacts.id, 'telefono', contacts.telefono, 'extension', contacts.extension, 'celular', contacts.celular, 'correo', contacts.correo, 'fax', contacts.fax, 'facebook', contacts.facebook, 'instagram', contacts.instagram) FROM contacts WHERE contacts.id = j.idContacto),
                            'direccion', (SELECT
                                JSON_OBJECT(
                                'id', addresses.id,
                                'direccion', addresses.direccion,
                                'sector', addresses.sector,
                                'estado', (SELECT JSON_OBJECT('id', states.id, 'nombre', states.nombre) FROM states WHERE states.id = addresses.idEstado),
                                'ciudad', (SELECT JSON_OBJECT('id', cities.id, 'nombre', cities.nombre) FROM cities WHERE cities.id = addresses.idCiudad)
                            ) FROM addresses WHERE addresses.id = j.idDireccion)
                        )
                    )
                ) AS trabajo,
                (SELECT
                    IF(
                        b.id IS NULL,
                        NULL,
                        JSON_OBJECT(
                            'id', b.id,
                            'nombre', b.nombre,
                            'tipo', b.tipo,
                            'tiempoExistencia', b.tiempoExistencia,
                            'direccion', (SELECT
                                JSON_OBJECT(
                                'id', addresses.id,
                                'direccion', addresses.direccion,
                                'sector', addresses.sector,
                                'estado', (SELECT JSON_OBJECT('id', states.id, 'nombre', states.nombre) FROM states WHERE states.id = addresses.idEstado),
                                'ciudad', (SELECT JSON_OBJECT('id', cities.id, 'nombre', cities.nombre) FROM cities WHERE cities.id = addresses.idCiudad)
                            ) FROM addresses WHERE addresses.id = b.idDireccion)
                        )
                    )
                ) AS negocio,
                (
                    SELECT
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'id', r.id,
                                'nombre', r.nombre,
                                'tipo', r.tipo,
                                'parentesco', r.parentesco
                            )
                        )
                    FROM prestamo.references as r
                    WHERE r.idCliente = c.id
                ) AS referencias,
                (SELECT JSON_OBJECT('id', ts.id, 'descripcion', ts.descripcion)) AS tipoSituacionLaboral,
                c.idEmpresa,
                (SELECT IF(r.id IS NULL, null, JSON_OBJECT('id', r.id, 'descripcion', r.descripcion))) AS ruta,
                c.idRuta
            FROM customers c
            INNER JOIN types ts ON c.idTipoSituacionLaboral = ts.id
            INNER JOIN nationalities n ON n.id = c.idNacionalidad
            LEFT JOIN addresses a ON a.id = c.idDireccion
            LEFT JOIN contacts co ON co.id = c.idContacto
            LEFT JOIN documents d ON d.id = c.idDocumento
            LEFT JOIN jobs j ON j.id = c.idTrabajo
            LEFT JOIN businesses b ON b.id = c.idNegocio
            LEFT JOIN routes r ON r.id = c.idRuta
            WHERE c.estado = 1 AND c.id = $idCliente
        ");

        // return $data;
        return count($data) > 0 ? $data[0] : null;
    }
}
