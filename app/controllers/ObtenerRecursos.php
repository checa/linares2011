<?php
class ObtenerRecursos extends BaseController{
	
	// Función para obtener el nombre del club
	public static function obtenerClub(){
		$club = Club::first();

		return $club;
	}

	// Función para obtener todas las categorías
	public static function obtenerCategorias(){
		$categorias = Categoria::all();

		foreach ($categorias as $categoria) {
			foreach ($categoria->equipos as $key => $equipo) {
				if (!$equipo->club) {
					unset($categoria->equipos[$key]);
				}
			}
		}

		return $categorias;
	}

	// Función para obtener los datos para 'Clasificación'
	public static function obtenerDatosClasificacion($categoria){
		$liga = DB::table('equipos')->join('categorias', 'equipos.categoria_id', '=', 'categorias.id')
						->join('clubs', 'equipos.club_id', '=', 'clubs.id')
						->where('categorias.nombre', '=', $categoria)
						->select('liga_id')->get();

		$liga = DB::table('ligas')->join('equipos', 'ligas.id', '=', 'equipos.liga_id')
						->join('estadisticas', 'equipos.id', '=', 'estadisticas.equipo_id')
						->leftJoin('clubs', 'equipos.club_id', '=', 'clubs.id')
						->where('ligas.id', '=', $liga[0]->liga_id)
						->orderBy('estadisticas.puntos', 'Desc')
						->select('estadisticas.*', 'equipos.*', 'ligas.nombre as liga', 'clubs.nombre as club')->get();
						// var_dump($equipos);die();

		return $liga;
	}

	// Función para obtener los integrantes del equipo seleccionado
	public static function obtenerIntegrantes($equipo){
		$equipo = Equipo::whereSlug($equipo)->get()->first();

		if (!$equipo) {
			return false;
		}

		$integrantes = $equipo->integrantes;

		return $integrantes;
	}

	// Función para obtener los jugadores del equipo seleccionado
	public static function obtenerJugadores($integrantes){
		$jugadores = array();

		foreach ($integrantes as $key => $integrante) {
			if (!$integrante->es_tecnico) {
				$jugadores[] = $integrante;
			}
		}

		return $jugadores;
	}

	// Función para obtener los técnicos del equipo seleccionado
	public static function obtenerTecnicos($integrantes){
		$tecnicos = array();

		foreach ($integrantes as $key => $integrante) {
			if ($integrante->es_tecnico) {
				$tecnicos[] = $integrante;
			}
		}

		return $tecnicos;
	}

	// Función para obtener la categoria del equipo seleccionado
	public static function obtenerCategoria($equipo){
		$categoria = Equipo::whereSlug($equipo)->get()->first()->categoria->nombre;

		return $categoria;
	}
}