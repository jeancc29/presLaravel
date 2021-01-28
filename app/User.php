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
        'nombres', 'apellidos', 'correo', 'telefono', 'idContacto', 'idRol', 'idSucursal', 'idEmpresa', 'password', 'usuario', 'foto', 'status'
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

    public function permisos(){
        return $this->belongsToMany("App\Permission", "permission_user", "idUsuario", "idPermiso")->withPivot("created_at");
    }
}
