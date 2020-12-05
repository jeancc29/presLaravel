<?php

use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ID_COUNTRY_REPUBLICA_DOMINICANA = 138;
        $country = \App\Country::whereName("Republica Dominicana")->first();

        // $states = $this->getStatesJsonFile();
        $states = json_decode(\App\Classes\JsonCountries::getStates());
        foreach ($states->states as $state) {
            if($state->id_country == $ID_COUNTRY_REPUBLICA_DOMINICANA){
                $name = $state->name;
                $id = $state->id;

                $mystate = \App\State::updateOrCreate([
                    "name" => $state->name,
                    "idCountry" => $country->id
                ]);

                $this->insertCities($state->id, $mystate->id);
            }
            
        }

        
        
    }

    public function insertCities($idStateOfJsonFile, $idStateDB){
        foreach (json_decode(\App\Classes\JsonCountries::getCities())->cities as $city) {
            if($city->id_state == $idStateOfJsonFile){
                $name = $city->name;
                $id = $city->id;

                \App\City::updateOrCreate([
                    "name" => $city->name,
                    "idState" => $idStateDB
                ]);
            }
        }
    }

    public function getStatesJsonFile(){
        $path = public_path();
        if($contains = Str::contains($path, '\\'))
            $path .= "\assets\countriesjson\states.json";
        else
            $path .= "/assets/countriesjson/states.json";

        $file = file_get_contents($path, true);
        $file = $this->removeLineBreak($file);
        $file = $this->removeTab($file);
        return json_decode($file);
    }

    public function removeLineBreak($string){
        return str_replace("\n", "", $string);
    }
    public function removeTab($string){
        return str_replace("\t", "", $string);
    }
    public function quitarPrimeraComilla($string){
        return Str::replaceFirst('\"', '', $string);
    }

    public function getStateJson(){
        
            
    }
}




