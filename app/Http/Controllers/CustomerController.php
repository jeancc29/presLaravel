<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response; 
use Intervention\Image\Facades\Image;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datos = request()->validate([
            'data.id' => '',
            'data.nombres' => '',
            'data.usuario' => '',
            'data.apiKey' => '',
            'data.idEmpresa' => '',
        ])["data"];

        // return Response::json([
        //     "message" => $data["apiKey"]
        // ], 404);

        \App\Classes\Helper::validateApiKey($datos["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos, "Clientes", ["Ver"]);

        return Response::json([
            'mensaje' => '',
            'ciudades' => \App\City::cursor(),
            'estados' => \App\State::cursor(),
            'clientes' => \App\Http\Resources\CustomerSmallResource::collection(\App\Customer::where("idEmpresa", $datos["idEmpresa"])->cursor()),
        ], 201);
    }

    public function search()
    {
        $data = request()->validate([
            'datos' => '',
            'idEmpresa' => '',
        ]);

        // \App\Classes\Helper::validateApiKey($data["usuario"]["apiKey"]);
        // \App\Classes\Helper::validatePermissions($data["usuario"], "Clientes", ["Guardar"]);
        
        $searchTerm = $data["datos"];
        $clientes = Customer::
            query()
            ->where('idEmpresa', '=', $data["idEmpresa"]) 
            ->where('nombres', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('apellidos', 'LIKE', "%{$searchTerm}%")
            ->select("id", "nombres", "apellidos", "idDocumento")
            ->limit(10)
            ->get();

        return Response::json([
            'clientes' => \App\Http\Resources\CustomerUltraSmallResource::collection($clientes),
        ], 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $datos = request()->validate([
            'data.usuario' => '',
            'data.id' => '',
            'data.foto' => '',
            'data.nombres' => '',
            'data.apellidos' => '',
            'data.apodo' => '',
            'data.fechaNacimiento' => '',
            'data.numeroDependientes' => '',
            'data.sexo' => '',
            'data.estadoCivil' => '',
            'data.nacionalidad' => '',
            'data.tipoVivienda' => '',
            'data.tiempoEnVivienda' => '',
            'data.referidoPor' => '',
            'data.idDocumento' => '',
            'data.documento' => '',
            'data.idDireccion' => '',
            'data.direccion' => '',
            'data.idContacto' => '',
            'data.contacto' => '',
            'data.trabajo' => '',
            'data.negocio' => '',
            'data.referencias' => '',
            // 'abreviatura' => 'required|min:1|max:10',
            // 'estado' => 'required',
            // 'horaCierre' => 'required',
            // 'sorteos' => 'required',
            // 'loterias' => '',
    
        ])["data"];
        // $datos["foto"] = substr($datos["foto"], strpos($datos["foto"], ",")+1);

        // return Response::json([
        //     'mensaje' => 'Se ha guardado',
        //     'clientes' => "hey culooooooooooooooooooooooooooooooooooooooo"
        // ], 201);

        \App\Classes\Helper::validateApiKey($datos["usuario"]["apiKey"]);
        \App\Classes\Helper::validatePermissions($datos["usuario"], "Clientes", ["Guardar"]);


        \DB::transaction(function() use($datos)
        {
            $p = $datos["nombres"];
            $customer = Customer::whereId($datos["id"])->first();
            // return Response::json([
            //     "errores" => 0,
            //     'mensaje' => 'Se ha guardado el culooooooooooooo',
            //     'clientes' => Customer::orderBy("id", "desc")->get() 
            // ], 201);
            if($customer != null){
                $documento = \App\Document::updateOrCreate(
                    ["id" => $datos["documento"]["id"]],
                    [
                        "descripcion" => $datos["documento"]["descripcion"],
                        "idTipo" => $datos["documento"]["idTipo"],
                    ]
                );

                $direccion = \App\Address::updateOrCreate(
                    ["id" => $datos["direccion"]["id"]],
                    [
                        "direccion" => $datos["direccion"]["direccion"],
                        "sector" => $datos["direccion"]["sector"],
                        "idEstado" => $datos["direccion"]["idEstado"],
                        "idCiudad" => $datos["direccion"]["idCiudad"],
                    ]
                );

                $contacto = \App\Contact::updateOrCreate(
                    ["id" => $datos["contacto"]["id"]],
                    [
                        "telefono" => $datos["contacto"]["telefono"],
                        "celular" => $datos["contacto"]["celular"],
                        "correo" => $datos["contacto"]["correo"],
                        "facebook" => $datos["contacto"]["facebook"],
                        "instagram" => $datos["contacto"]["instagram"],
                    ],
                );

                //Trabajo
                $direccionTrabajo = \App\Address::updateOrCreate(
                    ["id" => $datos["trabajo"]["direccion"]["id"]],
                    [
                        "direccion" => $datos["trabajo"]["direccion"]["direccion"],
                        "sector" => $datos["trabajo"]["direccion"]["sector"],
                        "idEstado" => $datos["trabajo"]["direccion"]["idEstado"],
                        "idCiudad" => $datos["trabajo"]["direccion"]["idCiudad"],
                        "numero" => $datos["trabajo"]["direccion"]["numero"],
                    ]
                );

                $contactoTrabajo = \App\Contact::updateOrCreate(
                    ["id" => $datos["trabajo"]["contacto"]["id"]],
                    [
                        "telefono" => $datos["trabajo"]["contacto"]["telefono"],
                        "extension" => $datos["trabajo"]["contacto"]["extension"],
                        "celular" => $datos["trabajo"]["contacto"]["celular"],
                        "correo" => $datos["trabajo"]["contacto"]["correo"],
                        "facebook" => $datos["trabajo"]["contacto"]["facebook"],
                        "instagram" => $datos["trabajo"]["contacto"]["instagram"],
                        "fax" => $datos["trabajo"]["contacto"]["fax"],
                    ],
                );

                $trabajo = \App\Job::updateOrCreate(
                    ["id" => $datos["trabajo"]["id"]],
                    [
                        "nombre" => $datos["trabajo"]["nombre"],
                        "ocupacion" => $datos["trabajo"]["ocupacion"],
                        "ingresos" => $datos["trabajo"]["ingresos"],
                        "otrosIngresos" => $datos["trabajo"]["otrosIngresos"],
                        "fechaIngreso" => $datos["trabajo"]["fechaIngreso"],
                        "idDireccion" => $direccionTrabajo->id,
                        "idContacto" => $contactoTrabajo->id,
                    ],
                );

                //Negocio
                $direccionNegocio = \App\Address::updateOrCreate(
                    ["id" => $datos["negocio"]["direccion"]["id"]],
                    [
                        "direccion" => $datos["negocio"]["direccion"]["direccion"],
                        "idEstado" => $datos["negocio"]["direccion"]["idEstado"],
                        "idCiudad" => $datos["negocio"]["direccion"]["idCiudad"],
                    ]
                );

                $negocio = \App\Business::updateOrCreate(
                    ["id" => $datos["negocio"]["id"]],
                    [
                        "nombre" => $datos["negocio"]["nombre"],
                        "tipo" => $datos["negocio"]["tipo"],
                        "tiempoExistencia" => $datos["negocio"]["tiempoExistencia"],
                        "idDireccion" => $direccionNegocio->id,
                    ],
                );

                foreach($datos["referencias"] as $referencia){
                    \App\Reference::updateOrCreate(
                        ["id" => $referencia["id"], "idCliente" => $customer->id],
                        ["nombre" => $referencia["nombre"], "tipo" => $referencia["tipo"], "parentesco" => $referencia["parentesca"]]
                    );
                }

                //Cliente
                $customer->foto = $datos["foto"];
                $customer->nombres = $datos["nombres"];
                $customer->apellidos = $datos["apellidos"];
                $customer->apodo = $datos["apodo"];
                $customer->fechaNacimiento = $datos["fechaNacimiento"];
                $customer->numeroDependientes = $datos["numeroDependientes"];
                $customer->sexo = $datos["sexo"];
                $customer->estadoCivil = $datos["estadoCivil"];
                $customer->nacionalidad = $datos["nacionalidad"];
                $customer->tipoVivienda = $datos["tipoVivienda"];
                $customer->tiempoEnVivienda = $datos["tiempoEnVivienda"];
                $customer->referidoPor = $datos["referidoPor"];
                $customer->idDocumento = $documento->id;
                $customer->idDireccion = $direccion->id;
                $customer->idContacto = $contacto->id;
                $customer->idTrabajo = $trabajo->id;
                $customer->idNegocio = $negocio->id;
                $customer->save();
            }else{
                
                $documento = \App\Document::updateOrCreate(
                    ["id" => $datos["documento"]["id"]],
                    [
                        "descripcion" => $datos["documento"]["descripcion"],
                        "idTipo" => $datos["documento"]["idTipo"],
                    ]
                );

                

                $direccion = \App\Address::updateOrCreate(
                    ["id" => $datos["direccion"]["id"]],
                    [
                        "direccion" => $datos["direccion"]["direccion"],
                        "sector" => $datos["direccion"]["sector"],
                        "idEstado" => $datos["direccion"]["idEstado"],
                        "idCiudad" => $datos["direccion"]["idCiudad"],
                    ]
                );

                $contacto = \App\Contact::updateOrCreate(
                    ["id" => $datos["contacto"]["id"]],
                    [
                        "telefono" => $datos["contacto"]["telefono"],
                        "celular" => $datos["contacto"]["celular"],
                        "correo" => $datos["contacto"]["correo"],
                        "facebook" => $datos["contacto"]["facebook"],
                        "instagram" => $datos["contacto"]["instagram"],
                    ],
                );

                //Trabajo
                $direccionTrabajo = \App\Address::updateOrCreate(
                    ["id" => $datos["trabajo"]["direccion"]["id"]],
                    [
                        "direccion" => $datos["trabajo"]["direccion"]["direccion"],
                        "sector" => $datos["trabajo"]["direccion"]["sector"],
                        "idEstado" => $datos["trabajo"]["direccion"]["idEstado"],
                        "idCiudad" => $datos["trabajo"]["direccion"]["idCiudad"],
                        "numero" => $datos["trabajo"]["direccion"]["numero"],
                    ]
                );

                $contactoTrabajo = \App\Contact::updateOrCreate(
                    ["id" => $datos["trabajo"]["contacto"]["id"]],
                    [
                        "telefono" => $datos["trabajo"]["contacto"]["telefono"],
                        "extension" => $datos["trabajo"]["contacto"]["extension"],
                        "celular" => $datos["trabajo"]["contacto"]["celular"],
                        "correo" => $datos["trabajo"]["contacto"]["correo"],
                        "facebook" => $datos["trabajo"]["contacto"]["facebook"],
                        "instagram" => $datos["trabajo"]["contacto"]["instagram"],
                        "fax" => $datos["trabajo"]["contacto"]["fax"],
                    ],
                );

                $trabajo = \App\Job::updateOrCreate(
                    ["id" => $datos["trabajo"]["id"]],
                    [
                        "nombre" => $datos["trabajo"]["nombre"],
                        "ocupacion" => $datos["trabajo"]["ocupacion"],
                        "ingresos" => $datos["trabajo"]["ingresos"],
                        "otrosIngresos" => $datos["trabajo"]["otrosIngresos"],
                        "fechaIngreso" => $datos["trabajo"]["fechaIngreso"],
                        "idDireccion" => $direccionTrabajo->id,
                        "idContacto" => $contactoTrabajo->id,
                    ],
                );

                //Negocio
                $direccionNegocio = \App\Address::updateOrCreate(
                    ["id" => $datos["negocio"]["direccion"]["id"]],
                    [
                        "direccion" => $datos["negocio"]["direccion"]["direccion"],
                        "idEstado" => $datos["negocio"]["direccion"]["idEstado"],
                        "idCiudad" => $datos["negocio"]["direccion"]["idCiudad"],
                    ]
                );

                $negocio = \App\Business::updateOrCreate(
                    ["id" => $datos["negocio"]["id"]],
                    [
                        "nombre" => $datos["negocio"]["nombre"],
                        "tipo" => $datos["negocio"]["tipo"],
                        "tiempoExistencia" => $datos["negocio"]["tiempoExistencia"],
                        "idDireccion" => $direccionNegocio->id,
                    ],
                );

                //Cliente
                $fotoPerfil = null;
                if(isset($datos["foto"]))
                    $fotoPerfil = $this->guardarFoto($datos["foto"], $documento->descripcion);
                
                $customer = Customer::create([
                    "foto" => $fotoPerfil,
                    "nombres" => $datos["nombres"],
                    "apellidos" => $datos["apellidos"],
                    "apodo" => $datos["apodo"],
                    "fechaNacimiento" => $datos["fechaNacimiento"],
                    "numeroDependientes" => $datos["numeroDependientes"],
                    "sexo" => $datos["sexo"],
                    "estadoCivil" => $datos["estadoCivil"],
                    "nacionalidad" => $datos["nacionalidad"],
                    "tipoVivienda" => $datos["tipoVivienda"],
                    "tiempoEnVivienda" => $datos["tiempoEnVivienda"],
                    "referidoPor" => $datos["referidoPor"],
                    "idEmpresa" => $datos["usuario"]["idEmpresa"],
                    "idContacto" => $contacto->id,
                    "idDireccion" => $direccion->id,
                    "idTrabajo" => $trabajo->id,
                    "idNegocio" => $negocio->id,
                    "idDocumento" => $documento->id
                ]);

                foreach($datos["referencias"] as $referencia){
                    \App\Reference::updateOrCreate(
                        ["id" => $referencia["id"], "idCliente" => $customer->id],
                        ["nombre" => $referencia["nombre"], "tipo" => $referencia["tipo"], "parentesco" => $referencia["parentesco"]]
                    );
                }

                $datos["id"] = $customer->id;
            }
        });

        return Response::json([
            'mensaje' => 'Se ha guardado',
            'id' => Customer::latest('id')->first()->id
            // 'clientes' => Customer::orderBy("id", "desc")->cursor() 
        ], 201);
    }

    // public function guardarFoto($base64, $documento){
    //     $file = $base64;
    //     $safeName = base64_decode($documento) . \Str::random(12).'.'.'png';
    //     $folderName = \App\Classes\Helper::path();
    //     // $destinationPath = public_path() . $folderName;
    //     Image::make(file_get_contents($base64))->save($folderName.$safeName); 
    //     // $success = file_put_contents($folderName.$safeName, $file);
    //     return $safeName;
    // }

    
    public function guardarFoto($base64Image, $documento){
        $realImage = base64_decode($base64Image);
        $safeName = $documento . time() .'.'.'png';
        $path = \App\Classes\Helper::path() . $safeName;
        $success = file_put_contents($path, $realImage);
        return $safeName;
    }

    public function guardarFotovIEJO($base64, $documento){
        $file = $base64;
        $safeName = base64_decode($documento) . \Str::random(12).'.'.'png';
        $folderName = \App\Classes\Helper::path();
        // $destinationPath = public_path() . $folderName;
        $success = file_put_contents($folderName.$safeName, $file);
        return $safeName;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
