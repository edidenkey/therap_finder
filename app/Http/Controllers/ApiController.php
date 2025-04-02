<?php

namespace App\Http\Controllers;

use App\Models\AbonnementUser;
use App\Models\Client;
use App\Models\Disciplines;
use App\Models\DisciplinesUser;
use App\Models\Image;
use App\Models\InformationPay;
use App\Models\Meeting;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProfilImage;
use App\Models\Service;
use App\Models\Therapeute;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;

class ApiController extends Controller
{
    /**
     * ====================================================
     *  Checks
     * ====================================================
     */
    /** Vérifier si l'adresse email est unique */
    public function apiCheckEmailUnique()
    {
        request()->validate([
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
        ]);

        return response()->json([
            'status' => 'success',
            'data' => true
        ], 200);
    }

    /**
     * ====================================================
     *  Auth
     * ====================================================
     */
    /** Connexion de l'utilisateur */
    public function apiLogin()
    {
        $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($attributes)) {
            $user = User::where('email', $attributes['email'])
                ->with(['userable', 'images', 'abonnement.abonnement', 'info_bancaire'])
                ->first();

            $token = request()->user()->createToken('token_name');

            return response()->json([
                'status' => 'success',
                'token' => $token->plainTextToken,
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => "Identifiants incorrects."
            ], 401);
        }
    }

    /** Création de l'utilisateur(incomplète, juste pour le client actuellement) */
    public function apiRegister()
    {
        $attributes = request()->validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'street' => ['required', 'max:50'],
            'postal_code' => ['required', 'max:50'],
            'country' => ['required', 'max:50'],
            'region' => ['required', 'max:50'],
            'department' => ['required', 'max:50'],
            'phone' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', 'min:5', 'max:20'],
        ]);

        try {
            $username = $attributes['first_name'] . rand(pow(10, 8 - 1), pow(10, 8) - 1);
            $id = DB::table('clients')->insertGetId([
                'description_profil' => "",
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $user = User::create([
                'username' => Str::slug($username),
                'first_name' => $attributes['first_name'],
                'email' => $attributes['email'],
                'password' => Hash::make($attributes['password']),
                'last_name' => $attributes['last_name'],
                'street' => $attributes['street'],
                'postal_code' => $attributes['postal_code'],
                'country' => $attributes['country'],
                'region' => $attributes['region'],
                'department' => $attributes['department'],
                'role' => 'client',
                'userable_type' => 'App\Models\Client',
                'userable_id' => $id,
                'phone' => $attributes['phone'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $profil_image = ProfilImage::create([
                'type' => 'profil',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            Image::create([
                'name' => 'default',
                'image_path' => '/images/profil/default.jpg',
                'user_id' => $user->id,
                'imageable_type' => 'App\Models\ProfilImage',
                'imageable_id' => $profil_image->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $user = User::where('id', $user->id)
                ->with(['userable', 'images', 'abonnement.abonnement', 'info_bancaire'])
                ->first();
            $token = $user->createToken('token_name');

            return response()->json([
                'status' => 'success',
                'token' => $token->plainTextToken,
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /** Informations de l'utilisateur connecté */
    public function apiGetConnectedUser()
    {
        $request_user = request()->user();
        $user = User::where('id', $request_user->id)
            ->with(['userable', 'images', 'abonnement.abonnement', 'info_bancaire'])
            ->first();
        if ($user) {
            return response()->json([
                'status' => 'success',
                'data' => $user
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => "Utilisateur introuvable."
        ], 404);
    }

    /** Déconnexion de l'utilisateur */
    public function apiLogOut()
    {
        request()->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur déconnecté.'
        ], 200);
    }

    /**
     * ====================================================
     *  Users
     * ====================================================
     */
    /** Update des informations de l'utilisateur connecté */
    public function apiUpdateUser()
    {
        $request_user = request()->user();
        $attributes = request()->validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'street' => ['required', 'max:50'],
            'postal_code' => ['required', 'max:50'],
            'country' => ['required', 'max:50'],
            'region' => ['required', 'max:50'],
            'department' => ['required', 'max:50'],
            'phone' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore($request_user->id)],
        ]);

        try {
            User::where('id', $request_user->id)
                ->update([
                    'first_name' => $attributes['first_name'],
                    'email' => $attributes['email'],
                    'last_name' => $attributes['last_name'],
                    'street' => $attributes['street'],
                    'postal_code' => $attributes['postal_code'],
                    'country' => $attributes['country'],
                    'region' => $attributes['region'],
                    'department' => $attributes['department'],
                    'phone' => $attributes['phone'],
                    'updated_at' => now()
                ]);

            $user = User::where('id', $request_user->id)
                ->with(['userable', 'images', 'abonnement.abonnement', 'info_bancaire'])
                ->first();

            if ($user) {
                return response()->json([
                    'status' => 'success',
                    'data' => $user
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur introuvable.',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /** Update des informations de localisation de l'utilisateur connecté */
    public function apiUpdateUserLocation()
    {
        $request_user = request()->user();
        $attributes = request()->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
        ]);

        try {
            User::where('id', $request_user->id)
                ->update([
                    'lat' => $attributes['lat'],
                    'lon' => $attributes['lon'],
                    'updated_at' => now()
                ]);

            $user = User::where('id', $request_user->id)
                ->with(['userable', 'images', 'abonnement.abonnement', 'info_bancaire'])
                ->first();

            return response()->json([
                'status' => 'success',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /** Update le mot de passe de l'utilisateur connecté */
    public function apiUpdateUserPassword()
    {
        $request_user = request()->user()->makeVisible('password');
        $attributes = request()->validate([
            'old_password' => ['required'],
            'new_password' => ['required', 'confirmed', 'min:5', 'max:20'],
        ]);

        try {
            if (!Hash::check($attributes['old_password'], $request_user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => "L'ancien mot de passe n'est pas correct.",
                ], 401);
            }

            $request_user->password = Hash::make($attributes['new_password']);
            $request_user->save();

            $user = User::where('id', $request_user->id)
                ->with(['userable', 'images', 'abonnement.abonnement', 'info_bancaire'])
                ->first();

            if ($user) {
                return response()->json([
                    'status' => 'success',
                    'data' => $user
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur introuvable.',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /** Update de l'image de profil de l'utilisateur connecté */
    public function apiUpdateUserImageProfil()
    {
        request()->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg|max:5120'
        ]);

        try {
            $image_file = request()->file('image');
            $filename = explode('.', $image_file->getClientOriginalName())[0];
            $filename =  Str::slug($filename) . time() . '.' . $image_file->getClientOriginalExtension();
            $imagePath = '/images/profil/' . $filename;
            $destinationPath = public_path() . '/images/profil';
            $image_file->move($destinationPath, $filename);

            $request_user = request()->user();
            $image = Image::where('user_id', $request_user->id)
                ->where('imageable_type', 'App\Models\ProfilImage')
                ->first();

            if ($image) {
                if ($image->image_path != '/images/profil/default.jpg') {
                    File::delete(public_path('images/profil/' . $image->name));
                }
                $image->update(['name' => $filename, 'image_path' => $imagePath]);
            } else {
                $profil_image = ProfilImage::create([
                    'type' => 'profil',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $image = Image::create([
                    'name' => $filename,
                    'image_path' => $destinationPath,
                    'user_id' => $request_user->id,
                    'imageable_type' => 'App\Models\ProfilImage',
                    'imageable_id' => $profil_image->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => $image
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /** Delete de l'image de profil de l'utilisateur connecté */
    public function apiDeleteUserImageProfil()
    {
        $request_user = request()->user();

        try {
            $image_to_update = Image::where('user_id', $request_user->id)
                ->where('imageable_type', 'App\Models\ProfilImage')
                ->where('image_path', '!=', '/images/profil/default.jpg')
                ->first();

            if ($image_to_update) {
                File::delete(public_path('images/profil/' . $image_to_update->name));
                $image_to_update->name = 'default.jpg';
                $image_to_update->image_path = '/images/profil/default.jpg';
                $image_to_update->save();
                return response()->json([
                    'status' => 'success',
                    'data' => $image_to_update
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => "Pas de photo de profil à supprimer."
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * ====================================================
     *  Users
     * ====================================================
     */
    /** Liste des moyens de paiement pour l'utilisateur connecté */
    public function apiGetUserInformationPays()
    {
        $information_pay = InformationPay::where('user_id', request()->user()->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $information_pay
        ], 200);
    }

    /** Ajouter un moyen de paiement pour l'utilisateur connecté */
    public function apiAddUserInformationPay()
    {
        $attributes = request()->validate([
            'type' => ['required', Rule::in(['stripe', 'paypal']),],
            'value' => ['required', 'String', 'max:255'],
        ]);

        $information_pay = InformationPay::where('user_id', request()->user()->id)
            ->where('type', $attributes['type'])
            ->first();
        if ($information_pay) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ce type de moyen de paiement est déjà configuré'
            ], 400);
        }

        try {
            $information_pay = InformationPay::create([
                'type' => $attributes['type'],
                'value' => $attributes['value'],
                'user_id' => request()->user()->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $information_pay
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /** Editer un moyen de paiement pour l'utilisateur connecté */
    public function apiEditUserInformationPay($information_pay_id)
    {
        $attributes = request()->validate([
            'value' => ['required', 'String', 'max:255'],
        ]);

        try {
            $information_pay = InformationPay::find($information_pay_id);

            if ($information_pay) {
                $information_pay->value = $attributes['value'];
                $information_pay->save();

                return response()->json([
                    'status' => 'success',
                    'data' => $information_pay
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => "Ce moyen de paiement n'existe pas"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /** Supprimer un moyen de paiement pour l'utilisateur connecté */
    public function apiDeleteUserInformationPay($information_pay_id)
    {
        try {
            $information_pay = InformationPay::find($information_pay_id);

            if ($information_pay) {
                $information_pay->delete();
                return response()->json([
                    'status' => 'success',
                    'data' => $information_pay
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => "Ce moyen de paiement n'existe pas"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * ====================================================
     *  Therapeutes
     * ====================================================
     */
    /** Liste de tous les thérapeutes
     * Query params : discipline_id
     */
    public function apiListTherapeutes()
    {
        try {
            $therapeutes = [];
            if (request()->query('discipline_id')) {
                $therapeutes = Therapeute::whereRelation('disciplineUser', 'discipline_id', request()->query('discipline_id'))
                    ->with(['user.images', 'user.abonnement', 'user.info_bancaire', 'disciplineUser.discipline', 'service'])
                    ->get();
            } else {
                $therapeutes = Therapeute::with(['user.images', 'user.abonnement', 'user.info_bancaire', 'disciplineUser.discipline', 'service'])->get();
            }

            return response()->json([
                'status' => 'success',
                'data' => $therapeutes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /** Statistiques d'un thérapeute */
    public function apiTherapeuteStats($therapeute_id)
    {
        try {
            $products_count = Product::where("therapeute_id", $therapeute_id)->count();
            $services_count = Service::where("therapeute_id", $therapeute_id)->count();
            $orders_count = Order::whereRelation("product", 'therapeute_id', $therapeute_id)->count();
            $pending_meetings_count = Meeting::whereRelation("service", "therapeute_id", $therapeute_id)
                ->where("status", "en cours de validation")
                ->count();
            $user_current_abonement = AbonnementUser::whereRelation("user", "userable_id", $therapeute_id)
                ->where("statut", "actif")
                ->with(['abonnement'])
                ->first();
            $upcoming_meetings = Meeting::whereRelation("service", "therapeute_id", $therapeute_id)
                ->where("status", "acceptée")
                ->whereDate("date_meeting", ">=", Carbon::now())
                ->with(['service.therapeute.user.images', 'service.therapeute.user.abonnement', 'service.therapeute.disciplineUser.discipline', 'service.therapeute.service', 'client.user.images'])
                ->orderBy("date_meeting", "DESC")
                ->skip(0)
                ->take(5)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    "products_count" => $products_count,
                    "services_count" => $services_count,
                    "orders_count" => $orders_count,
                    "pending_meetings_count" => $pending_meetings_count,
                    "user_current_abonement" => $user_current_abonement,
                    "upcoming_meetings" => $upcoming_meetings,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /** Liste des thérapeutes proches du client
     * Query params : discipline_id
     *  */
    public function apiTherapeutesNearUser($lon, $lat)
    {
        if ($lat <= -90 || $lat >= 90) {
            return response()->json([
                'status' => 'error',
                'message' => "La latitude n'est pas valide"
            ], 400);
        }
        if ($lon <= -180 || $lat >= 180) {
            return response()->json([
                'status' => 'error',
                'message' => "La longitude n'est pas valide"
            ], 400);
        }
        try {
            $therapeutes = [];
            if (request()->query('discipline_id')) {
                $therapeutes = Therapeute::leftJoin('users', 'therapeutes.id', '=', 'users.userable_id')
                    ->select(
                        "therapeutes.*",
                        DB::raw("6371 * acos(cos(radians(" . $lat . "))
                                    * cos(radians(users.lat))
                                    * cos(radians(users.lon) - radians(" . $lon . "))
                                    + sin(radians(" . $lat . "))
                                    * sin(radians(users.lat))) AS distance")
                    )
                    ->groupBy("users.id")
                    ->where("userable_type", 'App\Models\Therapeute')
                    ->whereRelation('disciplineUser', 'discipline_id', request()->query('discipline_id'))
                    ->with(['user.images', 'user.abonnement', 'user.info_bancaire', 'disciplineUser.discipline', 'service'])
                    ->get();
            } else {
                $therapeutes = Therapeute::leftJoin('users', 'therapeutes.id', '=', 'users.userable_id')
                    ->select(
                        "therapeutes.*",
                        DB::raw("6371 * acos(cos(radians(" . $lat . "))
                                    * cos(radians(users.lat))
                                    * cos(radians(users.lon) - radians(" . $lon . "))
                                    + sin(radians(" . $lat . "))
                                    * sin(radians(users.lat))) AS distance")
                    )
                    ->groupBy("users.id")
                    ->where("userable_type", 'App\Models\Therapeute')
                    ->with(['user.images', 'user.abonnement', 'user.info_bancaire', 'disciplineUser.discipline', 'service'])
                    ->get();
            }

            return response()->json([
                'status' => 'success',
                'data' => $therapeutes,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /** Détails d'un thérapeute */
    public function apiGetTherapeute($therapeute_id)
    {
        $user = Therapeute::where('id', $therapeute_id)
            ->with(['user.images', 'user.abonnement', 'user.info_bancaire', 'disciplineUser.discipline', 'service'])
            ->first();
        if ($user) {
            return response()->json([
                'status' => 'success',
                'data' => $user
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Therapeute introuvable.',
        ], 404);
    }

    /**
     * ====================================================
     *  Categories
     * ====================================================
     */

    /** Liste de toutes les discipline */
    public function apiListDisciplines()
    {
        $disciplines = Disciplines::all();
        return response()->json([
            'status' => 'success',
            'data' => $disciplines
        ], 200);
    }

    /**
     * ====================================================
     *  Products
     * ====================================================
     */

    /** Liste de tous les produits */
    public function apiListProdutcs()
    {
        $products = Product::with(['images.image'])
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $products
        ], 200);
    }

    /** Liste des produits d'un thérapeute */
    public function apiListProductsByTherapeute($therapeute_id)
    {
        $products = Product::with(['images.image'])
            ->where('therapeute_id', $therapeute_id)
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $products
        ], 200);
    }

    /** Liste des produits par catégorie */
    public function apiListProductsByCategory($categorie_id)
    {
        $products = Product::with(['images.image'])
            ->where('categorie_id', $categorie_id)
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $products
        ], 200);
    }

    /**
     * ====================================================
     *  Clients
     * ====================================================
     */

    /** Information d'un client */
    public function apiGetClient($client_id)
    {
        $user = Client::where('id', $client_id)
            ->with(['user.images'])
            ->first();
        if ($user) {
            return response()->json([
                'status' => 'success',
                'data' => $user
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Client introuvable',
        ], 404);
    }

    /**
     * ====================================================
     *  Orders
     * ====================================================
     */

    /** Liste des commandes d'un utilisateur */
    public function apiGetUserOrder()
    {
        $request_user = request()->user();
        $orders = [];
        if ($request_user->userable_type == 'App\Models\Client') {
            $orders = Order::where('client_id', $request_user->userable_id)
                ->with(['product.images.image', 'client'])
                ->orderBy('updated_at', 'DESC')
                ->get();
        } elseif ($request_user->userable_type == 'App\Models\Therapeute') {
            $orders = Order::whereRelation('product', 'therapeute_id', $request_user->userable_id)
                ->with(['product.images.image', 'client'])
                ->orderBy('updated_at', 'DESC')
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ], 200);
    }

    /** Enregistrer des commandes et update la quantité de produits en stock*/
    public function apiSaveOrder()
    {
        $request_user = request()->user();
        $attributes = request()->validate([
            'orders' => 'required'
        ]);
        $orders = $attributes['orders'];
        if ($orders) {
            try {
                foreach ($orders as $key => $value) {
                    $product = Product::find($value["product_id"]);
                    $current_stock = $product->stock;
                    $current_qty = $value["quantity"];
                    if ($current_qty > $current_stock) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Stock insuffisant pour le produit ' . $product->id,
                        ], 400);
                    }
                }
                foreach ($orders as $key => $value) {
                    $product = Product::find($value["product_id"]);
                    $current_stock = $product->stock;
                    $current_qty = $value["quantity"];
                    $latestOrder = Order::orderBy('created_at', 'DESC')->first();
                    $code = '#' . str_pad(($latestOrder ? $latestOrder->id : 0) + 1, 8, "0", STR_PAD_LEFT);
                    Order::create([
                        'code' => $code,
                        'client_id' => $request_user->userable_id,
                        'product_id' => $value["product_id"],
                        'status' => 1,
                        'quantity' => $value["quantity"],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $product->update(['stock' => $current_stock - $current_qty]);
                }
                return response()->json([
                    'status' => 'success',
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 400);
            }
        }
        return response()->json([
            'status' => 'error'
        ], 400);

        $request_user = request()->user();
        if ($request_user->userable_type = 'App\Models\Client') {
            $orders = Order::where('client_id', $request_user->userable_id)
                ->with(['product'])
                ->get();
        } elseif ($request_user->userable_type = 'App\Models\Therapeute') {
            $orders_all = Order::with(['product', 'client'])->get();
            $orders = $orders_all->filter(function ($item) use ($request_user) {
                return $item->product->therapeute_id = $request_user->userable_id;
            })->values();
        }
    }

    /**
     * ====================================================
     *  Meetings
     * ====================================================
     */

    /** Enregistrer un rendez-vous*/
    public function apiSaveMeeting()
    {
        $attributes = request()->validate([
            'date_meeting' => 'required|date',
            'service_id' => 'required|numeric|integer',
            'client_id' => 'required|numeric|integer'
        ]);

        try {
            $latestMeeting = Meeting::orderBy('created_at', 'DESC')->first();
            $code = '#' . str_pad(($latestMeeting ? $latestMeeting->id : 0) + 1, 8, "0", STR_PAD_LEFT);
            $meeting = Meeting::create([
                'code' => $code,
                'client_id' => $attributes['client_id'],
                'status' => 'en cours de validation',
                'service_id' => $attributes['service_id'],
                'date_meeting' => $attributes['date_meeting'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $meeting = Meeting::where('id', $meeting->id)
                ->with(['service.therapeute.user.images', 'service.therapeute.service', 'client.user.images'])
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $meeting
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /** Mettre à jour un rendez-vous*/
    public function apiUpdateMeeting($meeting_id)
    {
        $attributes = request()->validate([
            'status' => 'filled|string',
            'date_meeting' => 'filled|date',
        ]);

        if (!request()->has('status') && !request()->has('date_meeting')) {
            return response()->json([
                'status' => 'error',
                'message' => "Veuillez définir le status ou la date"
            ], 400);
        }

        try {
            $meeting = Meeting::find($meeting_id);
            if ($meeting) {
                if (request()->has('status')) {
                    $status = $attributes['status'];
                    $final_status = '';
                    switch ($status) {
                        case 'acceptée':
                            $final_status = $status;
                            break;
                        case 'rejetée':
                            $final_status = $status;
                            break;
                        case 'terminée':
                            $final_status = $status;
                            break;
                        case 'annulée':
                            $final_status = $status;
                            break;
                        default:
                            $final_status = 'rejetée';
                            break;
                    }
                    $meeting->status = $final_status;
                }

                if (request()->has('date_meeting')) {
                    $meeting->date_meeting = $attributes['date_meeting'];
                }

                $meeting->save();

                $meeting = Meeting::where('id', $meeting_id)
                    ->with(['service.therapeute.user.images', 'service.therapeute.service', 'client.user.images'])
                    ->get();

                return response()->json([
                    'status' => 'success',
                    'data' => $meeting
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => "Ce meeting n'existe pas"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /** Liste des rendez-vous de l'utilisateur connecté */
    public function apiGetMeetings()
    {
        $request_user = request()->user();

        // Client
        if ($request_user->userable_type == 'App\Models\Client') {
            $meeting = Meeting::where('client_id', $request_user->userable_id)
                ->with(['service.therapeute.user.images', 'service.therapeute.user.abonnement', 'service.therapeute.disciplineUser.discipline', 'service.therapeute.service', 'client.user.images'])
                ->orderBy("date_meeting", "DESC")
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $meeting
            ], 200);
        }

        // Therapeute
        $meeting = Meeting::whereRelation("service", "therapeute_id", $request_user->userable_id)
            ->with(['service.therapeute.user.images', 'service.therapeute.user.abonnement', 'service.therapeute.disciplineUser.discipline', 'service.therapeute.service', 'client.user.images'])
            ->orderBy("date_meeting", "DESC")
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $meeting
        ], 200);
    }

    /**
     * ====================================================
     *  Services
     * ====================================================
     */

    /** Liste des services*/
    public function apiGetServices()
    {
        $services = Service::with(['therapeute.user.images', 'therapeute.user.abonnement', 'therapeute.service', 'therapeute.disciplineUser.discipline'])
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $services
        ], 200);
    }

    /** Liste des services*/
    public function apiGetServicesByTherapeute($therapeute_id)
    {
        $services = Service::with(['therapeute.user.images', 'therapeute.user.abonnement', 'therapeute.service', 'therapeute.disciplineUser.discipline'])
            ->where('therapeute_id', $therapeute_id)
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $services
        ], 200);
    }
}
