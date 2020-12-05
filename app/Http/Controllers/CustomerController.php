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
        return Response::json([
            'message' => '',
            'cities' => \App\City::cursor(),
            'states' => \App\State::cursor(),
            'customers' => \App\Http\Resources\CustomerSmallResource::collection(\App\Customer::cursor()),
        ], 201);
    }

    public function search()
    {
        $data = request()->validate([
            'data' => '',
        ]);
        
        $searchTerm = $data["data"];
        $customers = Customer::
            query()->where('names', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('surnames', 'LIKE', "%{$searchTerm}%")
            ->select("id", "names", "surnames", "idDocument")
            ->limit(10)
            ->get();

        return Response::json([
            'customers' => \App\Http\Resources\CustomerUltraSmallResource::collection($customers),
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
        $data = request()->validate([
            'data.id' => '',
            'data.photo' => '',
            'data.names' => '',
            'data.surnames' => '',
            'data.nickname' => '',
            'data.birthDate' => '',
            'data.numberDependents' => '',
            'data.gender' => '',
            'data.maritalStatus' => '',
            'data.nationality' => '',
            'data.residenceType' => '',
            'data.timeInResidence' => '',
            'data.referredBy' => '',
            'data.status' => '',
            'data.idDocument' => '',
            'data.document' => '',
            'data.idAddress' => '',
            'data.address' => '',
            'data.idContact' => '',
            'data.contact' => '',
            'data.job' => '',
            'data.business' => '',
            'data.references' => '',
            // 'abreviatura' => 'required|min:1|max:10',
            // 'estado' => 'required',
            // 'horaCierre' => 'required',
            // 'sorteos' => 'required',
            // 'loterias' => '',
    
        ])["data"];
        // $data["photo"] = substr($data["photo"], strpos($data["photo"], ",")+1);

        // return Response::json([
        //     'message' => 'Se ha guardado',
        //     'clientes' => "hey culooooooooooooooooooooooooooooooooooooooo"
        // ], 201);

        \DB::transaction(function() use($data)
        {
            $p = $data["names"];
            $customer = Customer::whereId($data["id"])->first();
            // return Response::json([
            //     "error" => 0,
            //     'message' => 'Se ha guardado el culooooooooooooo',
            //     'clientes' => Customer::orderBy("id", "desc")->get() 
            // ], 201);
            if($customer != null){
                $document = \App\Document::updateOrCreate(
                    ["id" => $data["document"]["id"]],
                    [
                        "description" => $data["document"]["description"],
                        "idType" => $data["document"]["idType"],
                    ]
                );

                $address = \App\Address::updateOrCreate(
                    ["id" => $data["address"]["id"]],
                    [
                        "address" => $data["address"]["address"],
                        "sector" => $data["address"]["sector"],
                        "idState" => $data["address"]["idState"],
                        "idCity" => $data["address"]["idCity"],
                    ]
                );

                $contact = \App\Contact::updateOrCreate(
                    ["id" => $data["contact"]["id"]],
                    [
                        "phone" => $data["contact"]["phone"],
                        "mobile" => $data["contact"]["mobile"],
                        "email" => $data["contact"]["email"],
                        "facebook" => $data["contact"]["facebook"],
                        "instagram" => $data["contact"]["instagram"],
                    ],
                );

                //job
                $addressjob = \App\Address::updateOrCreate(
                    ["id" => $data["job"]["address"]["id"]],
                    [
                        "address" => $data["job"]["address"]["address"],
                        "sector" => $data["job"]["address"]["sector"],
                        "idState" => $data["job"]["address"]["idState"],
                        "idCity" => $data["job"]["address"]["idCity"],
                        "number" => $data["job"]["address"]["number"],
                    ]
                );

                $contactjob = \App\Contact::updateOrCreate(
                    ["id" => $data["job"]["contact"]["id"]],
                    [
                        "phone" => $data["job"]["contact"]["phone"],
                        "extension" => $data["job"]["contact"]["extension"],
                        "mobile" => $data["job"]["contact"]["mobile"],
                        "email" => $data["job"]["contact"]["email"],
                        "facebook" => $data["job"]["contact"]["facebook"],
                        "instagram" => $data["job"]["contact"]["instagram"],
                        "fax" => $data["job"]["contact"]["fax"],
                    ],
                );

                $job = \App\Job::updateOrCreate(
                    ["id" => $data["job"]["id"]],
                    [
                        "name" => $data["job"]["name"],
                        "occupation" => $data["job"]["occupation"],
                        "income" => $data["job"]["income"],
                        "otherIncome" => $data["job"]["otherIncome"],
                        "admissionDate" => $data["job"]["admissionDate"],
                        "idAddress" => $addressjob->id,
                        "idContact" => $contactjob->id,
                    ],
                );

                //business
                $addressbusiness = \App\Address::updateOrCreate(
                    ["id" => $data["business"]["address"]["id"]],
                    [
                        "address" => $data["business"]["address"]["address"],
                        "idState" => $data["business"]["address"]["idState"],
                        "idCity" => $data["business"]["address"]["idCity"],
                    ]
                );

                $business = \App\Business::updateOrCreate(
                    ["id" => $data["business"]["id"]],
                    [
                        "name" => $data["business"]["name"],
                        "type" => $data["business"]["type"],
                        "existenceTime" => $data["business"]["existenceTime"],
                        "idAddress" => $addressbusiness->id,
                    ],
                );

                foreach($data["references"] as $referencia){
                    \App\Reference::updateOrCreate(
                        ["id" => $referencia["id"], "idCustomer" => $customer->id],
                        ["name" => $referencia["name"], "type" => $referencia["type"], "relationship" => $referencia["relationship"]]
                    );
                }

                //Cliente
                $customer->photo = $data["photo"];
                $customer->names = $data["names"];
                $customer->surnames = $data["surnames"];
                $customer->nickname = $data["nickname"];
                $customer->birthDate = $data["birthDate"];
                $customer->numberDependents = $data["numberDependents"];
                $customer->gender = $data["gender"];
                $customer->maritalStatus = $data["maritalStatus"];
                $customer->nationality = $data["nationality"];
                $customer->residenceType = $data["residenceType"];
                $customer->timeInResidence = $data["timeInResidence"];
                $customer->referredBy = $data["referredBy"];
                $customer->idDocument = $document->id;
                $customer->idAddress = $address->id;
                $customer->idContact = $contact->id;
                $customer->idJob = $job->id;
                $customer->idBusiness = $business->id;
                $customer->save();
            }else{
                
                $document = \App\Document::updateOrCreate(
                    ["id" => $data["document"]["id"]],
                    [
                        "description" => $data["document"]["description"],
                        "idType" => $data["document"]["idType"],
                    ]
                );

                

                $address = \App\Address::updateOrCreate(
                    ["id" => $data["address"]["id"]],
                    [
                        "address" => $data["address"]["address"],
                        "sector" => $data["address"]["sector"],
                        "idState" => $data["address"]["idState"],
                        "idCity" => $data["address"]["idCity"],
                    ]
                );

                $contact = \App\Contact::updateOrCreate(
                    ["id" => $data["contact"]["id"]],
                    [
                        "phone" => $data["contact"]["phone"],
                        "mobile" => $data["contact"]["mobile"],
                        "email" => $data["contact"]["email"],
                        "facebook" => $data["contact"]["facebook"],
                        "instagram" => $data["contact"]["instagram"],
                    ],
                );

                //job
                $addressjob = \App\Address::updateOrCreate(
                    ["id" => $data["job"]["address"]["id"]],
                    [
                        "address" => $data["job"]["address"]["address"],
                        "sector" => $data["job"]["address"]["sector"],
                        "idState" => $data["job"]["address"]["idState"],
                        "idCity" => $data["job"]["address"]["idCity"],
                        "number" => $data["job"]["address"]["number"],
                    ]
                );

                $contactjob = \App\Contact::updateOrCreate(
                    ["id" => $data["job"]["contact"]["id"]],
                    [
                        "phone" => $data["job"]["contact"]["phone"],
                        "extension" => $data["job"]["contact"]["extension"],
                        "mobile" => $data["job"]["contact"]["mobile"],
                        "email" => $data["job"]["contact"]["email"],
                        "facebook" => $data["job"]["contact"]["facebook"],
                        "instagram" => $data["job"]["contact"]["instagram"],
                        "fax" => $data["job"]["contact"]["fax"],
                    ],
                );

                $job = \App\Job::updateOrCreate(
                    ["id" => $data["job"]["id"]],
                    [
                        "name" => $data["job"]["name"],
                        "occupation" => $data["job"]["occupation"],
                        "income" => $data["job"]["income"],
                        "otherIncome" => $data["job"]["otherIncome"],
                        "admissionDate" => $data["job"]["admissionDate"],
                        "idAddress" => $addressjob->id,
                        "idContact" => $contactjob->id,
                    ],
                );

                //business
                $addressbusiness = \App\Address::updateOrCreate(
                    ["id" => $data["business"]["address"]["id"]],
                    [
                        "address" => $data["business"]["address"]["address"],
                        "idState" => $data["business"]["address"]["idState"],
                        "idCity" => $data["business"]["address"]["idCity"],
                    ]
                );

                $business = \App\Business::updateOrCreate(
                    ["id" => $data["business"]["id"]],
                    [
                        "name" => $data["business"]["name"],
                        "type" => $data["business"]["type"],
                        "existenceTime" => $data["business"]["existenceTime"],
                        "idAddress" => $addressbusiness->id,
                    ],
                );

                //Cliente
                $photoPerfil = null;
                if(isset($data["photo"]))
                    $photoPerfil = $this->savePhoto($data["photo"], $document->description);
                
                $customer = Customer::create([
                    "photo" => $photoPerfil,
                    "names" => $data["names"],
                    "surnames" => $data["surnames"],
                    "nickname" => $data["nickname"],
                    "birthDate" => $data["birthDate"],
                    "numberDependents" => $data["numberDependents"],
                    "gender" => $data["gender"],
                    "maritalStatus" => $data["maritalStatus"],
                    "nationality" => $data["nationality"],
                    "residenceType" => $data["residenceType"],
                    "timeInResidence" => $data["timeInResidence"],
                    "referredBy" => $data["referredBy"],
                    "idContact" => $contact->id,
                    "idAddress" => $address->id,
                    "idJob" => $job->id,
                    "idBusiness" => $business->id,
                    "idDocument" => $document->id
                ]);

                foreach($data["references"] as $referencia){
                    \App\Reference::updateOrCreate(
                        ["id" => $referencia["id"], "idCustomer" => $customer->id],
                        ["name" => $referencia["name"], "type" => $referencia["type"], "relationship" => $referencia["relationship"]]
                    );
                }

                $data["id"] = $customer->id;
            }
        });

        return Response::json([
            'message' => 'Se ha guardado',
            'id' => Customer::latest('id')->first()->id
            // 'clientes' => Customer::orderBy("id", "desc")->cursor() 
        ], 201);
    }

    // public function savePhoto($base64, $document){
    //     $file = $base64;
    //     $safeName = base64_decode($document) . \Str::random(12).'.'.'png';
    //     $folderName = \App\Classes\Helper::path();
    //     // $destinationPath = public_path() . $folderName;
    //     Image::make(file_get_contents($base64))->save($folderName.$safeName); 
    //     // $success = file_put_contents($folderName.$safeName, $file);
    //     return $safeName;
    // }

    
    public function savePhoto($base64Image, $document){
        $realImage = base64_decode($base64Image);
        $safeName = $document . time() .'.'.'png';
        $path = \App\Classes\Helper::path() . $safeName;
        $success = file_put_contents($path, $realImage);
        return $safeName;
    }

    public function guardarphotovIEJO($base64, $document){
        $file = $base64;
        $safeName = base64_decode($document) . \Str::random(12).'.'.'png';
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
