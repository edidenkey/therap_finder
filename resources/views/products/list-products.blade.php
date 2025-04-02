@extends('layouts.user_type.auth')

@section('content')

<div>
    <!--<div class="alert alert-secondary mx-4" role="alert">
        <span class="text-white">
            <strong>Add, Edit, Delete features are not functional!</strong> This is a
            <strong>PRO</strong> feature! Click <strong>
            <a href="https://www.creative-tim.com/live/soft-ui-dashboard-pro-laravel" target="_blank" class="text-white">here</a></strong>
            to see the PRO product!
        </span>
    </div>-->

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Liste des produits</h5>
                        </div>
                        @if (Auth::user()->role == "therapeute")
                        <button data-bs-toggle="modal" data-bs-target="#modal-form" class="btn bg-gradient-info btn-sm mb-0" type="button">+&nbsp; Ajouter</button>
                        @endif
                        <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                        <span style="color:black;" aria-hidden="true">x</span>
                                    </button>
                                </div>
                                <div class="modal-body p-0">
                                  <div class="card card-plain">
                                    <div class="card-header pb-0 text-left">
                                      <h3 class="font-weight-bolder text-info text-gradient">Ajouter un produit</h3>
                                      <p class="mb-0">Renseignez les détails du produit</p>
                                    </div>
                                    <div class="card-body">
                                      <form enctype="multipart/form-data" action="/add-product" method="POST" role="form text-left">
                                        @csrf
                                        <div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Nom du produit</label>
                                                        <div class="input-group mb-3">
                                                            <input name="name" type="text" class="form-control" placeholder="Nom" aria-label="Nom" aria-describedby="nom-addon">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Prix du produit</label>
                                                        <div class="input-group mb-3">
                                                            <input name="prix" type="number" class="form-control" placeholder="Prix" aria-label="Prix" aria-describedby="nom-addon">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <label>Sélectionnez la catégorie du produit</label>
                                            <div class="input-group mb-3">
                                                <select name="categorie" class="form-control" >
                                                    @foreach ($categories as $categorie)
                                                        <option value="{{$categorie->id}}">{{$categorie->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label>Image du produit</label>
                                            <div class="input-group mb-3">
                                                <input name="image" accept="image/png, image/jpeg, image/jpg" type="file" class="form-control" placeholder="Image" aria-label="Image" aria-describedby="nom-addon">
                                            </div>
                                            <label>Description</label>
                                            <div class="input-group mb-3">
                                                <textarea class="form-control" id="description" rows="3" placeholder="Décrivez le produit" name="description"></textarea>
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-round bg-gradient-info btn-lg w-100 mt-4 mb-0">Ajouter</button>
                                            </div>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        ID
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Image
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Nom
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Prix
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Date de création
                                    </th>

                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ( $products as $product )
                                    <tr>
                                        <td class="ps-4">
                                            <p class="text-xs font-weight-bold mb-0">{{ $loop->iteration }}</p>
                                        </td>
                                        <td class="text-center">
                                            <img src="{{ url($product->images[0]->image->image_path) }}" class="avatar avatar-sm me-3">
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $product->name }}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $product->prix }}</p>
                                        </td>
                                        <td class="text-center">
                                            <p class="text-xs font-weight-bold mb-0">{{ $product->created_at }}</p>
                                        </td>

                                        <td class="text-center">
                                            <a href="#" class="mx-3" data-bs-toggle="tooltip" data-bs-original-title="Edit user">
                                                <i class="fas fa-user-edit text-secondary"></i>
                                            </a>
                                            <span>
                                                <i class="cursor-pointer fas fa-trash text-secondary"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
@endsection

