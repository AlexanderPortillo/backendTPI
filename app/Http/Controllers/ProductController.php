<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Mostramos todos los productos de la db
        return Product::all();
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
        $inputs = $request->input();

        // Transformamos la primer letra de cada palabra del product_name en mayuscula
        if (isset($inputs['product_name']) && is_string($inputs['product_name'])) {
            $inputs['product_name'] = ucwords($inputs['product_name']);
        }

        // Transformamos la primer letra de category en mayuscula
        if (isset($inputs['category']) && is_string($inputs['category'])) {
            $inputs['category'] = ucfirst($inputs['category']);
        }

        // Verificamos si ya existe un producto con el mismo product_name
        $existingProduct = Product::where('product_name', $inputs['product_name'])->first();

        if ($existingProduct) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Ya existe un producto con el mismo nombre en la base de datos.',
            ]);
        }

        // Creamos el producto si no hay duplicados
        $product = Product::create($inputs);

        return response()->json([
            'data' => $product,
            'mensaje' => 'Producto creado con éxito',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($value)
    {
        $id = null;
        $products = null;

        switch (true) {
            case is_numeric($value):
                // Buscamos por ID un producto de la db
                $id = Product::find($value);
                break;

            // Buscamos por la category un producto, si hay mas de un producto
            // con esta categoria nos muetras todas las coincidencias
            case is_string($value):
                $products = Product::where('category', $value)->get();

                // Buscar por product_name
                // $p = Product::where('product_name', $value)->first();

                // Si no se encontró por product_name, buscar por category
                // if (!$p) {
                //     $products = Product::where('category', $value)->get();
                // }
                break;

            default:
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Parámetro no válido. Se espera un ID numérico, un product_name o un category.',
                ]);
        }

        if ($id !== null) {
            return response()->json([
                'data' => $id,
                'mensaje' => 'Producto encontrado con éxito',
            ]);
        } elseif ($products !== null && $products->count() > 0) {
            return response()->json([
                'data' => $products,
                'mensaje' => 'Productos encontrados con éxito',
            ]);
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => 'No existen productos con el criterio de búsqueda proporcionado.',
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $p = Product::find($id);

        if (!$p) {
            return response()->json([
                'error' => true,
                'mensaje' => 'No existe el producto.',
            ]);
        }

        // Verificamos si se está cambiando el product_name
        if ($request->has('product_name') && $request->input('product_name') !== $p->product_name) {
            $newProductName = ucfirst($request->input('product_name'));

            // Verificamos si ya existe un producto con el nuevo product_name para que este no se pueda actualizar
            if (Product::where('product_name', $newProductName)->exists()) {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'Ya existe un producto con este nombre.',
                ]);
            }

            $p->product_name = $newProductName;
        }
        // Verificamos si se está cambiando la category
        if ($request->has('category') && $request->input('category') !== $p->category) {
            $newCategory = ucfirst($request->input('category'));

            $p->category = $newCategory;
        }

        // Actualizamos los demás campos o los dejamos como estan
        $p->product_image = $request->input('product_image');
        $p->supplier = $request->input('supplier');
        $p->stock = $request->input('stock');
        $p->cost = $request->input('cost');
        $p->price = $request->input('price');
        $p->description = $request->input('description');

        // Guardamos la informacion
        if ($p->save()) {
            return response()->json([
                'data' => $p,
                'mensaje' => 'Producto actualizado con éxito.',
            ]);
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => 'No se pudo actualizar el producto.',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Buscamos un producto por el id
        $p = Product::find($id);
        if (isset($p)) {
            $res = Product::destroy($id);
            if ($res) {
                return response()->json([
                    'data' => $p,
                    'mensaje' => 'Producto eliminado con exito.',
                ]);
            } else {
                return response()->json([
                    'data' => [],
                    'mensaje' => 'Producto no existe.',
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'mensaje' => 'No existe el producto.',
            ]);
        }
    }
}
