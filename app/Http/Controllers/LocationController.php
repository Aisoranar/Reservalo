<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\City;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Obtener todos los departamentos activos
     */
    public function getDepartments()
    {
        $departments = Department::active()->ordered()->get(['id', 'name', 'code']);
        
        return response()->json($departments);
    }

    /**
     * Obtener ciudades por departamento
     */
    public function getCitiesByDepartment(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id'
        ]);

        $cities = City::active()
            ->byDepartment($request->department_id)
            ->ordered()
            ->get(['id', 'name']);

        return response()->json($cities);
    }

    /**
     * Obtener ciudades por ID de departamento (para AJAX)
     */
    public function getCitiesByDepartmentId($departmentId)
    {
        $cities = City::active()
            ->where('department_id', $departmentId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($cities);
    }

    /**
     * Obtener todas las ciudades (para búsquedas)
     */
    public function getAllCities()
    {
        $cities = City::active()
            ->with('department:id,name')
            ->ordered()
            ->get(['id', 'name', 'department_id']);

        return response()->json($cities);
    }

    /**
     * Buscar ciudades por nombre
     */
    public function searchCities(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $cities = City::active()
            ->where('name', 'like', '%' . $request->query . '%')
            ->with('department:id,name')
            ->ordered()
            ->limit(20)
            ->get(['id', 'name', 'department_id']);

        return response()->json($cities);
    }
}
