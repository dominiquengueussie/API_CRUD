<?php

namespace App\Http\Controllers;

use App\Models\cars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cars = cars::all();
        if (count($cars) <= 0) {
            return response(
                ['message' => "Aucune voiture disponible pour l'instant"],
                401
            );
        }
        return response($cars, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $carsStore = $request->validate([
            'model' => ['required', 'string', 'max:100', 'min:10'],
            'prix' => ['required', 'numeric'],
            'description' => ['required', 'max:500', 'min:5'],
           // 'image_path' => ['required', 'nimes:jpep,jpg,png', 'max:5096'],
            'user_id' => ['required', 'numeric']
        ]);

        $carsInsert = Cars::create([
            'model' => $carsStore['model'],
            'prix' => $carsStore['prix'],
            'description' => $carsStore['description'],
            'user_id' => $carsStore['user_id'],
        ]);

        return response(['message' => 'voiture ajouter avec succès!'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\cars  $cars
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cars = DB::table('cars')
            ->join('users', 'cars.user_id', '=', 'users.id')
            ->select('cars.*', 'users.name', 'users.email')
            ->where('cars.id', '=', $id)
            ->get()
            ->first();

        return $cars;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\cars  $cars
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $carsStore = $request->validate([
            'model' => ['string', 'max:100', 'min:10'],
            'prix' => ['numeric'],
            'description' => ['max:500', 'min:5'],
            'user_id' => ['numeric'],
        ]);

        $cars = Cars::find($id);
        if (!$cars) {
            return response(
                [
                    'message' => "aucune voiture de trouver pour l'instant",
                ],
                401
            );
        }
        if ($cars->user_id !== $carsStore['user_id']) {
            return response(['message' => 'Action interdite pour vous!'], 403);
        }
        $cars->update($carsStore);
        return response(['message' => 'Voiture modifié!'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\cars  $cars
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $carsStore = $request->validate([
            'user_id' => ['numeric'],
        ]);
        $cars = cars::find($id);
        if (!$cars) {
            return response([
                'message' => 'Aucun car a supprimer pour vous!',
            ],403);
        }
        if ($cars->user_id !== $carsStore['user_id']) {
            return response(['message' => "Vous n'avez auncun droit!"], 403);
        }
        $car = cars::destroy($id); // RETOURNE 1 SI LA RESSOURCE A ETE SUPPRIME ET 0 SINON
        if ($car) {
            return response(['message' => 'voiture supprime!'], 201);
        }
    }
}
