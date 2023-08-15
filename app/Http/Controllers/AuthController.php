<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Persona;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Candidato;
use App\Models\TipoCandidato;
use App\Models\Voto;


class AuthController extends Controller
{
    /**
 * @OA\Info(
 *    title="APIs For Thrift Store",
 *    version="1.0.0",
 * ),
 *   @OA\SecurityScheme(
 *       securityScheme="bearerAuth",
 *       in="header",
 *       name="bearerAuth",
 *       type="http",
 *       scheme="bearer",
 *       bearerFormat="JWT",
 *    ),
 */
/**
 * @OA\Post(
 *     path="/api/auth/register",
 *     summary="Crear un nuevo usuario",
 *     description="Crea un nuevo usuario con los datos proporcionados.",
 *     tags={"Usuarios"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="Nombre del usuario"),
 *             @OA\Property(property="email", type="string", example="usuario@example.com"),
 *             @OA\Property(property="password", type="string", example="contraseña")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Usuario creado exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Usuario creado exitosamente"),
 *             @OA\Property(property="token", type="string", example="Token de acceso generado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Campos inválidos o faltantes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Existen campos vacíos"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Mensaje de error")
 *         )
 *     )
 * )
 */
    public function createUser(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Existen campos vacios',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    /**
 * @OA\Post(
 *     path="/api/auth/login",
 *     summary="Iniciar sesión de usuario",
 *     description="Inicia sesión de usuario con las credenciales proporcionadas.",
 *     tags={"Usuarios"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="email", type="string", example="usuario@example.com"),
 *             @OA\Property(property="password", type="string", example="contraseña")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Inicio de sesión exitoso",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Inicio de sesión exitoso"),
 *             @OA\Property(property="token", type="string", example="Token de acceso generado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Credenciales inválidas o faltantes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Correo electrónico y contraseña no coinciden con nuestros registros")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Mensaje de error")
 *         )
 *     )
 * )
 */

    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    /**
 * @OA\Get(
 *     path="/api/auth/candidatos",
 *     summary="Listado de candidatos con información adicional",
 *     description="Muestra un listado de los candidatos con detalles sobre la lista y el tipo de candidato.",
 *     tags={"Candidatos"},
 *     @OA\Response(
 *         response=200,
 *         description="Listado de candidatos con información adicional",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="candidatos", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="descripcion", type="string", example="Descripción del candidato"),
 *                 @OA\Property(property="idlista", type="integer", example=1),
 *                 @OA\Property(property="lista", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="descripcion", type="string", example="Descripción de la lista"),
 *                     @OA\Property(property="numero", type="string", example="Número de la lista")
 *                 ),
 *                 @OA\Property(property="idtipocandidato", type="integer", example=1),
 *                 @OA\Property(property="tipo_candidato", type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="descripcion", type="string", example="Descripción del tipo de candidato"),
 *                     @OA\Property(property="icon", type="string", example="icono-del-tipo")
 *                 )
 *             ))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Mensaje de error")
 *         )
 *     )
 * )
 */
    public function listadoCandidatosConDetalles() {
        try {
            $candidatos = Candidato::with(['lista:id,descripcion,numero', 'tipoCandidato:id,descripcion,icon'])->get();
    
            $formattedCandidatos = $candidatos->map(function ($candidato) {
                return [
                    'id' => $candidato->id,
                    'descripcion' => $candidato->descripcion,
                    'idlista' => $candidato->idlista,
                    'lista' => $candidato->lista,
                    'idtipocandidato' => $candidato->idtipocandidato,
                    'tipo_candidato' => $candidato->tipoCandidato,
                ];
            });
    
            return response()->json([
                'candidatos' => $formattedCandidatos,
            ], 200);
    
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }


}