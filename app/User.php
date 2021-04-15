<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombres', 'apellidos', 'correo', 'telefono', 'idContacto', 'idRol', 'idSucursal', 'idEmpresa', 'password', 'usuario', 'foto', 'status', 'idEmpresa', 'idRuta'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tienePermiso($entidad, $permiso){
        $entidad = \App\Entity::whereDescripcion($entidad)->first();

        if($entidad == null)
            return false;

        if($this->permisos()->where(["descripcion" => $permiso, "idEntidad" => $entidad->id])->first() != null && $this->status == 1)
            return true;
        else
            return false;
    }

    public function cajas()
    {
        return $this->belongsToMany('App\Box', 'box_user', 'idUsuario', 'idCaja')->withPivot('created_at');
    }

    public function contacto()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Contact', 'id', 'idContacto');
    }
    public function rol()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Role', 'id', 'idRol');
    }
    public function sucursal()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Branchoffice', 'id', 'idSucursal');
    }
    public function empresa()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Company', 'id', 'idEmpresa');
    }
    public function ruta()
    {
        //Modelo, foreign key, local key
        return $this->hasOne('App\Route', 'id', 'idRuta');
    }

    public function permisos(){
        return $this->belongsToMany("App\Permission", "permission_user", "idUsuario", "idPermiso")->withPivot("created_at");
    }

    public static function customAll($idEmpresa, $idRol = null){
        $consultaRol = ($idRol != null) ? " AND u.idRol = $idRol" : "";
        return \DB::select("
            SELECT
            u.id,
            u.nombres,
            u.apellidos,
            (SELECT IF(r.id IS NULL, NULL, JSON_OBJECT('id', r.id, 'descripcion', r.descripcion))) AS rol,
            (SELECT IF(rt.id IS NULL, NULL, JSON_OBJECT('id', rt.id, 'descripcion', rt.descripcion))) AS ruta
            FROM users u
            LEFT JOIN roles r on r.id = u.idRol
            LEFT JOIN routes rt on rt.id = u.idRuta
            WHERE u.idEmpresa = $idEmpresa
            $consultaRol
        ");
    }

    public static function customCajas($usuario){
        $cajas = \DB::select("
            SELECT
                b.*
            FROM box_user bu
            INNER JOIN boxes b ON b.id = bu.idCaja
            WHERE bu.idUsuario = {$usuario['id']};
        ");
        if(count($cajas) == 0)
            $cajas = \App\Box::where("idEmpresa", $usuario["idEmpresa"])->get();

        return $cajas;
    }
}
