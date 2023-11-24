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

        $defaultImg = 'default.jpg';

        if(!isset($inputs['product_image'])) {
            $inputs['product_image'] = $defaultImg;
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
    public function show($id)
    {
        // Validación de entrada
        if (!is_numeric($id) || $id <= 0) {
            return response()->json(['error' => 'El ID debe ser un número entero positivo'], 400);
        }

        // Buscar producto por ID
        $product = Product::find($id);

        // Verifica si se encontró el producto
        if (!$product) {
            return response()->json(['error' => 'No se encontró un producto con el ID proporcionado'], 404);
        }

        return response()->json($product);
    }

    public function showCategory($category)
    {
        // Validación de entrada
        if (!is_string($category)) {
            return response()->json(['error' => 'El valor debe ser una cadena de texto'], 400);
        }

        // Filtra los productos por categoria
        $products = Product::where('category', $category)->get();

        // Verifica si se encontraron productos
        if ($products->isEmpty()) {
            return response()->json(['error' => 'No se encontraron productos con la categoria proporcionada'], 404);
        }

        return response()->json($products);
    }

    public function showRangePrice($inicio, $final)
    {
        // Validación de entrada
        if (!is_numeric($inicio) || !is_numeric($final)) {
            return response()->json(['error' => 'Los valores deben ser numéricos'], 400);
        }

        // Convierte los valores de inicio y final a números flotantes
        $inicio = floatval($inicio);
        $final = floatval($final);

        // Filtra los productos dentro del rango de precio
        $products = Product::whereBetween('price', [$inicio, $final])->get();

        if ($products->isEmpty()) {
            return response()->json(['error' => 'No se encontraron productos en el rango proporcionado'], 404);
        }

        return response()->json($products);
    }

    public function showNameProduct($productName)
    {
        // Validación de entrada
        if (!is_string($productName)) {
            return response()->json(['error' => 'El valor debe ser una cadena de texto'], 400);
        }

        // Filtra los productos por nombre de manera flexible
        $products = Product::where('product_name', 'LIKE', "%$productName%")->get();

        // Verifica si se encontraron productos
        if ($products->isEmpty()) {
            return response()->json(['error' => 'No se encontraron productos con el nombre proporcionado'], 404);
        }

        return response()->json($products);
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
